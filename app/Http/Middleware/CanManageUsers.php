<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanManageUsers
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->canManageUsers()) {
            abort(403, 'Only Super Admins can manage users.');
        }
        return $next($request);
    }
}
