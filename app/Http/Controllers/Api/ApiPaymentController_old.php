<?php





namespace App\Http\Controllers\Api;





use App\Http\Controllers\Controller;



use App\Http\Controllers\PaymentApiController;



use App\Models\CcInfo;



use App\Models\MultiPaymentResponse;



use App\Models\Payment;



use App\Models\Invoice;



use App\Models\PaymentMethod;



use App\Models\PaymentMethodExpigate;



use Illuminate\Http\JsonResponse;



use Illuminate\Http\Request;



use Illuminate\Support\Facades\Config;



use Illuminate\Support\Facades\Route;



use Illuminate\Support\Facades\Validator;



use Illuminate\Support\Facades\Http;



use Illuminate\Support\Facades\Log;



use Illuminate\Support\Facades\DB;



use GuzzleHttp\Psr7\Response as GuzzleHttpResponse;



use Illuminate\Support\Str;





class ApiPaymentController extends Controller



{



    private function validate_request($request)



    {



        /** Defining rules to validate */



        $rules = [



            'invoice_id' => 'required|int',



            'card_name' => 'required|string|max:255',



            'card_type' => 'required|string',



            'card_number' => 'required|string|max:16|min:15',



            'card_exp_month' => 'required|string|max:2',



            'card_exp_year' => 'required|string|max:4',



            'card_cvv' => 'required|min:3',



        ];



        /** Defining rules message to show validation messages */



        $messages = [



            'invoice_id.required' => 'The Invoice number field is required.',



            'card_number.required' => 'The Card number field is required.',



            'card_number.min' => 'The Card number should not be less than 15 digits.',



            'card_exp_month.required' => 'The Expiry month field is required.',



            'card_exp_year.required' => 'The Expiry year field is required.',



            'card_cvv.required' => 'The CVV  number field is required.',



            'card_cvv.integer' => 'The CVV number must be in numbers.',



        ];



        /** Validating through Validator Library */



        return Validator::make($request->all(), $rules, $messages);





    }





    private function card_available($card_number, $cvv, $request, $merchant_id = 0, $merchant_type = 'Expigate')



    {



        if ($merchant_id == 0) {



            return false;



        }



//        $query = DB::table('cc_infos')



//            ->join('clients', 'cc_infos.client_id', '=', 'clients.id')



//            ->join('payments', 'payments.clientid', '=', 'clients.id')



//            ->select('*')



//            ->where('cc_infos.card_number', $card_number)



//            ->where('cc_infos.card_cvv', $cvv)



//            ->where('cc_infos.card_exp_month', $request->card_exp_month)



//            ->where('cc_infos.card_exp_year', $request->card_exp_year)



//            ->where('cc_infos.status', 1)



////            ->where('cc_infos.card_type', $request->card_type)



////            ->where('cc_infos.card_name', $request->card_name)



//            ->where('payments.created_at', '>', now()->subDays(30)->endOfDay())



//            ->where('payments.payment_gateway', 'Expigate')



//            ->where('payments.merchant_id', $merchant_id)



//            ->get();





        $query = DB::table('payments')

            ->join('cc_infos', 'cc_infos.invoice_id', '=', 'payments.invoice_id')

            ->select('*')

            ->where('cc_infos.card_number', $card_number)

            ->where('cc_infos.card_cvv', $cvv)

            ->where('cc_infos.card_exp_month', $request->card_exp_month)

            ->where('cc_infos.card_exp_year', $request->card_exp_year)



//            ->where('cc_infos.card_type', $request->card_type)



//            ->where('cc_infos.card_name', $request->card_name)



            ->where('payments.created_at', '>', now()->subDays(30)->endOfDay())

            ->where('payments.payment_gateway', $merchant_type)

            ->where('payments.merchant_id', $merchant_id)

            ->where('cc_infos.status', 1)

            ->get();



        return count($query) === 0;



    }





    public function failed_request($response, $statusCode = 500, $payment_gateway = null)



    {



        \Log::error('API request failed: ' . $statusCode);



        $errorMessage = $response['errors'] ?? ($response['error'] ?? $response);



        return response()->json(['errors' => $errorMessage, 'payment_gateway' => $payment_gateway], $statusCode);



    }





    private function response_array($response)



    {



        $response = json_decode($response->getContent(), true);



        if (isset($response['response']) && is_string($response['response'])) {



            $response['response'] = json_decode($response['response'], true);



        }



        $responseArray = [];



        if (isset($response['payment_gateway']) && ($response['payment_gateway'] === 'expigate' || $response['payment_gateway'] === 'amazon')) {



            if (isset($response['response']['responsetext'])) {



                $responseArray['payment_error'] = $response['response']['responsetext'];



            } elseif (isset($response['response']['response']['responsetext'])) {



                $responseArray['payment_error'] = $response['response']['response']['responsetext'];



            }



        }



        if (isset($response['response']) && is_array($response['response'])) {



            $responseData = $response['response'];



            $responseData['payment_gateway'] = $response['payment_gateway'];



            $responseData['payment_process_from'] = $response['payment_process_from'];



            foreach ($responseData as $key => $value) {



                $responseArray[$key] = $value;



            }



        } else {



            Log::error('API response is not in the expected JSON format');



            return $this->failed_request($response, 500, $response['payment_gateway'] ?? null);



        }



        return response()->json(['response' => $responseArray], 200);





    }





    private function payment_gateway($invoice, $request)



