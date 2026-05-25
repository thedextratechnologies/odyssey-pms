<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MustChangePassword
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->must_change_password) {
            if (!$request->routeIs('auth.change-password') && !$request->routeIs('logout')) {
                return redirect()->route('auth.change-password')
                    ->with('warning', 'Please change your password before continuing.');
            }
        }
        return $next($request);
    }
}
