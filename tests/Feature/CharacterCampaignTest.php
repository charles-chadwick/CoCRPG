<?php

use App\Enums\Character\Occupation;
use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;

test('a character can be assigned to a campaign the player belongs to', function () {
    $player = User::factory()->player()->create();
    $campaign = Campaign::factory()->create();
    $campaign->players()->attach($player);

    $character = Character::factory()->create(['user_id' => $player->id]);

    $this->actingAs($player)
        ->patch(route('characters.update', $character), [
            'campaign_id' => $campaign->id,
            'name' => $character->name,
            'occupation' => Occupation::Journalist->value,
            'age' => 34,
            'gender' => 'Female',
            'birthplace' => 'Arkham',
            'residence' => 'Boston',
        ])
        ->assertRedirect(route('characters.show', $character));

    expect($character->refresh()->campaign_id)->toBe($campaign->id);
});

test('a character cannot be assigned to a campaign the player does not belong to', function () {
    $player = User::factory()->player()->create();
    $foreign_campaign = Campaign::factory()->create();
    $character = Character::factory()->create(['user_id' => $player->id]);

    $this->actingAs($player)
        ->patch(route('characters.update', $character), [
            'campaign_id' => $foreign_campaign->id,
            'name' => $character->name,
            'occupation' => Occupation::Journalist->value,
            'age' => 34,
            'gender' => 'Female',
            'birthplace' => 'Arkham',
            'residence' => 'Boston',
        ])
        ->assertSessionHasErrors('campaign_id');

    expect($character->refresh()->campaign_id)->toBeNull();
});

test('the character form only offers campaigns the player belongs to', function () {
    $player = User::factory()->player()->create();
    $campaign = Campaign::factory()->create(['title' => 'Mine']);
    $campaign->players()->attach($player);
    Campaign::factory()->create(['title' => 'Not Mine']);

    $this->actingAs($player)
        ->get(route('characters.create'))
        ->assertInertia(fn ($page) => $page
            ->has('campaign_options', 1)
            ->where('campaign_options.0.label', 'Mine')
        );
});

test('deleting a campaign leaves its characters intact without a campaign', function () {
    $game_master = User::factory()->gameMaster()->create();
    $player = User::factory()->player()->create();
    $campaign = Campaign::factory()->create(['game_master_id' => $game_master->id]);
    $campaign->players()->attach($player);

    $character = Character::factory()->create([
        'user_id' => $player->id,
        'campaign_id' => $campaign->id,
    ]);

    $this->actingAs($game_master)->delete(route('campaigns.destroy', $campaign));

    expect($character->refresh()->exists)->toBeTrue();
});