    {



        if (isset($invoice->getBrand) && isset($invoice->getBrand->getMerchant)) {



            $merchant_name = $invoice->getBrand->getMerchant->merchant;



        } else {



            $merchant_name = 'null';



        }



        $payment_process_from['authorize'] = [];



        $payment_process_from['expigate'] = [];



        $payment_process_from['amazon'] = [];



        $payment_process_from['payarc'] = [];



        $response = null;



        $payment_complete = 0;



        $inputs = $request->input();



        $inputs['payment_gateway'] = 'none';



        $inputs['process_from_mode'] = 1;



        $payment_gateway = 0;



        $card_available = false;



        $pkey = Config::get('app.privateKey');



        $card_number_enc = cxmEncrypt($request->card_number, $pkey);



        $cvv_enc = cxmEncrypt($request->card_cvv, $pkey);





        $ERROR_LIST = ['Activity limit exceeded', 'Pick up card - SF', 'Do Not Honor', 'DECLINE', 'Invalid Credit Card Number'];



        $IS_ERROR = $IS_AMAZON = false;



        $ERROR = $ERROR_MSG = null;





        $VISA_CARD = substr($request->card_number, 0, 1) === '4' || substr($request->card_number, 0, 1) === '5';





        // tgcrm





        // /** ================================================================================================================================================================================== */



        // /** ===================================> Amazon Kindle Direct Publisher (Capri) = 3 on Expigate Payment Method Start <================================================================ */



        // /** ================================================================================================================================================================================== */





        // /** => If payment not available on this card within 30 days and amount is less than 3000, payment should be on Amazon <= */



        // if ($payment_complete === 0 && isset($invoice->getBrand) && $invoice->getBrand->is_amazon == 1 



        // // && $invoice->getBrand->merchant_id == 2



        // ) {



        //     if ($IS_AMAZON == false) {



        //         $expigate_rules = ['zipcode' => 'required', 'city' => 'required', 'state' => 'required'];



        //         $expigate_validator = Validator::make($request->all(), $expigate_rules);



        //         if ($expigate_validator->fails()) {



        //             $errors = $expigate_validator->errors()->toArray();



        //             $formattedErrors = [];



        //             foreach ($errors as $field => $messages) {



        //                 foreach ($messages as $message) {



        //                     $formattedErrors[$field] = $message;



        //                 }



        //             }



        //             $payment_process_from['amazon']['Kindle_Direct_Publishers'] = ['errors' => $formattedErrors];



        //         } else {



        //             $amazon_id = 3;



        //             if ($VISA_CARD) {



        //                 if ($invoice->total_amount > 0 && $invoice->total_amount <= 3000) {



        //                     $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $amazon_id);



        //                     if ($card_available) {



        //                         if (PaymentMethodExpigate::isCapacityAvailable($amazon_id, $invoice->total_amount)) {



        //                             $api_url = route('api.expigate.payment');



        //                             $inputs['payment_gateway'] = 'amazon';



        //                             $inputs['merchant_id'] = $amazon_id;



        //                             $payment_gateway = 2;





        //                             $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



        //                             $IS_AMAZON = true;



        //                             $ERROR_MSG .= 'Amazon Kindle Direct Publishers Activated.';



        //                             if ($response->json() != null) {



        //                                 if ($response->json()['response'] && $response->json()['response']['response_code'] != '100') {



        //                                     $payment_process_from['amazon']['Kindle_Direct_Publishers'] = [$response->json()];



        //                                     if (isset($response->json()['response']['responsetext'])) {



        //                                         $amazon_responsetext = Str::contains($response->json()['response']['responsetext'], 'REFID') ? trim(Str::before($response->json()['response']['responsetext'], 'REFID:')) : $response->json()['response']['responsetext'];



        //                                         $errorMatches = array_intersect([$amazon_responsetext], $ERROR_LIST);



        //                                         if (!empty($errorMatches)) {



        //                                             $IS_ERROR = true;



        //                                             $ERROR = 'AMAZON - ' . reset($errorMatches);



        //                                         }



        //                                     } else {



        //                                         $payment_process_from['amazon']['Kindle_Direct_Publishers'] = ['error' => 'response text not found in amazon response.'];



        //                                     }



        //                                 } elseif ($response->failed() && $response->json()['response'] && $response->json()['response']['response_code'] != '100') {



        //                                     $failed_response = json_decode($this->failed_request($response->json(), 500, 'expigate')->getcontent(), true);



        //                                     $payment_process_from['amazon']['Kindle_Direct_Publishers'] = [$failed_response['errors']];



        //                                     if (isset($response->json()['response']['responsetext'])) {



        //                                         $amazon_responsetext = Str::contains($response->json()['response']['responsetext'], 'REFID') ? trim(Str::before($response->json()['response']['responsetext'], 'REFID:')) : $failed_response['errors'];



        //                                         $errorMatches = array_intersect([$amazon_responsetext], $ERROR_LIST);



        //                                         if (!empty($errorMatches)) {



        //                                             $IS_ERROR = true;



        //                                             $ERROR = 'AMAZON - ' . reset($errorMatches);



        //                                         }



        //                                     } else {



        //                                         $payment_process_from['amazon']['Kindle_Direct_Publishers'] = ['error' => 'response text not found in amazon response.'];



        //                                     }



        //                                 } elseif ($response->json()['response'] && $response->json()['response']['response_code'] == '100') {



        //                                     $payment_complete = 1;



        //                                     $payment_process_from['amazon']['Kindle_Direct_Publishers'] = [$response->json()];



        //                                 }



        //                             } else {



        //                                 $payment_process_from['amazon']['Kindle_Direct_Publishers'] = [$response->json()];



        //                             }



        //                         } else {



        //                             $payment_process_from['amazon']['Kindle_Direct_Publishers'] = ['error' => 'Status off / Capacity issue. ' . $amazon_id];



        //                         }



        //                     } else {



        //                         $payment_process_from['amazon']['Kindle_Direct_Publishers'] = ['error' => 'Card already exists. ' . $amazon_id];



        //                     }



        //                 } else {



        //                     $payment_process_from['amazon']['Kindle_Direct_Publishers'] = ['error' => 'Amount limit ' . $amazon_id];



        //                 }



        //             } else {



        //                 $payment_process_from['amazon']['Kindle_Direct_Publishers'] = ['error' => 'Not a visa card.'];



        //             }



        //         }



        //     } else {



        //         $payment_process_from['amazon']['Kindle_Direct_Publishers'] = ['error' => $ERROR_MSG];



        //     }



        // }





        // /** ================================================================================================================================================================================== */



        // /** ===================================> Amazon Kindle Direct Publisher (Capri) = 3 on Expigate Payment Method End <================================================================== */



        // /** ================================================================================================================================================================================== */





        // /** ********************************************************************************************************************************************************************************** */





        /** ================================================================================================================================================================================== */



        /** ===================================> Amazon KDP Publisher (Merchant E)= 4 on Expigate Payment Method Start <====================================================================== */



        /** ================================================================================================================================================================================== */





        /** => If payment not available on this card within 30 days and amount is less than 3000, payment should be on Amazon <= */



        if ($payment_complete === 0 && isset($invoice->getBrand) && $invoice->getBrand->is_amazon == 1



            // && $invoice->getBrand->merchant_id == 2



        ) {



            if ($IS_AMAZON == false) {



                $expigate_rules = ['zipcode' => 'required', 'city' => 'required', 'state' => 'required'];



                $expigate_validator = Validator::make($request->all(), $expigate_rules);



                if ($expigate_validator->fails()) {



                    $errors = $expigate_validator->errors()->toArray();



                    $formattedErrors = [];



                    foreach ($errors as $field => $messages) {



                        foreach ($messages as $message) {



                            $formattedErrors[$field] = $message;



                        }



                    }



                    $payment_process_from['amazon']['KDP_Publishers'] = ['errors' => $formattedErrors];



                } else {



                    $amazon_id = 4;



                    if ($VISA_CARD) {



                        if ($invoice->total_amount > 0 && $invoice->total_amount <= 3000) {



                            $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $amazon_id);



                            if ($card_available) {



                                if (PaymentMethodExpigate::isCapacityAvailable($amazon_id, $invoice->total_amount)) {



                                    $api_url = route('api.expigate.payment');



                                    $inputs['payment_gateway'] = 'amazon';



                                    $inputs['merchant_id'] = $amazon_id;



                                    $payment_gateway = 2;





                                    $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                                    $IS_AMAZON = true;



                                    $ERROR_MSG .= 'KDP Publisher Activated.';



                                    if ($response->json() != null) {



                                        if ($response->json()['response'] && $response->json()['response']['response_code'] != '100') {



                                            $payment_process_from['amazon']['KDP_Publishers'] = [$response->json()];



                                            if (isset($response->json()['response']['responsetext'])) {



                                                $amazon_responsetext = Str::contains($response->json()['response']['responsetext'], 'REFID') ? trim(Str::before($response->json()['response']['responsetext'], 'REFID:')) : $response->json()['response']['responsetext'];



                                                $errorMatches = array_intersect([$amazon_responsetext], $ERROR_LIST);



                                                if (!empty($errorMatches)) {



                                                    $IS_ERROR = true;



                                                    $ERROR = 'AMAZON - ' . reset($errorMatches);



                                                }



                                            } else {



                                                $payment_process_from['amazon']['KDP_Publishers'] = ['error' => 'response text not found in amazon response.'];



                                            }



                                        } elseif ($response->failed() && $response->json()['response'] && $response->json()['response']['response_code'] != '100') {



                                            $failed_response = json_decode($this->failed_request($response->json(), 500, 'expigate')->getcontent(), true);



                                            $payment_process_from['amazon']['KDP_Publishers'] = [$failed_response['errors']];



                                            if (isset($response->json()['response']['responsetext'])) {



                                                $amazon_responsetext = Str::contains($response->json()['response']['responsetext'], 'REFID') ? trim(Str::before($response->json()['response']['responsetext'], 'REFID:')) : $failed_response['errors'];



                                                $errorMatches = array_intersect([$amazon_responsetext], $ERROR_LIST);



                                                if (!empty($errorMatches)) {



                                                    $IS_ERROR = true;



                                                    $ERROR = 'AMAZON - ' . reset($errorMatches);



                                                }



                                            } else {



                                                $payment_process_from['amazon']['KDP_Publishers'] = ['error' => 'response text not found in amazon response.'];



                                            }



                                        } elseif ($response->json()['response'] && $response->json()['response']['response_code'] == '100') {



                                            $payment_complete = 1;



                                            $payment_process_from['amazon']['KDP_Publishers'] = [$response->json()];



                                        }



                                    } else {



                                        $payment_process_from['amazon']['KDP_Publishers'] = [$response->json()];



                                    }



                                } else {



                                    $payment_process_from['amazon']['KDP_Publishers'] = ['error' => 'Status off / Capacity issue. ' . $amazon_id];



                                }



                            } else {



                                $payment_process_from['amazon']['KDP_Publishers'] = ['error' => 'Card already exists. ' . $amazon_id];



                            }



                        } else {



                            $payment_process_from['amazon']['KDP_Publishers'] = ['error' => 'Amount limit' . $amazon_id];



                        }



                    } else {



                        $payment_process_from['amazon']['KDP_Publishers'] = ['error' => 'Not a visa card.'];



                    }



                }



            } else {



                $payment_process_from['amazon']['KDP_Publishers'] = ['error' => $ERROR_MSG];



            }



        }





