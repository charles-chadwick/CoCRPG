<?php

namespace App\Actions;

use App\Models\User;

class UpdateUserProfileAction
{
    /**
     * Update the user's profile, resetting email verification when the address changes.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): User
    {
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }
}
