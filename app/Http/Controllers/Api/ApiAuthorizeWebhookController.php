<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Refund;
use App\Models\WebhookResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiAuthorizeWebhookController extends Controller
{
    public function handle_webhook(Request $request)
    {
        $log = Log::driver('webhook_notification');
        try {
            $log->info('Received webhook request: ' . $request->getContent());

            $payment_methods = PaymentMethod::where('live_signature_key', '!=', null)->where('status', 1)->get();

            $payment_signature_key = $payment_method = null;
            $signatureFound = false;
            if ($request->header('X-Anet-Signature')) {
                $json = $request->getContent();
                foreach ($payment_methods as $val) {
                    if (hash_equals(strtolower($request->header('X-Anet-Signature')), 'sha512=' . hash_hmac('sha512', $json, $val->live_signature_key)) && $signatureFound == false) {
                        $payment_signature_key = $val->live_signature_key;
                        $payment_method = $val;
                        $signatureFound = true;
                        break;
                    }
                }
            } else {
                $log->info('header X-Anet-Signature not found.');
            }
            if (!empty($json)) {
                if ($payment_signature_key && $signatureFound == true) {
                    $log->info('After hash match ' . $json);

                    $data = json_decode($json, true);

                    $webhookResponse = new WebhookResponse();
                    $webhookResponse->merchant_name = 'authorize';
                    $webhookResponse->merchant_id = optional($payment_method)->id ?? null;
                    $webhookResponse->merchant_type = isset($payment_method) ? get_class($payment_method) : null;
                    $webhookResponse->notification_id = $data['notificationId'];
                    $webhookResponse->webhook_id = $data['webhookId'];
                    $webhookResponse->event_type = $data['eventType'];
                    $webhookResponse->event_date = $data['eventDate'] ? Carbon::parse($data['eventDate'])->toDateTimeString() : null;
                    $webhookResponse->response = $json;
                    if ($webhookResponse->save()) {

                        if (isset($data['payload']) && isset($data['payload']['invoiceNumber'])) {
                            $invoice = Invoice::where('invoice_key', $data['payload']['invoiceNumber'])->orderByDesc('id')->first();
                            $payment = Payment::where('invoice_id', $data['payload']['invoiceNumber'])->orderByDesc('id')->first();
                            if ($data['eventType']) {
                                $action = str_replace(".", " ", str_replace("net.authorize.", "", $data['eventType']));
                                $log->info('Event Type is : ' . $action);
                            }
                            if ($invoice && $payment && $data['eventType']) {
                                $eventTypes_for_refund = [
                                    'net.authorize.payment.refund.created',
                                    'net.authorize.payment.void.created',
                                    'net.authorize.payment.fraud.declined'
                                ];
                                if (in_array($data['eventType'], $eventTypes_for_refund)) {
                                    $refund = new Refund();
                                    $refund->team_key = $invoice->team_key;
                                    $refund->brand_key = $invoice->brand_key;
                                    $refund->payment_id = $payment->id;
                                    $refund->agent_id = $invoice->agent_id;
                                    $refund->client_id = $invoice->client_id;
                                    $refund->invoice_id = $data['payload']['invoiceNumber'];
                                    $refund->amount = $data['payload']['authAmount'] ?? null;
                                    $refund->authorizenet_transaction_id = $data['payload']['id'] ?? null;
                                    $refund->reason = 'from response. ' . $action;
                                    $refund->type = 'refund';
                                    $refund->qa_approval = 1;

                                    $payment->payment_status_process_time = Carbon::now();
                                    $payment->payment_status = 2;
                                    $payment->save();
                                }
                            } else {
                                if (!$invoice) {
                                    $log->info('Invoice not found.');
                                }
                                if (!$payment) {
                                    $log->info('Payment not found.');
                                }
                                if (!$data['eventType']) {
                                    $log->info('Event Type not found.');
                                }
                            }
                        } else {
                            $log->info(((isset($data['payload']) ? 'payload' : '') . (isset($data['payload']['invoiceNumber']) ? 'invoiceNumber' : '')) . ' not found');
                        }
                        $log->info('Saved..!');
                    } else {
                        $log->info('Response not saved.!');
                    }
                } else {
                    $log->info('Empty signature or signature not matched.');
                }
            } else {
                $log->info('Json response not found.');
            }
            return response()->json(['message' => 'Webhook received'], 200);
        } catch (\Exception $e) {
            $log->info('Error Message : ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
