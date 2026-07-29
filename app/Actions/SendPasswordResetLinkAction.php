<?php

namespace App\Actions;

use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkAction
{
    /**
     * Send the password reset link and return the resulting status key.
     */
    public function handle(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }
}
