<?php

use App\Enums\Campaign\SessionStatus;
use App\Models\Campaign;
use App\Models\CampaignSession;
use App\Models\User;

test('a game master can create a campaign with players and a schedule', function () {
    $game_master = User::factory()->gameMaster()->create();
    $players = User::factory()->player()->count(2)->create();

    $response = $this->actingAs($game_master)->post(route('campaigns.store'), [
        'title' => 'The Haunting',
        'description' => 'A short introductory scenario.',
        'player_ids' => $players->pluck('id')->all(),
        'sessions' => [
            ['title' => 'Session One', 'scheduled_at' => '2026-08-01T19:00'],
            ['title' => 'Session Two', 'scheduled_at' => '2026-08-08T19:00'],
        ],
    ]);

    $campaign = Campaign::query()->firstOrFail();

    $response->assertRedirect(route('campaigns.show', $campaign));

    expect($campaign->title)->toBe('The Haunting')
        ->and($campaign->game_master_id)->toBe($game_master->id)
        ->and($campaign->players)->toHaveCount(2)
        ->and($campaign->sessions)->toHaveCount(2)
        ->and($campaign->sessions->first()->status)->toBe(SessionStatus::Scheduled);
});

test('the create screen offers the players a campaign can include', function () {
    $game_master = User::factory()->gameMaster()->create();
    User::factory()->player()->count(2)->create();
    User::factory()->gameMaster()->create();

    $this->actingAs($game_master)
        ->get(route('campaigns.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Campaigns/Create')
            ->has('player_options', 2)
            ->has('session_status_options', 3)
        );
});

test('the edit screen is prefilled with the campaign, its players and its schedule', function () {
    $game_master = User::factory()->gameMaster()->create();
    $campaign = Campaign::factory()->create(['game_master_id' => $game_master->id]);
    $player = User::factory()->player()->create();
    $campaign->players()->attach($player);
    CampaignSession::factory()->create(['campaign_id' => $campaign->id]);

    $this->actingAs($game_master)
        ->get(route('campaigns.edit', $campaign))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Campaigns/Edit')
            ->where('campaign.title', $campaign->title)
            ->where('campaign.player_ids', [$player->id])
            ->has('campaign.sessions', 1)
        );
});

test('a player cannot create a campaign', function () {
    $player = User::factory()->player()->create();

    $this->actingAs($player)
        ->post(route('campaigns.store'), [
            'title' => 'Unauthorised Campaign',
        ])
        ->assertForbidden();

    expect(Campaign::query()->count())->toBe(0);
});

test('a campaign requires a title and valid session dates', function () {
    $game_master = User::factory()->gameMaster()->create();

    $this->actingAs($game_master)
        ->post(route('campaigns.store'), [
            'title' => '',
            'sessions' => [
                ['title' => 'No Date'],
            ],
        ])
        ->assertSessionHasErrors(['title', 'sessions.0.scheduled_at']);
});

test('the campaign index only lists campaigns a user belongs to', function () {
    $player = User::factory()->player()->create();
    $joined_campaign = Campaign::factory()->create(['title' => 'Joined']);
    $joined_campaign->players()->attach($player);

    Campaign::factory()->create(['title' => 'Someone Elses']);

    $this->actingAs($player)
        ->get(route('campaigns.index'))
        ->assertInertia(fn ($page) => $page
            ->component('Campaigns/Index')
            ->has('campaigns', 1)
            ->where('campaigns.0.title', 'Joined')
        );
});

test('an admin sees every campaign', function () {
    $admin = User::factory()->admin()->create();
    Campaign::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('campaigns.index'))
        ->assertInertia(fn ($page) => $page->has('campaigns', 3));
});

test('a user who is not a member cannot view a campaign', function () {
    $outsider = User::factory()->player()->create();
    $campaign = Campaign::factory()->create();

    $this->actingAs($outsider)
        ->get(route('campaigns.show', $campaign))
        ->assertForbidden();
});

