<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $version = $request->header('Accept-Version') ?? $request->get('version', 'v1');
        if (!in_array($version, ['v1', 'v2'])) {
            return response()->json(['error' => 'API version not supported.'], 400);
        }

        $request->headers->set('Accept', 'application/vnd.application.' . $version . '+json');
        return $next($request);
    }
}
