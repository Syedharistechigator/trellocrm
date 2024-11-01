<?php

namespace App\Console\Commands;

use App\Http\Controllers\AdminSplitPaymentController;
use App\Models\Brand;
use App\Models\CcInfo;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentErrorCods;
use App\Models\PaymentMethod;
use App\Models\PaymentTransactionsLog;
use App\Models\SplitPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemainingTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remaining:transaction';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform remaining transaction on leads';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new AdminSplitPaymentController();

        $maxRetries = 2;
        Log::info('');
        Log::info('*************************************************');

        for ($retryCount = 0; $retryCount < $maxRetries; $retryCount++) {
            $split_payments = SplitPayment::where('status', 0)->inRandomOrder()->take(2)->get();
            if ($split_payments->isEmpty()) {
                Log::info("Success: No payment pending.");
                break; // No payments pending, exit the loop
            }
            foreach ($split_payments as $key => $split_payment) {
                try {
                    $response = $controller->pay_now_split_payments($split_payment->id);

                    if ($response->getStatusCode() === 200) {
                        $responseData = json_decode($response->getContent());
                        if (isset($responseData->status)) {
                            Log::info($responseData->status . ", sp-id = " . $split_payment->id);
                            $retryCount=2;
                        } else {
                            Log::error("An unknown error occurred for sp-id = " . $split_payment->id);
                        }
                    } else {
                        $errorResponse = json_decode($response->getContent());
                        if ($errorResponse && isset($errorResponse->error)) {
                            $errorMessage = $errorResponse->error;
                            Log::error($errorMessage . " sp-id = " . $split_payment->id);
                        }
                    }
                } catch (\Exception $e) {
                    $errorResponse = json_decode($response->getContent());
                    if ($errorResponse && isset($errorResponse->error)) {
                        $errorMessage = $errorResponse->error;
                        Log::error($errorMessage . " sp-id = " . $split_payment->id);
                    }
                    Log::error("Error processing payment for SP-ID {$split_payment->id}: " . $e->getMessage());
                }
            }
            Log::info('Cron job completed.');
            Log::info('*************************************************');
        }
    }
}
