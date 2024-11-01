<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/** Package */
use GuzzleHttp\Client;
/** Package Exception */
use GuzzleHttp\Exception\ClientException;
/** Models */
use App\Models\Lead;
use App\Models\User_info_api;

class VerifyPhone extends Command
{
    protected $signature = 'verify:phone';
    protected $description = 'Perform phone number verification on leads';

    public function __construct()
    {
        parent::__construct();
    }

    public function verifyPhone($phone, $apiKey)
    {
        $countries = ['US', 'CA'];
        $parameters = [
            'country' => $countries,
        ];

        $formattedParameters = http_build_query($parameters);

        $url = sprintf(
            'https://www.ipqualityscore.com/api/json/phone/%s/%s?%s',
            $apiKey,
            $phone,
            $formattedParameters
        );

        $httpClient = new Client();
        try {
            $response = $httpClient->get($url);
            $result = json_decode($response->getBody(), true);
            return $result;
        } catch (ClientException $e) {
            // Handle exception here (e.g., log, display error message)
            return ['error' => $e->getMessage()];
        }
    }

    public function handle()
    {
        $leads = Lead::where('is_number_verify', 0)->orderByDesc('created_at')->where('phone', '!=', null)
            ->where('phone', 'regexp', '^([0-9\s\-\+\(\)]*)$')
            ->where('phone', 'regexp', '^\d{10}$')
            ->where('phone', '!=', 1234567890)//for fake number
            ->where('phone', '!=', '---')->take(10)->get();

        foreach ($leads as $lead) {
            $randomRecord = User_info_api::where('balance', '>', 0)->inRandomOrder()->first();

            if ($randomRecord) {
                $apiKey = $randomRecord->key;

                if ($lead->is_number_verify != 1) {
                    if ($lead->phone != null && $lead->phone != '---') {
//                        $usPhonePattern = '/^\(?\d{3}\)?[-\/\s]?\d{3}[-\/\s]?\d{4}$/';
//                        $caPhonePattern = '/^\(?\d{3}\)?[-\/\s]?\d{3}[-\/\s]?\d{4}$/';
                        $phonePattern = '/^([0-9\s\-\+\(\)]*)$/';
                        $phonePattern2 = '/^\d{10}$/';
                        if (preg_match($phonePattern, $lead->phone) && preg_match($phonePattern2, $lead->phone)) {
                            $this->info("API Key: $apiKey, Phone: $lead->phone");
                            $data = $this->verifyPhone($lead->phone, $apiKey);
                            $randomRecord->balance = $randomRecord->balance - 1;
                            $randomRecord->save();
                            if (isset($data['valid']) && $data['valid'] === true) {
                                $lead->is_number_verify = 1; // Valid number
                            } else {
                                $lead->is_number_verify = 2; // Invalid number
                            }
                            $lead->number_response = $data;
                            $lead->save();
                        } else {
                            $this->info('VP-86 : Invalid phone format.');
                            $lead->number_response = '"VP-87 : Invalid phone format."';
                            $lead->save();
                        }
                    } else {
                        $this->info('VP-84 : Lead Phone not found.');
                        $lead->number_response = '"VP-92 : Lead Phone not found."';
                        $lead->save();
                    }
                }
            } else {
                $this->info('No valid record found for phone verification.');
                $this->error('No valid record found for phone verification.');
                return;
            }
        }

        $this->info('Phone verification completed.');
    }
}
