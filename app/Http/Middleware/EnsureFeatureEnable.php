<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFeatureEnabled
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        $enabled = (bool) config("features.{$feature}", false);

        if (! $enabled) {
            // dibuat 404 biar user tidak "lihat" fitur ini sama sekali
            abort(404);
        }

        return $next($request);
    }
}