<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetUserPasswordAction
{
    /**
     * Reset the user's password and return the resulting status key.
     *
     * @param  array{email: string, password: string, password_confirmation: string, token: string}  $credentials
     */
    public function handle(array $credentials): string
    {
        return Password::reset($credentials, function (User $user) use ($credentials): void {
            $user->forceFill([
                'password' => Hash::make($credentials['password']),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });
    }
}
