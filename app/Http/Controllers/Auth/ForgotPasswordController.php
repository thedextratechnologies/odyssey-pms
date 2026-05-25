<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Send reset email
        Mail::send('emails.password-reset', ['token' => $token, 'email' => $request->email], function ($m) use ($request) {
            $m->to($request->email)->subject('Odyssey PMS — Password Reset Request');
        });

        return back()->with('success', 'Password reset link has been sent to your email.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required',
            'password' => 'required|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'This reset link is invalid or has expired.']);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            return back()->withErrors(['token' => 'This reset link has expired. Please request a new one.']);
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        $user->update([
            'password'            => bcrypt($request->password),
            'must_change_password' => false,
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        AuditLog::record('password_reset', $user);

        return redirect()->route('login')->with('success', 'Password reset successfully. You can now log in.');
    }
}
