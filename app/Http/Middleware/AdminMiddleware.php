<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('authsign_id') || !session('authsign_is_admin')) {
            return redirect()->route('login.form')->with('error', 'Admin access only');
        }
        return $next($request);
    }
}
