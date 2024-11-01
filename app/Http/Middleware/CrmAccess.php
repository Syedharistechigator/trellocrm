<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !in_array(Auth::user()->user_access, [0, 2], true)) {
            if ($request->is('api/*')) {
                return response()->json(['error' => "Looks like you don't have access to Trello right now."], 403);
            }
            return redirect()->route('dashboard')->with('error', "Looks like you don't have access to Trello right now.");
        }
        return $next($request);
    }
}
