<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Create a new user, generate a temporary password, and send welcome email.
     */
    public function createUser(array $data): User
    {
        $tempPassword = $this->generateTempPassword();

        $user = User::create(array_merge($data, [
            'password'            => bcrypt($tempPassword),
            'must_change_password' => true,
        ]));

        // Send welcome email with temp password
        try {
            Mail::send('emails.welcome', [
                'user'     => $user,
                'password' => $tempPassword,
            ], function ($m) use ($user) {
                $m->to($user->email, $user->name)
                  ->subject('Welcome to Odyssey Elevators — Proposal Management System');
            });
        } catch (\Exception $e) {
            \Log::warning("Failed to send welcome email to {$user->email}: " . $e->getMessage());
        }

        return $user;
    }

    /**
     * Force a password reset link for an existing user.
     */
    public function sendPasswordReset(User $user): void
    {
        $tempPassword = $this->generateTempPassword();

        $user->update([
            'password'            => bcrypt($tempPassword),
            'must_change_password' => true,
        ]);

        try {
            Mail::send('emails.password-reset-admin', [
                'user'     => $user,
                'password' => $tempPassword,
            ], function ($m) use ($user) {
                $m->to($user->email, $user->name)
                  ->subject('Odyssey PMS — Your Password Has Been Reset');
            });
        } catch (\Exception $e) {
            \Log::warning("Failed to send reset email to {$user->email}: " . $e->getMessage());
        }
    }

    private function generateTempPassword(): string
    {
        // Format: Odyssey@XXXXXX (meets complexity requirements)
        return 'Odyssey@' . Str::upper(Str::random(3)) . rand(100, 999);
    }
}
