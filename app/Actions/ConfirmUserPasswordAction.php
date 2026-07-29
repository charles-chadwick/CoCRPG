<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ConfirmUserPasswordAction
{
    /**
     * Verify the user's password and mark it as confirmed for this session.
     */
    public function handle(User $user, string $password): bool
    {
        $isValid = Auth::guard('web')->validate([
            'email' => $user->email,
            'password' => $password,
        ]);

        if (! $isValid) {
            return false;
        }

        session()->put('auth.password_confirmed_at', time());

        return true;
    }
}
