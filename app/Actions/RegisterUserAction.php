<?php

namespace App\Actions;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as PermissionRole;

class RegisterUserAction
{
    /**
     * Create a newly registered user, give them the Player role, and log them in.
     *
     * @param  array{name: string, email: string, password: string}  $data
     */
    public function handle(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole(PermissionRole::findOrCreate(Role::Player->value));

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }
}
