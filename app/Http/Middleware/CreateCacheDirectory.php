<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CreateCacheDirectory
{
    public function handle($request, Closure $next)
    {
        $cachePath = storage_path('framework/cache');
        // Check if the cache directory exists; if not, create it
        if (!File::isDirectory($cachePath)) {
            File::makeDirectory($cachePath, 0755, true, true);
            Artisan::call('config:cache');
        }

        return $next($request);
    }
}
