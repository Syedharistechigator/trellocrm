<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\File;

class CreateViewDirectory
{
    public function handle($request, Closure $next)
    {
        $viewPath = storage_path('framework/views');

        // Check if the view directory exists; if not, create it
        if (!File::isDirectory($viewPath)) {
            File::makeDirectory($viewPath, 0755, true, true);
        }

        return $next($request);
    }
}