        /** ================================================================================================================================================================================== */



        /** ===================================> Amazon KDP Publisher (Merchant E) = 4 on Expigate Payment Method End <======================================================================= */



        /** ================================================================================================================================================================================== */





        /** ****************************************************************************************************************************************************************************** */





        /** ============================================================================================================================================================================== */



        /** =====================================================> THE CREATIVE LABZ [9]  Start <========================================================================================= */



        /** ============================================================================================================================================================================== */





        if ($payment_complete === 0) {



            $authorize_name = str_replace(' ', '_', 'THE CREATIVE LABZ 9');



            if ($IS_AMAZON == false) {





                if (isset($invoice->getBrand)



                    // && $invoice->getBrand->merchant_id == 1



                ) {



                    $authorize_id = 9;



                    if ($invoice->total_amount > 0 && $invoice->total_amount <= 4000) {



                        if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                            $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $authorize_id, 'authorize');

                            if ($card_available) {



                                $api_url = route('api.authorize.payment');



                                $inputs['payment_gateway'] = 'authorize';



                                $inputs['merchant_id'] = $authorize_id;



                                $payment_gateway = 1;



                                $IS_AMAZON = true;



                                $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                                if ($response->json() != null) {



                                    if (!$response->failed()) {



                                        $payment_complete = 1;



                                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                                    } elseif ($response->failed()) {



                                        $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                                        $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];



                                    } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                                    }



                                } else {



                                    $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                                }



                            } else {



                                $payment_process_from['authorize'][$authorize_name] = ['error' => 'Card Exits' . $authorize_id];



                            }





                        } else {



                            $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                        }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



                    }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'the CREATIVE is temp disabled design only'];



                }



            } else {



                $payment_process_from['authorize'][$authorize_name] = ['error' => $ERROR_MSG ?? "Amazon condition"];



            }



        }





        /** ============================================================================================================================================================================== */



        /** =====================================================> THE CREATIVE LABZ[9] End <============================================================================================= */



        /** ============================================================================================================================================================================== */





        /** ****************************************************************************************************************************************************************************** */





        /** ============================================================================================================================================================================== */



        /** =====================================================> Design Curvature 12 Start <====================================================================================== */



        /** ============================================================================================================================================================================== */





//                $authorize_name = str_replace(' ', '_', $merchant_name);



        $authorize_name = str_replace(' ', '_', 'Design Curvature 1');



        if ($IS_AMAZON == false) {





            if (isset($invoice->getBrand)



            ) {



                $authorize_id = 12;



                if ($invoice->total_amount > 0 && $invoice->total_amount <= 3000) {



                    if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                        $api_url = route('api.authorize.payment');



                        $inputs['payment_gateway'] = 'authorize';



                        $inputs['merchant_id'] = 12;



                        $payment_gateway = 1;



                        $IS_AMAZON = true;



                        $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                        if ($response->json() != null) {



                            if (!$response->failed()) {



                                $payment_complete = 1;



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            } elseif ($response->failed()) {



                                $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                                $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];



                            } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            }



                        } else {



                            $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                        }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                    }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



                }



            } else {



                $payment_process_from['authorize'][$authorize_name] = ['error' => 'Easy Writing is temp disabled'];



            }



        } else {



            $payment_process_from['authorize'][$authorize_name] = ['error' => $ERROR_MSG ?? "Amazon condition"];



        }





        /** ============================================================================================================================================================================== */



        /** =====================================================> Design Curvature 12 End <======================================================================================== */



        /** ============================================================================================================================================================================== */





        /** ****************************************************************************************************************************************************************************** */





        /** ============================================================================================================================================================================== */



        /** =====================================================> Creative Jenie [13]  Start <========================================================================================= */



        /** ============================================================================================================================================================================== */





        if ($payment_complete === 0) {



            $authorize_name = str_replace(' ', '_', 'Creative Jenie 13');



            if ($IS_AMAZON == false) {





                if (isset($invoice->getBrand)



                    // && $invoice->getBrand->merchant_id == 1



                ) {



                    $authorize_id = 13;



                    if ($invoice->total_amount > 0 && $invoice->total_amount <= 3000) {



                        if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                            $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $authorize_id, 'authorize');

                            if ($card_available) {



                                $api_url = route('api.authorize.payment');



                                $inputs['payment_gateway'] = 'authorize';



                                $inputs['merchant_id'] = $authorize_id;



                                $payment_gateway = 1;



                                $IS_AMAZON = true;



                                $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                                if ($response->json() != null) {



                                    if (!$response->failed()) {



                                        $payment_complete = 1;



                                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                                    } elseif ($response->failed()) {



                                        $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                                        $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];



                                    } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                                    }



                                } else {



                                    $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                                }



                            } else {



                                $payment_process_from['authorize'][$authorize_name] = ['error' => 'Card Exits' . $authorize_id];



                            }





                        } else {



                            $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                        }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



                    }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'the CREATIVE is temp disabled design only'];



                }



            } else {



                $payment_process_from['authorize'][$authorize_name] = ['error' => $ERROR_MSG ?? "Amazon condition"];



            }



        }





        /** ============================================================================================================================================================================== */



        /** =====================================================> Creative Jenie [13] End <============================================================================================= */



        /** ============================================================================================================================================================================== */





        /** ================================================================================================================================================================================== */



        /** ===================================> Creative Labz  = 5 on Expigate Payment Method Start <================================================================ */



        /** ================================================================================================================================================================================== */





        /** => If payment not available on this card within 30 days and amount is less than 3000, payment should be on Amazon <= */



        if ($payment_complete === 0 && isset($invoice->getBrand)



            // && $invoice->getBrand->merchant_id == 1



        ) {



            if ($IS_AMAZON == false) {



                $expigate_rules = ['zipcode' => 'required', 'city' => 'required', 'state' => 'required'];



                $expigate_validator = Validator::make($request->all(), $expigate_rules);



                if ($expigate_validator->fails()) {



                    $errors = $expigate_validator->errors()->toArray();



                    $formattedErrors = [];



                    foreach ($errors as $field => $messages) {



                        foreach ($messages as $message) {



                            $formattedErrors[$field] = $message;



                        }



                    }



                    $payment_process_from['amazon']['creative_labz'] = ['errors' => $formattedErrors];



                } else {



                    $amazon_id = 5;



                    // if ($VISA_CARD) {



                    if ($invoice->total_amount > 0 && $invoice->total_amount <= 3000) {



                        $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $amazon_id);



                        if ($card_available) {



                            if (PaymentMethodExpigate::isCapacityAvailable($amazon_id, $invoice->total_amount)) {



                                $api_url = route('api.expigate.payment');



                                $inputs['payment_gateway'] = 'amazon';



                                $inputs['merchant_id'] = $amazon_id;



                                $payment_gateway = 2;





                                $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                                $IS_AMAZON = true;



                                $ERROR_MSG .= 'Amazon Kindle Direct Publishers Activated.';



                                if ($response->json() != null) {



                                    if ($response->json()['response'] && $response->json()['response']['response_code'] != '100') {



                                        $payment_process_from['amazon']['creative_labz'] = [$response->json()];



                                        if (isset($response->json()['response']['responsetext'])) {



                                            $amazon_responsetext = Str::contains($response->json()['response']['responsetext'], 'REFID') ? trim(Str::before($response->json()['response']['responsetext'], 'REFID:')) : $response->json()['response']['responsetext'];



                                            $errorMatches = array_intersect([$amazon_responsetext], $ERROR_LIST);



                                            if (!empty($errorMatches)) {



                                                $IS_ERROR = true;



                                                $ERROR = 'AMAZON - ' . reset($errorMatches);



                                            }



                                        } else {



                                            $payment_process_from['amazon']['creative_labz'] = ['error' => 'response text not found in amazon response.'];



                                        }



                                    } elseif ($response->failed() && $response->json()['response'] && $response->json()['response']['response_code'] != '100') {



                                        $failed_response = json_decode($this->failed_request($response->json(), 500, 'expigate')->getcontent(), true);



                                        $payment_process_from['amazon']['creative_labz'] = [$failed_response['errors']];



                                        if (isset($response->json()['response']['responsetext'])) {



                                            $amazon_responsetext = Str::contains($response->json()['response']['responsetext'], 'REFID') ? trim(Str::before($response->json()['response']['responsetext'], 'REFID:')) : $failed_response['errors'];



                                            $errorMatches = array_intersect([$amazon_responsetext], $ERROR_LIST);



                                            if (!empty($errorMatches)) {



                                                $IS_ERROR = true;



                                                $ERROR = 'AMAZON - ' . reset($errorMatches);



                                            }



                                        } else {



                                            $payment_process_from['amazon']['creative_labz'] = ['error' => 'response text not found in amazon response.'];



                                        }



                                    } elseif ($response->json()['response'] && $response->json()['response']['response_code'] == '100') {



                                        $payment_complete = 1;



                                        $payment_process_from['amazon']['creative_labz'] = [$response->json()];



                                    }



                                } else {



                                    $payment_process_from['amazon']['creative_labz'] = [$response->json()];



                                }



                            } else {



                                $payment_process_from['amazon']['creative_labz'] = ['error' => 'Status off / Capacity issue. ' . $amazon_id];



                            }



                        } else {



                            $payment_process_from['amazon']['creative_labz'] = ['error' => 'Card already exists. ' . $amazon_id];



                        }



                    } else {



                        $payment_process_from['amazon']['creative_labz'] = ['error' => 'Amount limit ' . $amazon_id];



                    }



                }



            } else {



                $payment_process_from['amazon']['creative_labz'] = ['error' => $ERROR_MSG];



            }



        }





        /** ================================================================================================================================================================================== */



        /** ===================================> Creative Labz = 5 on Expigate Payment Method End <================================================================== */



        /** ================================================================================================================================================================================== */





        /** ****************************************************************************************************************************************************************************** */





        /** ============================================================================================================================================================================== */



        /** =====================================================> CREATIVE SOLUTIONS AGENCY Start <====================================================================================== */



        /** ============================================================================================================================================================================== */





