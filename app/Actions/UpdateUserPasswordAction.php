<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordAction
{
    public function handle(User $user, string $password): User
    {
        $user->update([
            'password' => Hash::make($password),
        ]);

        return $user;
    }
}
