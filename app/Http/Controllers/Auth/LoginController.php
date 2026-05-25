<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited($request);

        $user = \App\Models\User::where('email', $request->email)
            ->with('role')
            ->first();

        // Check account lock
        if ($user && $user->isLocked()) {
            throw ValidationException::withMessages([
                'email' => 'Your account is locked due to too many failed attempts. Try again in 15 minutes.',
            ]);
        }

        // Check user status
        if ($user && $user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact your administrator.',
            ]);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            if ($user) {
                $user->incrementFailedAttempts();
            }

            AuditLog::record('login_failed', null, [], ['email' => $request->email]);

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $user = Auth::user();
        $user->resetFailedAttempts();

        AuditLog::record('login', $user);

        $request->session()->regenerate();

        // Force password change on first login
        if ($user->must_change_password) {
            return redirect()->route('auth.change-password')
                ->with('warning', 'You must change your password before continuing.');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        AuditLog::record('logout', Auth::user());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required', 'confirmed', 'min:8',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',
            ],
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase letters and numbers.',
        ]);

        $user = Auth::user();
        $user->update([
            'password'            => bcrypt($request->password),
            'must_change_password' => false,
        ]);

        AuditLog::record('password_changed', $user);

        return redirect()->route('dashboard')->with('success', 'Password changed successfully. Welcome!');
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->email) . '|' . $request->ip());
    }
}