//                $authorize_name = str_replace(' ', '_', $merchant_name);



        $authorize_name = str_replace(' ', '_', 'CREATIVE SOLUTIONS AGENCY');



        if ($IS_AMAZON == false) {





            if (isset($invoice->getBrand)) {



                $authorize_id = 1;



                if ($invoice->total_amount > 0 && $invoice->total_amount <= 3000) {



                    if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                        $api_url = route('api.authorize.payment');



                        $inputs['payment_gateway'] = 'authorize';



                        $inputs['merchant_id'] = 1;



                        $payment_gateway = 1;



                        $IS_AMAZON = true;



                        $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                        if ($response->json() != null) {



                            if (!$response->failed()) {



                                $payment_complete = 1;



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            } elseif ($response->failed()) {



                                $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                                $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];



                            } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            }



                        } else {



                            $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                        }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                    }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



                }



            } else {



                $payment_process_from['authorize'][$authorize_name] = ['error' => 'Easy Writing is temp disabled'];



            }



        } else {



            $payment_process_from['authorize'][$authorize_name] = ['error' => $ERROR_MSG ?? "Amazon condition"];



        }





        /** ============================================================================================================================================================================== */



        /** =====================================================> CREATIVE SOLUTIONS AGENCY End <======================================================================================== */



        /** ============================================================================================================================================================================== */





        /** ********************************************************************************************************************************************************************************** */





        /** ================================================================================================================================================================================== */



        /** ====================================================> PayArc Payment Method Start <=============================================================================================== */



        /** ================================================================================================================================================================================== */





//        $payment_process_from['payarc'] = [$invoice->total_amount > 0 , $invoice->total_amount < 3000 , isset($invoice->getBrand) , $invoice->getBrand->merchant_id == 2 , $invoice->getBrand->expigate_id == 2];



//        if ($invoice->total_amount > 0 && $invoice->total_amount <= 1999 && isset($invoice->getBrand) && $invoice->getBrand->merchant_id == 2 && $invoice->getBrand->expigate_id == 2) {



//            $payarc_rules = ['zipcode' => 'required'];



//            $payarc_validator = Validator::make($request->all(), $payarc_rules);



//            if ($payarc_validator->fails()) {



//                $errors = $payarc_validator->errors()->toArray();



//                $formattedErrors = [];



//                foreach ($errors as $field => $messages) {



//                    foreach ($messages as $message) {



//                        $formattedErrors[$field] = $message;



//                    }



//                }



//                $payment_process_from['payarc'] = ['errors' => $formattedErrors];



//            } else {



//                $api_url = route('api.payarc.payment');



//                $inputs['payment_gateway'] = 'payarc';



//                $payment_gateway = 3;





//                $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



//                if ($response->json() != null) {



//                    if (!$response->failed()) {



//                        $payment_complete = 1;



//                        $payment_process_from['payarc'] = [$response->json()];



//                    } elseif ($response->failed()) {



//                        $failed_response = json_decode($this->failed_request($response->json(), 500, 'payarc')->getcontent(), true);



//                        $payment_process_from['payarc'] = [$failed_response['errors']];



//                    }



//                } else {



//                    $payment_process_from['payarc'] = [$response->json()];



//                }



//            }



//        } elseif (isset($invoice->getBrand) && $invoice->getBrand->merchant_id != 2) {



//            $payment_process_from['payarc'] = ['error' => 'Please check your brand'];



//        }



//        /** => PayArc Payment Method End <= */





        /** ================================================================================================================================================================================== */



        /** ====================================================> PayArc Payment Method End <================================================================================================= */



        /** ================================================================================================================================================================================== */





        /** ********************************************************************************************************************************************************************************** */





        /** ================================================================================================================================================================================== */



        /** ====================================================> Expigate Payment Method Start <============================================================================================= */



        /** ================================================================================================================================================================================== */





        /** => If payment not available on this card within 30 days and amount is less than 3000, payment should be on expigate <= */





//            if ($payment_complete === 0 && isset($invoice->getBrand) && $invoice->getBrand->merchant_id == 2) {



//                if (isset($invoice->getBrand->getMerchantExpigate->name)) {



//                    $expigate_id = $invoice->getBrand->expigate_id;



//                    $expigate_name = str_replace(' ', '_', $invoice->getBrand->getMerchantExpigate->merchant);



//                    if ($IS_AMAZON == false) {



//                        $expigate_rules = ['zipcode' => 'required', 'city' => 'required', 'state' => 'required'];



//                        $expigate_validator = Validator::make($request->all(), $expigate_rules);



//                        if ($expigate_validator->fails()) {



//                            $errors = $expigate_validator->errors()->toArray();



//                            $formattedErrors = [];



//                            foreach ($errors as $field => $messages) {