test('a player who belongs to a campaign can view it but not edit it', function () {
    $player = User::factory()->player()->create();
    $campaign = Campaign::factory()->create();
    $campaign->players()->attach($player);

    $this->actingAs($player)
        ->get(route('campaigns.show', $campaign))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('can_update', false));

    $this->actingAs($player)
        ->get(route('campaigns.edit', $campaign))
        ->assertForbidden();
});

test('updating a campaign syncs its players and rewrites its schedule', function () {
    $game_master = User::factory()->gameMaster()->create();
    $campaign = Campaign::factory()->create(['game_master_id' => $game_master->id]);

    $original_player = User::factory()->player()->create();
    $replacement_player = User::factory()->player()->create();
    $campaign->players()->attach($original_player);

    $kept_session = CampaignSession::factory()->create(['campaign_id' => $campaign->id]);
    $dropped_session = CampaignSession::factory()->create(['campaign_id' => $campaign->id]);

    $this->actingAs($game_master)
        ->patch(route('campaigns.update', $campaign), [
            'title' => 'Masks of Nyarlathotep',
            'description' => null,
            'player_ids' => [$replacement_player->id],
            'sessions' => [
                [
                    'id' => $kept_session->id,
                    'title' => 'Rescheduled',
                    'scheduled_at' => '2026-09-01T18:30',
                    'status' => SessionStatus::Played->value,
                ],
                ['scheduled_at' => '2026-09-08T18:30'],
            ],
        ])
        ->assertRedirect(route('campaigns.show', $campaign));

    $campaign->refresh()->load('players', 'sessions');

    expect($campaign->title)->toBe('Masks of Nyarlathotep')
        ->and($campaign->players->pluck('id')->all())->toBe([$replacement_player->id])
        ->and($campaign->sessions)->toHaveCount(2)
        ->and($kept_session->refresh()->title)->toBe('Rescheduled')
        ->and($kept_session->status)->toBe(SessionStatus::Played)
        ->and($dropped_session->refresh()->trashed())->toBeTrue();
});

test('a game master cannot edit a campaign they do not run', function () {
    $other_game_master = User::factory()->gameMaster()->create();
    $campaign = Campaign::factory()->create();

    $this->actingAs($other_game_master)
        ->patch(route('campaigns.update', $campaign), ['title' => 'Hijacked'])
        ->assertForbidden();
});

test('a game master can delete their own campaign', function () {
    $game_master = User::factory()->gameMaster()->create();
    $campaign = Campaign::factory()->create(['game_master_id' => $game_master->id]);

    $this->actingAs($game_master)
        ->delete(route('campaigns.destroy', $campaign))
        ->assertRedirect(route('campaigns.index'));

    expect($campaign->refresh()->trashed())->toBeTrue();
});

test('an admin can edit and delete any campaign', function () {
    $admin = User::factory()->admin()->create();
    $campaign = Campaign::factory()->create();

    $this->actingAs($admin)
        ->patch(route('campaigns.update', $campaign), ['title' => 'Administered'])
        ->assertRedirect(route('campaigns.show', $campaign));

    expect($campaign->refresh()->title)->toBe('Administered');

    $this->actingAs($admin)
        ->delete(route('campaigns.destroy', $campaign))
        ->assertRedirect(route('campaigns.index'));
});

test('a session belonging to another campaign cannot be smuggled into an update', function () {
    $game_master = User::factory()->gameMaster()->create();
    $campaign = Campaign::factory()->create(['game_master_id' => $game_master->id]);
    $foreign_session = CampaignSession::factory()->create();

    $this->actingAs($game_master)
        ->patch(route('campaigns.update', $campaign), [
            'title' => 'Tampered',
            'sessions' => [
                ['id' => $foreign_session->id, 'scheduled_at' => '2026-09-01T18:30'],
            ],
        ])
        ->assertSessionHasErrors('sessions.0.id');
});
