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

class VerifyIp extends Command
{
    protected $signature = 'verify:ip';
    protected $description = 'Perform IP verification on leads';
    protected $httpClient;

    public function __construct()
    {
        parent::__construct();
        $this->httpClient = new Client();
    }

    public function verifyIP($ip, $apiKey)
    {
        $countries = ['US', 'CA'];
        $parameters = [
            'country' => $countries,
        ];

        $formattedParameters = http_build_query($parameters);

        $url = sprintf(
            'https://www.ipqualityscore.com/api/json/ip/%s/%s?%s',
            $apiKey,
            $ip,
            $formattedParameters
        );
        try {
            $response = $this->httpClient->get($url);
            $data = json_decode($response->getBody(), true);
            return $data;
        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function handle()

    {
        // Fetch leads that need IP verification and update their status
        $leads = Lead::where('is_ip_verify', 0)->orderByDesc('created_at')->where('lead_ip', '!=', null)->where('lead_ip', '!=', '---')
            ->where(function ($query) {
                $query->where('lead_country', 'United States')
                    ->orWhere('lead_country', 'Canada');
            })

            ->take(10)->get();

        foreach ($leads as $lead) {
            $randomRecord = User_info_api::where('balance', '>', 0)->inRandomOrder()->first();

            if ($randomRecord) {
                $apiKey = $randomRecord->key;

                if ($lead->is_ip_verify != 1) {
                    if ($lead->lead_ip != null && $lead->lead_ip != '---') {
                        if ($lead->lead_country == 'United States' || $lead->lead_country == 'Canada') {
                            $this->info("API Key: $apiKey, Ip: $lead->lead_ip");
                            $data = $this->verifyIP($lead->lead_ip, $apiKey);
                            $randomRecord->balance = $randomRecord->balance -1;
                            $randomRecord->save();
                            if (isset($data['success']) && $data['success'] === true && ($data['country_code'] == "US" || $data['country_code'] == "CA")) {
                                $lead->is_ip_verify = 1; // Valid ip
                            } else {
                                $lead->is_ip_verify = 2; // Invalid ip
                            }
                            $lead->ip_response = $data;
                            $lead->save();
                        } else {
                            $this->info('VI-83 : Invalid country');
                            $lead->ip_response = '"VI-84 : Invalid country"';
                            $lead->save();
                        }
                    } else {
                        $this->info('VI-88 : Lead IP not found.');
                        $lead->ip_response = '"VI-89 : Lead IP not found."';
                        $lead->save();
                    }
                }
            } else {
                $this->info('No valid user info api record found for IP verification.');
                $this->error('No valid user info api record found for IP verification.');
                return;
            }
        }

        $this->info('IP verification completed.');
    }
}