//                                foreach ($messages as $message) {



//                                    $formattedErrors[$field] = $message;



//                                }



//                            }



//                            $payment_process_from['expigate'] = ['errors' => $formattedErrors];



//                        } else {



//                            if ($IS_ERROR === false) {



//                                if ($invoice->getBrand->getMerchantExpigate->name === 'Expigate') {



//                                    if (PaymentMethodExpigate::isCapacityAvailable($expigate_id, $invoice->total_amount)) {



//                                        if ($invoice->total_amount > 0 && $invoice->total_amount <= 3000) {



//                                            $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $expigate_id);



//                                            if ($card_available) {



//                                                $api_url = route('api.expigate.payment');



//                                                $inputs['payment_gateway'] = 'expigate';



//                                                $inputs['merchant_id'] = $expigate_id;



//                                                $payment_gateway = 2;



//



//                                                $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



//                                                if ($response->json() != null) {



//                                                    if ($response->json()['response'] && $response->json()['response']['response_code'] != '100') {



//                                                        $payment_process_from['expigate'][$expigate_name] = [$response->json()];



//                                                    } elseif ($response->failed() && $response->json()['response'] && $response->json()['response']['response_code'] != '100') {



//                                                        $failed_response = json_decode($this->failed_request($response->json(), 500, 'expigate')->getcontent(), true);



//                                                        $payment_process_from['expigate'][$expigate_name] = [$failed_response['errors']];



//                                                    } elseif ($response->json()['response'] && $response->json()['response']['response_code'] == '100') {



//                                                        $payment_complete = 1;



//                                                        $payment_process_from['expigate'][$expigate_name] = [$response->json()];



//                                                    }



//                                                } else {



//                                                    $payment_process_from['expigate'][$expigate_name] = [$response->json()];



//                                                }



//                                            } else {



//                                                $payment_process_from['expigate'][$expigate_name] = ['error' => 'Card already exists. ' . $expigate_id];



//                                            }



//                                        } else {



//                                            $payment_process_from['expigate'][$expigate_name] = ['error' => 'Amount limit ' . $expigate_id];



//                                        }



//                                    } else {



//                                        $payment_process_from['expigate'][$expigate_name] = ['error' => 'Status off / Capacity issue. ' . $expigate_id];



//                                    }



//                                } else {



//                                    $payment_process_from['expigate'][$expigate_name] = ['error' => 'Payment method is not Expigate.'];



//                                }



//                            } else if ($ERROR) {



//                                $payment_process_from['expigate'][$expigate_name] = ['error' => $ERROR];



//                            }



//                        }



//                    } else {



//                        $payment_process_from['expigate'][$expigate_name] = ['error' => $ERROR_MSG];



//                    }



//                } else {



//                    $payment_process_from['expigate'] = ['error' => 'Payment method is not Defined.'];



//                }



