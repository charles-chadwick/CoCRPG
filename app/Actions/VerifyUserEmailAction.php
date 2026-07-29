<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Auth\Events\Verified;

class VerifyUserEmailAction
{
    /**
     * Mark the user's email address as verified if it is not already.
     */
    public function handle(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
    }
}
