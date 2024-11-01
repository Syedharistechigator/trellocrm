<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\File;

class CreateSessionDirectory
{
    public function handle($request, Closure $next)
    {
        $sessionPath = storage_path('framework/sessions');

        // Check if the session directory exists; if not, create it
        if (!File::isDirectory($sessionPath)) {
            File::makeDirectory($sessionPath, 0755, true, true);
        }

        return $next($request);
    }
}