//            }



        /** ================================================================================================================================================================================== */



        /** ==================================================> Expigate Payment Method End <================================================================================================= */



        /** ================================================================================================================================================================================== */





        /** ********************************************************************************************************************************************************************************** */





        /** ************************************************************************************************************************************************************************** */





        /** ========================================================================================================================================================================== */



        /** =====================================================> The Creative Labz Start 8 <======================================================================================== */



        /** ========================================================================================================================================================================== */





        /** => The Creative Labz Payment Method start <= */



        if (!$IS_AMAZON && $payment_complete === 0 && isset($invoice->getBrand)



            // && $invoice->getBrand->merchant_id == 1



        ) {



            $authorize_id = 8;



            $authorize_name = str_replace(' ', '_', 'The Creative Labz');



            if ($invoice->total_amount > 0 && $invoice->total_amount <= 4000) {



                if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                    $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $authorize_id, 'authorize');



                    // if ($card_available) {



                    $api_url = route('api.authorize.payment');



                    $inputs['payment_gateway'] = 'authorize';



                    $inputs['merchant_id'] = $authorize_id;



                    $payment_gateway = 1;



                    $IS_AMAZON = true;



                    $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);





                    if ($response->json() != null) {



                        if (!$response->failed() && $response->json()['resultCode'] == "Ok" && in_array($response->json()['t_resp']['responseCode'], [1, 4])) {



                            $payment_complete = 1;



                            $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                        } elseif ($response->failed()) {



                            $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                            $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];





                        } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                            $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                        }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                    }



                    // } else {



                    //     $payment_process_from['authorize'][$authorize_name] = ['error' => 'Card already exists. ' . $authorize_id];



                    // }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                }



            } else {



                $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



            }



        }





        /** => The Creative Labz Payment Method End <= */





        /** ========================================================================================================================================================================== */



        /** =====================================================> The Creative Labz 8 End <========================================================================================== */



        /** ========================================================================================================================================================================== */





        /** ========================================================================================================================================================================== */



        /** =====================================================> The Creativ Agency Start 11 <======================================================================================== */



        /** ========================================================================================================================================================================== */





        /** => The Creative Labz Payment Method start <= */



        if (!$IS_AMAZON && $payment_complete === 0 && isset($invoice->getBrand)



            // && $invoice->getBrand->merchant_id == 1



        ) {



            $authorize_id = 11;



            $authorize_name = str_replace(' ', '_', 'The Creative Labz');



            if ($invoice->total_amount > 0 && $invoice->total_amount <= 4000) {



                if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                    $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $authorize_id, 'authorize');



                    // if ($card_available) {



                    $api_url = route('api.authorize.payment');



                    $inputs['payment_gateway'] = 'authorize';



                    $inputs['merchant_id'] = $authorize_id;



                    $payment_gateway = 1;



                    $IS_AMAZON = true;



                    $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);





                    if ($response->json() != null) {



                        if (!$response->failed() && $response->json()['resultCode'] == "Ok" && in_array($response->json()['t_resp']['responseCode'], [1, 4])) {



                            $payment_complete = 1;



                            $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                        } elseif ($response->failed()) {



                            $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                            $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];





                        } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                            $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                        }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                    }



                    // } else {



                    //     $payment_process_from['authorize'][$authorize_name] = ['error' => 'Card already exists. ' . $authorize_id];



                    // }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                }



            } else {



                $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



            }



        }





        /** => The Creative Labz Payment Method End <= */





        /** ========================================================================================================================================================================== */



        /** =====================================================> The Creativ Agency 11 End <========================================================================================== */



        /** ========================================================================================================================================================================== */





        /** ================================================================================================================================================================================== */



        /** ===========================================================> Authorize <========================================================================================================== */



        /** =====================================================> Payment Method Starts <==================================================================================================== */



        /** ================================================================================================================================================================================== */





        /** => If payment not process from amazon <= */





        if (!$IS_AMAZON) {





            /** ========================================================================================================================================================================== */



            /** =====================================================> Design Curvature [10] Start <====================================================================================== */



            /** ========================================================================================================================================================================== */





            /** => Design Curvature [10] Payment Method start <= */



            if (!$IS_AMAZON && $payment_complete === 0 && isset($invoice->getBrand) && $invoice->getBrand->merchant_id == 1) {



                $authorize_id = 10;



                $authorize_name = str_replace(' ', '_', 'Design Curvature LLC');



                if ($invoice->total_amount > 0 && $invoice->total_amount <= 1999) {



                    if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                        $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $authorize_id, 'authorize');



                        if ($card_available) {



                            $api_url = route('api.authorize.payment');



                            $inputs['payment_gateway'] = 'authorize';



                            $inputs['merchant_id'] = $authorize_id;



                            $payment_gateway = 1;



                            $IS_AMAZON = true;



                            $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                            if ($response->json() != null) {



                                if (!$response->failed() && $response->json()['resultCode'] == "Ok" && in_array($response->json()['t_resp']['responseCode'], [1, 4])) {



                                    $payment_complete = 1;



                                    $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                                } elseif ($response->failed()) {



                                    $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                                    $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];





                                } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                                    $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                                }



                            } else {



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            }



                        } else {



                            $payment_process_from['authorize'][$authorize_name] = ['error' => 'Card already exists. ' . $authorize_id];



                        }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                    }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



                }



            }



            /** => Design Curvature [10] Payment Method End <= */





            /** ========================================================================================================================================================================== */



            /** =====================================================> Design Curvature [10] End <======================================================================================== */



            /** ========================================================================================================================================================================== */





            /** ========================================================================================================================================================================== */



            /** =====================================================> Times Ghost Writer Start <========================================================================================= */



            /** ========================================================================================================================================================================== */





            /** => Times Ghost Writer Payment Method start <= */



            // if ($payment_complete === 0 && isset($invoice->getBrand) && $invoice->getBrand->merchant_id == 2) {



            //     $authorize_id = 4;



            //     $authorize_name = str_replace(' ', '_', 'Times Ghost Writer');



            //     if ($invoice->total_amount > 0 && $invoice->total_amount <= 1999) {



            //         if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



            //             $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $authorize_id, 'authorize');



            //             if ($card_available) {



            //                 $api_url = route('api.authorize.payment');



            //                 $inputs['payment_gateway'] = 'authorize';



            //                 $inputs['merchant_id'] = $authorize_id;



            //                 $payment_gateway = 1;





            //                 $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



            //                 if ($response->json() != null) {



            //                     if (!$response->failed() && $response->json()['resultCode'] == "Ok" && in_array($response->json()['t_resp']['responseCode'], [1, 4])) {



            //                         $payment_complete = 1;



            //                         $payment_process_from['authorize'][$authorize_name] = [$response->json()];



            //                     } elseif ($response->failed()) {



            //                         $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



            //                         $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];





            //                     } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



            //                         $payment_process_from['authorize'][$authorize_name] = [$response->json()];



            //                     }



            //                 } else {



            //                     $payment_process_from['authorize'][$authorize_name] = [$response->json()];



            //                 }



            //             } else {



            //                 $payment_process_from['authorize'][$authorize_name] = ['error' => 'Card already exists. ' . $authorize_id];



            //             }



            //         } else {



            //             $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



            //         }



            //     } else {



            //         $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



            //     }



            // }



            /** => Times Ghost Writer Payment Method End <= */





            /** ========================================================================================================================================================================== */



            /** =====================================================> Times Ghost Writer End <=========================================================================================== */



            /** ========================================================================================================================================================================== */





            /** ========================================================================================================================================================================== */



            /** =====================================================> Techno Boxer LLC Start <=========================================================================================== */



            /** ========================================================================================================================================================================== */





            /** => Techno Boxer LLC Payment Method start <= */



            if (!$IS_AMAZON && $payment_complete === 0 && isset($invoice->getBrand)) {



                $authorize_id = 7;



                $authorize_name = str_replace(' ', '_', 'Techno Boxer LLC');



                if ($invoice->total_amount > 0 && $invoice->total_amount <= 2000) {



                    if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                        // $card_available = $this->card_available($card_number_enc, $cvv_enc, $request, $authorize_id, 'authorize');



                        // if ($card_available) {



                        $api_url = route('api.authorize.payment');



                        $inputs['payment_gateway'] = 'authorize';



                        $inputs['merchant_id'] = $authorize_id;



                        $payment_gateway = 1;



                        $IS_AMAZON = true;



                        $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                        if ($response->json() != null) {



                            if (!$response->failed() && $response->json()['resultCode'] == "Ok" && in_array($response->json()['t_resp']['responseCode'], [1, 4])) {



                                $payment_complete = 1;



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            } elseif ($response->failed()) {



                                $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                                $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];





                            } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            }



                        } else {



                            $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                        }



                        // } else {



                        //     $payment_process_from['authorize'][$authorize_name] = ['error' => 'Card already exists. ' . $authorize_id];



                        // }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                    }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



                }



            }



            /** => Techno Boxer LLC Payment Method End <= */





            /** ========================================================================================================================================================================== */



            /** =====================================================> Techno Boxer LLC End <============================================================================================= */



            /** ========================================================================================================================================================================== */





            /** ************************************************************************************************************************************************************************** */





            /** ========================================================================================================================================================================== */



            /** =====================================================> Get Writing Service Start <======================================================================================== */



            /** =========================================================> Upsale & Fresh <=============================================================================================== */



            /** ========================================================================================================================================================================== */





            //   if ($payment_complete === 0 && isset($invoice->getBrand) && $invoice->getBrand->merchant_id == 2) {



            //          $authorize_id = 6;



            //          $authorize_name = str_replace(' ', '_', 'Get Writing Service');



            //          if ($invoice->total_amount > 0 && $invoice->total_amount <= 5000) {



            //           /** => Get writing disabled to Allan Team <= */



            //               if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



            //                   $api_url = route('api.authorize.payment');



            //                   $inputs['payment_gateway'] = 'authorize';



            //                   $inputs['merchant_id'] = $authorize_id;



            //                   $payment_gateway = 1;



            //                   $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



            //                   if ($response->json() != null) {



            //                       if (!$response->failed() && $response->json()['resultCode'] == "Ok" && in_array($response->json()['t_resp']['responseCode'], [1, 4])) {



            //                           $payment_complete = 1;



            //                           $payment_process_from['authorize'][$authorize_name] = [$response->json()];



            //                       } elseif ($response->failed()) {



            //                           $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



            //                           $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];



            //                       } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



            //                           $payment_process_from['authorize'][$authorize_name] = [$response->json()];



            //                       }



            //                   } else {



            //                       $payment_process_from['authorize'][$authorize_name] = [$response->json()];



            //                   }



            //               } else {



            //                   $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



            //               }





            //       } else {



            //           $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



            //       }



            //   }



            /** ========================================================================================================================================================================== */



            /** =====================================================> Get Writing Service End <========================================================================================== */



            /** =========================================================> Upsale & Fresh <=============================================================================================== */



            /** ========================================================================================================================================================================== */





            /** ************************************************************************************************************************************************************************** */





            /** ========================================================================================================================================================================== */



            /** =====================================================> Get Writing Service Start <======================================================================================== */



            /** =========================================================> Fresh Only <=================================================================================================== */



            /** ========================================================================================================================================================================== */





//                else if ($invoice->sales_type == 'Fresh' &&



//                    $payment_complete === 0 && isset($invoice->getBrand) &&



//                    $invoice->getBrand->merchant_id == 2 &&



//                    $invoice->total_amount <= 4999



//                ) {



//                    $authorize_id = 6;



//                    $authorize_name = str_replace(' ', '_', 'Get Writing Service');



//                    if ($invoice->total_amount > 0 && $invoice->total_amount <= 4999) {



//                        /** => Get writing disabled to Allan Team <= */



//                        if ($invoice->team_key != 770877) {



//                            if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



//



//                                $api_url = route('api.authorize.payment');



//                                $inputs['payment_gateway'] = 'authorize';



//                                $inputs['merchant_id'] = $authorize_id;



//                                $payment_gateway = 1;



//                                $response = Http::post(route('api.authorize.payment'), $inputs);



//                                if ($response->json() != null) {



//                                    if (!$response->failed() && $response->json()['resultCode'] == "Ok" && in_array($response->json()['t_resp']['responseCode'], [1, 4])) {



//                                        $payment_complete = 1;



//                                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



//                                    } elseif ($response->failed()) {



//                                        $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



//                                        $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];



//                                    } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



//                                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



//                                    }



//                                } else {



//                                    $payment_process_from['authorize'][$authorize_name] = [$response->json()];



//                                }



//                            } else {



//                                $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



//                            }



//                        } else {



//                            $payment_process_from['authorize'][$authorize_name] = ['error' => 'Team not allowed. 770877 '];



//                        }



//                    } else {



//                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



//                    }



