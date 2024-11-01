<?php

namespace App\Http\Controllers;

use App\Models\EmailConfiguration;
use Exception;
use Hybridauth\Provider\Google;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSocialiteController extends Controller
{
    private $provider;

    private function google_authenticate(EmailConfiguration $email, $id)
    {
        try {
            $state = [
                'dev' => 'dev michael',
                'id' => $id
            ];
            $scopes = 'https://mail.google.com https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/contacts.readonly https://www.googleapis.com/auth/contacts.other.readonly https://www.googleapis.com/auth/directory.readonly'
//                ' https://people.googleapis.com',

            ;
            $config = [
                'callback' => route('handle.google.call.back'),
                'keys' => [
                    'id' => $email->client_id,
                    'secret' => $email->client_secret
                ],
                'scope' => $scopes,
                'authorize_url_parameters' => [
                    'prompt' => 'consent',
//                    'approval_prompt' => 'force',
                    'access_type' => 'offline',
                    'state' => base64_encode(json_encode($state)),
                ]
            ];
            return new Google($config);
        } catch (\Exception $e) {
            return back()->with(['error' => $e->getMessage()], 500);
        }
    }

    public function redirect(Request $request, $id)
    {
        try {
            $email = $base_email = EmailConfiguration::find($id);
            if ($base_email->parent_id != 0 && ($base_email->client_id == null || $base_email->client_secret == null)) {
                $email = $parent_email = EmailConfiguration::find($base_email->parent_id);
            }

            $email_token = [];
            if (!empty($email->access_token)) {
                $email_token = json_decode($email->access_token, true, 512, JSON_THROW_ON_ERROR);
            }
            $this->provider = $this->google_authenticate($email, $id);
            $this->provider->disconnect();
            if (!$this->provider->isConnected()) {
                $this->provider->authenticate();
            } elseif ($email->access_token == null) {
                $this->provider->disconnect();
                $this->provider->authenticate();
            }
//            $token1 = $this->provider->getAccessToken();
//            if ($this->provider->hasAccessTokenExpired() || ($email_token && array_key_exists('refresh_token', $email_token))) {
            /**refresh token every time*/
            $token = $this->refresh_token($email, $this->provider);
//            }
            /** Through this remaining fields won't disturb like if we only fetch access token it will update access token and won't change refresh token to empty*/
            /** First it will check if access_token available then it will only update fields and if not then update whole access_token instance*/
            if ($token) {
                if (array_key_exists('access_token', $token)) {
                    $email_token['access_token'] = $token['access_token'];
                }
                if (array_key_exists('token_type', $token)) {
                    $email_token['token_type'] = $token['token_type'];
                }
                if (array_key_exists('refresh_token', $token)) {
                    $email_token['refresh_token'] = $token['refresh_token'];
                }
                if (array_key_exists('expires_in', $token)) {
                    $email_token['expires_in'] = $token['expires_in'];
                }
                if (array_key_exists('expires_at', $token)) {
                    $email_token['expires_at'] = $token['expires_at'];
                }
                $token = $email_token;
            }
            if ($base_email->parent_id != 0 && ($base_email->client_id == null || $base_email->client_secret == null)) {
                $parent_email->update(['access_token' => $token]);
            } elseif ($base_email->parent_id != 0) {
                $parent_email = EmailConfiguration::where('id', $base_email->parent_id)->where('client_id', $base_email->client_id)->where('client_secret', $base_email->client_secret)->first();
                if ($parent_email) {
                    $parent_email->update(['access_token' => $token]);
                }
            }
            $base_email->update(['access_token' => $token]);
            return redirect()->route('admin.email.configuration.edit', $base_email->id)->with('success', 'Authentication successful!');
        } catch (\Exception $e) {
//            dd($e->getMessage(), 'redirect');
            return back()->with(['error' => $e->getMessage()], 500);
        }
    }

    public function handle_call_back(Request $request)
    {
        try {
            $state = json_decode(base64_decode($request->get('state')));
            $email = $base_email = EmailConfiguration::find($state->id);
            if ($base_email->parent_id != 0 && ($base_email->client_id == null || $base_email->client_secret == null)) {
                $email = $parent_email = EmailConfiguration::find($base_email->parent_id);
            }
            $email_token = [];
            if (!empty($email->access_token)) {
                $email_token = json_decode($email->access_token, true, 512, JSON_THROW_ON_ERROR);
            }
            $this->provider = $this->google_authenticate($email, $state->id);
            if (!$this->provider->isConnected()) {
                $this->provider->authenticate();
            } elseif ($email->access_token == null) {
                $this->provider->disconnect();
                $this->provider->authenticate();
            }
//            $token = $this->provider->getAccessToken();
//            if ($this->provider->hasAccessTokenExpired()) {
            /** Refresh token every time*/
            $token = $this->refresh_token($email, $this->provider);
//            }
            /** Through this remaining fields won't disturb like if we only fetch access token it will update access token and won't change refresh token to empty*/
            /** First it will check if access_token available then it will only update fields and if not then update whole access_token instance*/
            if ($token) {
                if (array_key_exists('access_token', $token)) {
                    $email_token['access_token'] = $token['access_token'];
                }
                if (array_key_exists('token_type', $token)) {
                    $email_token['token_type'] = $token['token_type'];
                }
                if (array_key_exists('refresh_token', $token)) {
                    $email_token['refresh_token'] = $token['refresh_token'];
                }
                if (array_key_exists('expires_in', $token)) {
                    $email_token['expires_in'] = $token['expires_in'];
                }
                if (array_key_exists('expires_at', $token)) {
                    $email_token['expires_at'] = $token['expires_at'];
                }
                $token = $email_token;
            }
            if ($base_email->parent_id != 0 && ($base_email->client_id == null || $base_email->client_secret == null)) {
                $parent_email->update(['access_token' => $token]);
            } elseif ($base_email->parent_id != 0) {
                $parent_email = EmailConfiguration::where('id', $base_email->parent_id)->where('client_id', $base_email->client_id)->where('client_secret', $base_email->client_secret)->first();
                if ($parent_email) {
                    $parent_email->update(['access_token' => $token]);
                }
            }
            $base_email->update(['access_token' => $token]);
            return redirect()->route('admin.email.configuration.edit', $base_email->id)->with('success', 'Authentication successful!');
        } catch (\Exception $e) {
//            dd($e->getMessage(), 'handle_call_back');
            return redirect()->back()->with(['error' => $e->getMessage()], 500);
        }
    }

    private function refresh_token(EmailConfiguration $email, $provider)
    {
        try {
            $this->provider = $provider;
            $email_token = json_decode($email->access_token, true);

            $token = $this->provider->getAccessToken();
            if (array_key_exists('refresh_token', $token)) {
                return $token;
            }
            if (array_key_exists('refresh_token', $email_token)) {
                $response = Http::post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $email_token['refresh_token'],
                    'client_id' => $email->client_id,
                    'client_secret' => $email->client_secret,
                ]);

                $responseBody = $response->json();
                if (!$response->successful() || !isset($responseBody['access_token'])) {
                    $error = $responseBody['error_description'] ?? 'Token Expire : 107';
                    Log::error('Token refresh exception: ' . $error);
                    throw new Exception($error);
                }
                return $responseBody;
            }
            throw new \RuntimeException('Refresh token not found.!');
        } catch (Exception $e) {
//            dd($e->getMessage(), 'refresh_token');
            Log::error('Token refresh exception: ' . $e->getMessage());
            throw new Exception('Token Expire : 112');
        }
    }
}
