<?php

namespace App\Http\Controllers;

use App\Models\CcInfo;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\PaymentAuthorization;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MerchantFeeAndTaxPaymentController extends Controller
{
    public function MerchantFeeAndTaxPayment(Request $request)
    {
        try {
            $invoice_id = $request->get('invoiceid');
            $invoice = $this->getInvoice($invoice_id);

            if (!$invoice) {
                Log::driver('merchant_payment_info')->debug('Invoice not found mt : ');
                return response()->json(['errors' => 'Invoice not found . '], 404);
            }
            $paymentCheck = $this->isPaymentAlreadyMade($invoice);
            if ($paymentCheck['status']) {
                return response()->json(['errors' => $paymentCheck['message']], 404);
            }

            $client = Client::where('id', $invoice->clientid)->first();
            if (!$client) {
                Log::driver('merchant_payment_info')->debug('Client not found where invoice key is : ' . $invoice->invoice_key);
                return response()->json(['errors' => 'Client not found where invoice key is : ' . $invoice->invoice_key], 404);
            }
            $client_cc_info = CcInfo::where('client_id', $client->id)->where('invoice_id', $invoice->invoice_key)->where('status', 1)->first();
            if (!$client_cc_info) {
                Log::driver('merchant_payment_info')->debug('Card not found in merchant payment where invoice number is mt : ' . $invoice->invoice_key . ' , client id is : ' . $invoice->clientid);
                return response()->json(['errors' => 'Card not found in merchant payment where invoice number is mt : ' . $invoice->invoice_key . ' , client id is : ' . $invoice->clientid], 404);
            }
            $missingFields = $this->checkMissingFields($client_cc_info);
            if (!empty($missingFields)) {
                $errorMsg = 'Oops! ' . $missingFields . ' field not found';
                Log::driver('merchant_payment_info')->debug($errorMsg);
            }
            $inputs = $this->preparePaymentInputs($invoice, $client_cc_info, Config::get('app.privateKey'));
            $paymentResponse = $this->processPaymentByGateway($request, $inputs, $invoice->payment_gateway);
            Log::driver('merchant_payment_info')->info('Final payment response', ['response' => $paymentResponse]);
            if (isset($paymentResponse['status']) && isset($paymentResponse['response']) && $paymentResponse['status'] === true) {
                return response()->json(['success' => 'Payment processed successfully.', 'response' => $paymentResponse['response']]);
            }
            return response()->json(['errors' => 'Payment failed.', 'details' => $paymentResponse], 422);
        } catch (RequestException $e) {
            return response()->json(['errors' => $e], 422);
        }
    }

    private function getInvoice($invoice_id)
    {
        if ($invoice_id) {
            return Invoice::where('invoice_key', $invoice_id)->first();
        }
        return Invoice::where(function ($query) {
            $query->where(function ($query_1) {
                $query_1->where('is_merchant_handling_fee', 1)
                    ->where('merchant_handling_fee_paid', 0)
                    ->where('merchant_handling_fee', '>', 0)
                    ->where('is_merchant_handling_fee_curl', 0);
            })->orWhere(function ($query_2) {
                $query_2->where('is_merchant_handling_fee', 1)
                    ->where('is_tax', 1)
                    ->where('tax_paid', 0)
                    ->where('tax_amount', '>', 0)
                    ->where('is_tax_curl', 0);
            });
        })
            ->first();
    }

    private function isPaymentAlreadyMade($invoice): array
    {
        if ($invoice->status == "paid") {
            if ($invoice->is_merchant_handling_fee == 1) {
                if ($invoice->is_tax == 1) {
                    if ($invoice->tax_paid == 1) {
                        if ($invoice->merchant_handling_fee_paid == 1) {
                            return ['status' => true, 'message' => 'Payment was already paid.'];
                        }
                    } else if ($invoice->tax_amount < 1) {
                        return ['status' => true, 'message' => 'Tax amount is less than 1.'];
                    } else {
                        return ['status' => false];
                    }
                }
                if ($invoice->merchant_handling_fee_paid == 1) {
                    return ['status' => true, 'message' => 'Merchant handling fee was already paid.'];
                }
                if ($invoice->merchant_handling_fee < 1) {
                    return ['status' => true, 'message' => 'Merchant handling fee is less than 1.'];
                }
                return ['status' => false];
            }
            return ['status' => true, 'message' => 'Merchant handling fee is not applicable for this invoice.'];
        }

        return ['status' => true, 'message' => 'Original Amount was not paid, where invoice id is : ' . $invoice->invoice_key];
    }

    private function checkMissingFields($client_cc_info): string
    {
        return collect([
            $client_cc_info->address ? null : 'address',
            $client_cc_info->zipcode ? null : 'zipcode',
            $client_cc_info->city ? null : 'city',
            $client_cc_info->state ? null : 'state',
            $client_cc_info->country ? null : 'country'
        ])->filter()->implode(', ');
    }

    private function preparePaymentInputs($invoice, $client_cc_info, $pkey): array
    {
        return [
            'invoice_id' => $invoice->invoice_key,
            'is_curl' => 1,
            'card_name' => $client_cc_info->card_name,
            'card_number' => cxmDecrypt($client_cc_info->card_number, $pkey),
            'card_cvv' => cxmDecrypt($client_cc_info->card_cvv, $pkey),
            'card_exp_month' => $client_cc_info->card_exp_month,
            'card_exp_year' => $client_cc_info->card_exp_year,
            'card_type' => $client_cc_info->card_type,
            'address' => $client_cc_info->address,
            'zipcode' => $client_cc_info->zipcode,
            'city' => $client_cc_info->city,
            'state' => $client_cc_info->state,
            'country' => $client_cc_info->country,
            'payment_gateway' => strtolower($invoice->payment_gateway),
            'merchant_id' => $invoice->merchant_id,
        ];
    }

    private function processPaymentByGateway(Request $request, $inputs, $payment_gateway): array
    {
        try {
            if (in_array(strtolower($payment_gateway), ['authorize', 'expigate'])) {
                $url = route('api.' . strtolower($payment_gateway) . '.payment');
                Log::driver('merchant_payment_info')->info('Sending payment request', ['url' => $url, 'inputs' => $inputs]);
                $response = Http::withHeaders(['X-Source' => $request->url()])->post($url, $inputs)->json();
                if (!$response) {
                    Log::driver('merchant_payment_info')->error('No response from payment gateway.', ['gateway' => $payment_gateway, 'invoice_id' => $inputs['invoice_id']]);
                    return ['status' => false, 'message' => 'No response from payment gateway.'];
                }
                Log::driver('merchant_payment_info')->info('Payment gateway response received', ['status' => true, 'response' => $response]);
                return ['status' => true, 'response' => $response];
            }
            Log::driver('merchant_payment_info')->debug('Invalid Merchant.');
            return ['status' => false, 'message' => 'Invalid Merchant.'];
        } catch (\Exception $e) {
            Log::driver('merchant_payment_info')->error('Payment processing error', [
                'error_message' => $e->getMessage(),
                'invoice_id' => $inputs['invoice_id'] ?? null,
                'gateway' => $payment_gateway
            ]);
            return ['status' => false, 'message' => 'An error occurred while processing payment.'];
        }
    }
}
