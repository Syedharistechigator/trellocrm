<?php

namespace App\Console\Commands;

use App\Models\EmailConfiguration;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailSystemRefreshAccessTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:RefreshEmailConfigurationAccessToken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to refresh email system (G suite) access token';

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
        $this->handle_email_configuration(EmailConfiguration::where('parent_id',0)->get());
        $log->info('handle parent email configuration');
        $this->handle_email_configuration(EmailConfiguration::where('parent_id','>',0)->get());
        $log->info('handle child email configuration');
        $log->info('Cron job completed.');
        $log->info('*************************************************');
        return Command::SUCCESS;
    }

    private function handle_email_configuration($email_configurations){
        foreach ($email_configurations as $email_configuration) {
            if (!$email_configuration->access_token){
                break;
            }
            $parent_email = EmailConfiguration::where('id', $email_configuration->parent_id)->where('client_id', $email_configuration->client_id)->where('client_secret', $email_configuration->client_secret)->first();
            if ($parent_email) {
                $email_configuration->access_token = $parent_email->access_token;
                $email_configuration->save();
                break;
            }

            try {
                $refresh_token_response = $this->refresh_token($email_configuration);
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
    /**
     * @throws \JsonException
     */
    private function refresh_token(EmailConfiguration $email_configuration)
    {
        $email_token = json_decode($email_configuration->access_token, true, 512, JSON_THROW_ON_ERROR);
        if (!$email_configuration->client_id || !$email_configuration->client_secret) {
            Log::driver('email_system_cron')->debug('Credentials not found.!');
            throw new \RuntimeException('Credentials not found!');
        }
        if (!array_key_exists('refresh_token', $email_token)) {
            Log::driver('email_system_cron')->debug('Refresh token not found.!');
            throw new \RuntimeException('Refresh token not found!');
        }
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $email_token['refresh_token'],
            'client_id' => $email_configuration->client_id,
            'client_secret' => $email_configuration->client_secret,
        ]);
        if (!$response->successful()) {
            $error = $response->json('error_description', 'Token Expire : 100');
            Log::driver('email_system_cron')->debug('Token refresh exception: ' . $error);
            throw new \RuntimeException($error);
        }
        return $response;
    }

    /**
     * @throws \JsonException
     */
    private function update_token(EmailConfiguration $email_configuration, ?array $token)
    {
        $email_token = json_decode($email_configuration->access_token, true, 512, JSON_THROW_ON_ERROR);

        foreach (['access_token', 'expires_in', 'refresh_token'] as $field) {
            if (array_key_exists($field, $token)) {
                $email_token[$field] = $token[$field];
            }
        }
        $email_token['expires_at'] = $token['expires_at'] ?? now()->addSeconds($token['expires_in'])->timestamp;
        $token = $email_token;
        $email_configuration->access_token = json_encode($token, JSON_THROW_ON_ERROR);
        $email_configuration->save();
    }
}
