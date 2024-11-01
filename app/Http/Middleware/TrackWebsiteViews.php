<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackWebsiteViews
{

    public function getIpResponse()
    {
        $tokens = [
            '478789134a7b9f',
            'c4d5bd23f6904c',
            '12b59c8b5bf82e',
            'f19afe426ebfa7',
            '590a01c8690db0',
            '0aaa18feea61f7',
            'f37b2121d2944b',
            'ff661bbe09498d',
        ];
        shuffle($tokens);

        foreach ($tokens as $token) {
            $curl = curl_init();
            if ($_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
                curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "/json?token=" . $token);
            } else {
                curl_setopt($curl, CURLOPT_URL, "https://ipinfo.io/" . $_SERVER['REMOTE_ADDR'] . "/json?token=" . $token);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $ipResponse = curl_exec($curl);
            $httpStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $ipResponse = (array)json_decode($ipResponse);
            if (!empty($ipResponse) && $httpStatusCode != 429) {
                $ipResponse['valid_token'] = $token;
                break;
            }

            if ($ipResponse && isset($ipResponse['status']) && $ipResponse['status'] == 429) {
                $ipResponse['expire_tokens'] = $token;
            } else {
                Log::error('Token ' . $token . ' failed with status code ' . $httpStatusCode);
            }
        }

        if ($ipResponse && isset($ipResponse['status']) && $ipResponse['status'] == 429) {
            Log::error('All Tokens Expired ');

            $ipResponse['ip'] = "All Tokens Expired";
            $ipResponse['city'] = "All Tokens Expired";
            $ipResponse['state'] = "All Tokens Expired";
            $ipResponse['country'] = "All Tokens Expired";
            $ipResponse['postal'] = "All Tokens Expired";
        }
        $ipResponse['ip'] = $ipResponse['ip'] ?? null;
        $ipResponse['city'] = $ipResponse['city'] ?? null;
        $ipResponse['state'] = $ipResponse['region'] ?? $ipResponse['state'] ?? null;
        $ipResponse['country'] = $ipResponse['country'] ?? null;
        $ipResponse['postal'] = $ipResponse['postal'] ?? null;
        $ipResponse['valid_token'] = $ipResponse['valid_token'] ?? null;
        $ipResponse['expire_tokens'] = $ipResponse['expire_tokens'] ?? [];
        return $ipResponse;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return null[]
     */

    public function handle(Request $request, Closure $next)
    {
        $ipAddress = $this->getIpResponse();
        auth()->user()->websiteViews()->create([
            'page_url' => $request->fullUrl(),
            'ip_address' => $ipAddress['ip'],
            'ip_response' => json_encode($ipAddress),
        ]);

        return $next($request);
    }
}
