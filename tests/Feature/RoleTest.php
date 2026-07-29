<?php

use App\Enums\Role;
use App\Models\User;

test('a newly registered user becomes a Player', function () {
    $this->seed(Database\Seeders\RoleSeeder::class);

    $this->post(route('register'), [
        'name' => 'New Investigator',
        'email' => 'investigator@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::query()->where('email', 'investigator@example.com')->firstOrFail();

    expect($user->isPlayer())->toBeTrue()
        ->and($user->isGameMaster())->toBeFalse()
        ->and($user->isAdmin())->toBeFalse();
});

test('role helpers reflect the assigned role', function (string $state, string $expected_role) {
    $user = User::factory()->{$state}()->create();

    expect($user->getRoleNames()->all())->toBe([$expected_role]);
})->with([
    ['admin', Role::Admin->value],
    ['gameMaster', Role::GameMaster->value],
    ['player', Role::Player->value],
]);

test('the shared inertia props expose the users roles', function () {
    $game_master = User::factory()->gameMaster()->create();

    $this->actingAs($game_master)
        ->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page
            ->where('auth.user.is_game_master', true)
            ->where('auth.user.is_admin', false)
            ->where('auth.user.roles', [Role::GameMaster->value])
        );
});
