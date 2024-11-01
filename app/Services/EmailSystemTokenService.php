<?php

namespace App\Services;

use App\Models\EmailConfiguration;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailSystemTokenService
{
    /**
     * Refreshes the access token for the provided email configuration.
     *
     * @param EmailConfiguration $email_configuration
     * @return array
     * @throws Exception
     */
    public function refresh_token(EmailConfiguration $email_configuration): array
    {
        try {
            $email_token = json_decode($email_configuration->access_token, true);
            if (array_key_exists('refresh_token', $email_token)) {
                $response = Http::post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $email_token['refresh_token'],
                    'client_id' => $email_configuration->client_id,
                    'client_secret' => $email_configuration->client_secret,
                ]);

                if (!$response->successful()) {
                    $error = $response->json('error_description', 'Token Expire');
                    Log::error('Token refresh exception: ' . $error);
                    throw new Exception($error);
                }
                return $response->json();
            }
            throw new Exception('Refresh token not found in the access token JSON.');
        } catch (Exception $e) {
            Log::error('Token refresh exception: ' . $e->getMessage());
            throw new Exception('Token Expire');
        }
    }

    /**
     * Retrieves the access token for the provided email configuration.
     *
     * @param EmailConfiguration $email_configuration
     * @return string
     * @throws Exception
     */
    public function get_access_token(EmailConfiguration $email_configuration): string
    {
        try {
            $email_token = json_decode($email_configuration->access_token, true, 512, JSON_THROW_ON_ERROR);
            if (!isset($email_token['access_token'])) {
                throw new \RuntimeException('Access token not found in the decoded JSON.');
            }
            return $email_token['access_token'];
        } catch (Exception $e) {
            Log::error('Error retrieving access token: ' . $e->getMessage());
            throw new Exception('Failed to retrieve access token');
        }
    }
}