//                }



        } else {



            $payment_process_from['authorize'][str_replace(' ', '_', 'Authorize Merchants')] = ['error' => $ERROR_MSG];



        }





        /** ============================================================================================================================================================================== */



        /** =====================================================> Get Writing Service End <============================================================================================== */



        /** =========================================================> Fresh Only <======================================================================================================= */



        /** ============================================================================================================================================================================== */





        /** ****************************************************************************************************************************************************************************** */



//



//            /** ============================================================================================================================================================================== */



//            /** =====================================================> Design Curvature AGENCY Start <======================================================================================== */



//            /** ============================================================================================================================================================================== */



//



//            if ($payment_complete === 0) {



//                $authorize_name = str_replace(' ', '_', 'Design Curvature');



//                if ($IS_AMAZON == false) {



//



//                    if (isset($invoice->getBrand)



//                        // && $invoice->getBrand->merchant_id == 1



//                    ) {



//                        $authorize_id = 3;



//                        if ($invoice->total_amount > 0 && $invoice->total_amount <= 3000) {



//                            if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



//                                $api_url = route('api.authorize.payment');



//                                $inputs['payment_gateway'] = 'authorize';



//                                $inputs['merchant_id'] = $authorize_id;



//                                $payment_gateway = 1;



//                                $IS_AMAZON = true;



//                                $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



//                                if ($response->json() != null) {



//                                    if (!$response->failed()) {



//                                        $payment_complete = 1;



//                                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



//                                    } elseif ($response->failed()) {



//                                        $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



//                                        $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];



//                                    } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



//                                        $payment_process_from['authorize'][$authorize_name] = [$response->json()];



//                                    }



//                                } else {



//                                    $payment_process_from['authorize'][$authorize_name] = [$response->json()];



//                                }



//                            } else {



//                                $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



//                            }



//                        } else {



//                            $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



//                        }



//                    } else {



//                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Easy Writing is temp disabled'];



//                    }



//                } else {



//                    $payment_process_from['authorize'][$authorize_name] = ['error' => $ERROR_MSG??"Amazon condition"];



//                }



//            }



//



//            /** ============================================================================================================================================================================== */



//            /** =====================================================> Design Curvature AGENCY End <========================================================================================== */



//            /** ============================================================================================================================================================================== */





        /** ****************************************************************************************************************************************************************************** */





        /** ============================================================================================================================================================================== */



        /** =====================================================> Creative Agency Start 2 <============================================================================================== */



        /** ============================================================================================================================================================================== */





        if ($payment_complete === 0) {



//                $authorize_name = str_replace(' ', '_', $merchant_name);



            $authorize_name = str_replace(' ', '_', 'Creative  AGENCY');



            if ($IS_AMAZON == false) {





                $authorize_id = 2;



                if ($invoice->total_amount > 0 && $invoice->total_amount <= 4500) {



                    if (PaymentMethod::isCapacityAvailable($authorize_id, $invoice->total_amount)) {



                        $api_url = route('api.authorize.payment');



                        $inputs['payment_gateway'] = 'authorize';



                        $inputs['merchant_id'] = 2;



                        $payment_gateway = 1;



                        $IS_AMAZON = true;



                        $response = Http::withHeaders(['X-Source' => $request->url()])->post($api_url, $inputs);



                        if ($response->json() != null) {



                            if (!$response->failed()) {



                                $payment_complete = 1;



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            } elseif ($response->failed()) {



                                $failed_response = json_decode($this->failed_request($response->json(), 500, 'authorize')->getcontent(), true);



                                $payment_process_from['authorize'][$authorize_name] = [$failed_response['errors']];



                            } elseif ($response->json()['resp'] == null || $response->json()['t_resp'] == null || $response->json()['resultCode'] != "Ok" || $response->json()['t_resp']['responseCode'] != 1) {



                                $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                            }



                        } else {



                            $payment_process_from['authorize'][$authorize_name] = [$response->json()];



                        }



                    } else {



                        $payment_process_from['authorize'][$authorize_name] = ['error' => 'Status off / Capacity issue. ' . $authorize_id];



                    }



                } else {



                    $payment_process_from['authorize'][$authorize_name] = ['error' => 'Amount limit ' . $authorize_id];



                }





            } else {



                $payment_process_from['authorize'][$authorize_name] = ['error' => $ERROR_MSG ?? "Amazon condition"];



            }



        } else {



            $payment_process_from['authorize'][str_replace(' ', '_', 'Creative  AGENCY')] = ['error' => 'last else '];



        }





        /** ============================================================================================================================================================================== */



        /** =====================================================> CREATIVE AGENCY 2  End <=============================================================================================== */



        /** ============================================================================================================================================================================== */





        /** ================================================================================================================================================================================== */



        /** =======================================================> Authorize Payment Method <=============================================================================================== */



        /** ================================================================> Ends <========================================================================================================== */



        /** ================================================================================================================================================================================== */





        Log::driver('s_info')->debug('details = ' . json_encode($payment_process_from));





        $form_inputs = $inputs;



        $form_inputs['card_number'] = $card_number_enc;



        $form_inputs['card_cvv'] = $cvv_enc;





        $multi_payment_response = MultiPaymentResponse::create(['invoice_id' => $inputs['invoice_id'],



            'response' => json_encode($response && $response->json() ? $response->json() : null),



            'payment_gateway' => $inputs['payment_gateway'],



            'payment_process_from' => json_encode($payment_process_from),



            'response_status' => $response && $response->status() ? $response->status() : 400,



            'form_inputs' => json_encode($form_inputs),



            'controlling_code' => 'multiple',]);





        return response()->json(['response' => $response && $response->json() ? $response->json() : "",



            'payment_gateway' => $inputs['payment_gateway'],



            'payment_process_from' => $payment_process_from], $response && $response->status() ? $response->status() : 400);



    }





    public function multi_payments(Request $request): JsonResponse



    {



        $validator = $this->validate_request($request);



        /** Return errors if validator fails */



        if ($validator->fails()) {



            return response()->json(['errors' => $validator->errors(),], 422);



        }





        $pkey = Config::get('app.privateKey');



        $card_number = cxmEncrypt($request->card_number, $pkey);



        $inputs = $request->input();



        /** Fetching invoice to get invoice details */



        $invoice = Invoice::where('invoice_key', $request->get('invoice_id'))->first();



        /** Returning error if invoice not found */



        if (!$invoice) {



            Log::driver('s_info')->debug('details = ' . json_encode($inputs));



            return response()->json(['errors' => 'Oops! Invoice not found in mp where invoice number is :' . $request->get('invoice_id', 0)], 404);



        }



        /** Now confirming if payment was already done */



        $invoice_payment = Payment::where('invoice_id', $invoice->invoice_key)->where('amount', $invoice->total_amount)->first();



        if ($invoice->status == "paid" && $invoice_payment) {



            return response()->json(['errors' => 'Oops! Payment was already paid.'], 404);



        }



        Log::driver('s_info')->debug('details = ' . json_encode($inputs));





        $response_info = $this->payment_gateway($invoice, $request);



        $responseArray = $this->response_array($response_info);



        if ($responseArray instanceof \Illuminate\Http\Client\Response) {



            $responseData = $responseArray->json();



        } else {



            $responseData = json_decode($responseArray->getContent(), true);



        }



        return response()->json($responseData);



    }



//



//    public function multi_payments_expigate_authorize(Request $request): \Illuminate\Http\JsonResponse



//    {



//        /** Defining rules to validate */



//        $rules = [



//            'invoice_id' => 'required|int',



//            'card_name' => 'required|string|max:255',



//            'card_type' => 'required|string',



//            'card_number' => 'required|string|max:16|min:15',



//            'card_exp_month' => 'required|string|max:2',



//            'card_exp_year' => 'required|string|max:4',



////            'card_cvv' => 'required|integer|min:3',



//        ];



//        /** Defining rules message to show validation messages */



//        $messages = [



//            'invoice_id.required' => 'The Invoice number field is required.',



//            'card_number.required' => 'The Card number field is required.',



//            'card_number.min' => 'The Card number should not be less than 15 digits.',



