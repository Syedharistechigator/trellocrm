<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MerchantFeeAndTaxPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MerchantFeeAndTaxPaymentController';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to pay client remaining merchant handling fee or tax.';

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
        $log = Log::driver('email_system_cron');
        $log->info('');
        $log->info('*************************************************');
        $this->handle_payment(Invoice::where('status', 'paid')->where(function ($query) {
            $query->where('is_tax_curl', 0)->orWhere('is_merchant_handling_fee_curl', 0);
        })->get());
        $log->info('handle parent email configuration');
        $log->info('Cron job completed.');
        $log->info('*************************************************');
        return Command::SUCCESS;
    }

    private function handle_payment($invoices)
    {
        foreach ($invoices as $invoice) {
            if ($invoice->is_tax_curl = 0) {
                break;
            }
            if ($invoice->is_tax_curl == 1 && $invoice->is_merchant_handling_fee_curl == 0) {
                break;
            }

            try {
                $refresh_token_response = $this->send_request($invoice);
                Log::driver('email_system_cron')->info($refresh_token_response);

                if (!$refresh_token_response || $refresh_token_response->failed() || $refresh_token_response->status() === 401) {
                    throw new \RuntimeException("Token Expire");
                }
                $refresh_token_response = $refresh_token_response->json();
                $this->update_token($email_configuration, $refresh_token_response);
                $this->info('Access token refreshed successfully for email = ' . $email_configuration->email . ' id = ' . $email_configuration->id);
            } catch (Exception $e) {
                Log::driver('email_system_cron')->debug('Error refreshing token for ' . $email_configuration->email . ': ' . $e->getMessage());
                $this->error('Error refreshing token for email = ' . $email_configuration->email . ' id = ' . $email_configuration->id . ': ' . $e->getMessage());
            }
        }
    }
}
