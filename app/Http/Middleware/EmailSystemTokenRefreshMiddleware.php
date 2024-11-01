<?php

namespace App\Http\Middleware;

use App\Models\EmailConfiguration;
use App\Services\EmailSystemTokenService;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailSystemTokenRefreshMiddleware
{
    protected $tokenService;
    public function __construct(EmailSystemTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle($request, Closure $next)
    {
        $emailConfiguration = EmailConfiguration::find($request->input('email_configuration_id'));
        try {
//            if ($this->isAccessTokenExpired($emailConfiguration)) {
//                // If the token is expired, refresh it
//                $this->refreshAccessToken($emailConfiguration);
//            }
        } catch (Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
            // Handle token refresh failure gracefully
            // You might want to redirect to an error page or handle it in a different way
            return response()->json(['error' => 'Token refresh failed', 'status' => 0]);
        }

        // Proceed with the request
        return $next($request);
    }

    protected function isAccessTokenExpired(EmailConfiguration $emailConfiguration): bool
    {
        return now()->greaterThan($emailConfiguration->access_token_expiry);
    }

    protected function refreshAccessToken(EmailConfiguration $emailConfiguration): void
    {
        $this->tokenService->refresh_token($emailConfiguration);
    }
}
