<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MustChangePassword {
    public function handle(Request $request, Closure $next) {
        if (Auth::check() && Auth::user()->must_change_password) {
            $allowed = ['auth.change-password', 'logout', 'password.email', 'password.request'];
            if (!$request->routeIs(...$allowed) && !$request->is('change-password')) {
                return redirect()->route('auth.change-password');
            }
        }
        return $next($request);
    }
}
