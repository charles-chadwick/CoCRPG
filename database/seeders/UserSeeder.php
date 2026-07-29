<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory()->gameMaster()->create([
            'name' => 'Keeper User',
            'email' => 'keeper@example.com',
        ]);

        User::factory()->player()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->player()->count(4)->create();
    }
}
