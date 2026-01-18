<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthOptional
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());

            if ($accessToken && $accessToken->tokenable) {
                auth()->login($accessToken->tokenable);
            }
        }

        return $next($request);
    }
}