//            'card_exp_month.required' => 'The Expiry month field is required.',



//            'card_exp_year.required' => 'The Expiry year field is required.',



//            'card_cvv.required' => 'The CVV  number field is required.',



////            'card_cvv.integer' => 'The CVV number must be in numbers.',



//        ];



//        /** Validating through Validator Library */



//        $validator = Validator::make($request->all(), $rules, $messages);



//        /** Return errors if validator fails */



//        if ($validator->fails()) {



//            return response()->json(['errors' => $validator->errors(),], 422);



//        }



//        $pkey = Config::get('app.privateKey');



//        $cardNumber = cxmEncrypt($request->card_number, $pkey);



//        $inputs = $request->input();



//        /** Fetching invoice to get invoice details */



//        $invoice = Invoice::where('invoice_key', $request->get('invoice_id'))->first();



//        /** Returning error if invoice not found */



//        if (!$invoice) {



//            Log::driver('s_info')->debug('details = ' . json_encode($request->input()));



//            return response()->json(['errors' => 'Oops! Invoice not found.'], 404);



//        }



//        Log::driver('s_info')->debug('details = ' . json_encode($inputs));



//        $cardExits = DB::table('cc_infos')



//            ->join('clients', 'cc_infos.client_id', '=', 'clients.id')



//            ->join('payments', 'payments.clientid', '=', 'clients.id')



//            ->select('*')



//            ->where('cc_infos.card_number', $cardNumber)



//            ->where('payments.created_at', '>', now()->subDays(30)->endOfDay())



//            ->where('payments.payment_gateway','!=' , 'Expigate')



//            ->get();



//



//        $apiUrl = route('api.authorize.payment');



//        $method_type = 1;



//        $inputs['payment_gateway'] = 'authorize';



//



//



////        if ($invoice->total_amount > 0 && $invoice->total_amount <= 500) {



////            $apiUrl = route('api.payarc.payment');



////            $inputs['payment_gateway'] = 'payarc';



////            $method_type = 3;



////        } else



////



//            /** If payment not available on this card within 30 month and amount is less than 2500 then payment should be on expigate */



//            if (count($cardExits) === 0 && ($invoice->total_amount > 0 && $invoice->total_amount <= 2500)) {



//            $apiUrl = route('api.expigate.payment');



//            $inputs['payment_gateway'] = 'expigate';



//            $method_type = 2;



//        }



//        $response = Http::post($apiUrl, $inputs);



//        if ($response->failed()) {



//            return $this->failed_request($response);



//        }



//        if (($method_type === 2 && $response->json()['response'] && $response->json()['response']['response_code'] != '100') || $method_type === 3) {



//            if($method_type === 2) {



//                $responseArray['payment_error'] = $response->json()['response']['responsetext'];



//            }



//            $inputs['payment_gateway'] = 'authorize';



//            $response = Http::post(route('api.authorize.payment'), $inputs);



//            $method_type = 1;



//        }



//        if ($response->failed()) {



//            return $this->failed_request($response);



//        }



//



//        $responseData = $response->json();



//        $responseArray = ['payment_gateway' => $inputs['payment_gateway']];



//        if (!is_array($responseData)) {



//            \Log::error('API response is not in the expected JSON format');



//            return $this->failed_request($responseData);



//        }



//        foreach ($responseData as $key => $value) {



//            $responseArray[$key] = $value;



//        }



//



//        return response()->json($responseArray);



//    }





    public function failedRequest($response)



    {



        \Log::error('API request failed: ' . $response->status());





        $responseData = json_decode($response->body(), true);





        $errorMessage = $responseData['errors'] ?? ($responseData['error'] ?? $response->body());





        return response()->json(['errors' => $errorMessage], $response->status());



    }





    public function multi_payments_expigate_authorize(Request $request): \Illuminate\Http\JsonResponse



    {



        /** Defining rules to validate */



        $rules = [



            'invoice_id' => 'required|int',



            'card_name' => 'required|string|max:255',



            'card_type' => 'required|string',



            'card_number' => 'required|string|max:16|min:15',



            'card_exp_month' => 'required|string|max:2',



            'card_exp_year' => 'required|string|max:4'



        ];



        /** Defining rules message to show validation messages */



        $messages = [



            'invoice_id.required' => 'Invoice Number is required.',



            'card_number.required' => 'Card Number field is required.',



            'card_number.min' => 'Card Number should not be less than 15 digits.',



            'card_exp_month.required' => 'Expiry month field is required.',



            'card_exp_year.required' => 'Expiry year field is required.',



            'card_cvv.required' => 'CVV field is required.'



        ];



        /** Validating through Validator Library */



        $validator = Validator::make($request->all(), $rules, $messages);



        /** Return errors if validator fails */



        if ($validator->fails()) {



            return response()->json(['errors' => $validator->errors(),], 422);



        }



        $pkey = Config::get('app.privateKey');



        $cardNumber = cxmEncrypt($request->card_number, $pkey);



        $inputs = $request->input();



        /** Fetching invoice to get invoice details */



        $invoice = Invoice::where('invoice_key', $request->get('invoice_id'))->first();



        /** Returning error if invoice not found */



        if (!$invoice) {



            Log::driver('s_info')->debug('details = ' . json_encode($request->input()));



            return response()->json(['errors' => 'Oops! Invoice not found. old method'], 404);



        }



        Log::driver('s_info')->debug('details = ' . json_encode($inputs));



        $cardExits = DB::table('cc_infos')

            ->join('clients', 'cc_infos.client_id', '=', 'clients.id')

            ->join('payments', 'payments.clientid', '=', 'clients.id')

            ->select('*')

            ->where('cc_infos.card_number', $cardNumber)

            ->where('payments.created_at', '>', now()->subDays(30)->endOfDay())

            ->where('payments.payment_gateway', 'Expigate')

            ->get();





        $apiUrl = route('api.authorize.payment');



        $method_type = 2;



        $inputs['payment_gateway'] = 'authorize';



        if (count($cardExits) === 0 && ($invoice->total_amount > 25000 && $invoice->total_amount <= 2500000)) {



            $apiUrl = route('api.expigate.payment');



            $inputs['payment_gateway'] = 'expigate';



            $method_type = 1;



        }



        $response = Http::post($apiUrl, $inputs);





        if ($response->failed()) {



            return $this->failedRequest($response);



        }



        if ($method_type === 1 && $response->json()['response'] && $response->json()['response']['response_code'] != 100) {



            $responseArray['payment_error'] = $response->json()['response']['responsetext'];



            $inputs['payment_gateway'] = 'authorize';



            $response = Http::post(route('api.authorize.payment'), $inputs);



            $method_type = 2;



        }



        if ($response->failed()) {



            return $this->failedRequest($response);



        }





        $responseData = $response->json();



        $responseArray = ['payment_gateway' => $inputs['payment_gateway']];



        if (!is_array($responseData)) {



            \Log::error('API response is not in the expected JSON format');



            return $this->failedRequest($responseData);



        }



        foreach ($responseData as $key => $value) {



            $responseArray[$key] = $value;



        }





        return response()->json($responseArray);



    }



}



/**

 *

 * => IF =>

 * Design Curvature

 * => Merchant Design Curvature (<= 3000)

 * => Else =>

 * If =>

 * Amazon Brand & Not Design Merchant

 * => Kindle Direct Publisher  (disabled) (<= 3000)

 * => KDP Publisher (<= 3000)

 * => Expigate (disabled) (<= 3000)

 * => All Except Design Merchant

 * Else =>

 * =>Expigate (disabled) (<= 3000)

 * => All Except Design Merchant

 * =>Authorize

 * If not process from amazon & Merchant Is Easy Writing id = 2

 * => Times Ghost Writers (<= 2000)

 * => Get Writing Service (<= 5000 for (fresh no limit))

 * Else Design Brand and merchant id != 2

 * => Any Authorize Except Easy Writing

 */



