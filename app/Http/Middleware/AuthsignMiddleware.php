<?php

namespace App\Http\Middleware;

use Closure;

class AuthsignMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('authsign_id')) {
            return redirect()->route('login.form')->with('error', 'Please login first');
        }

        return $next($request);
    }
}
