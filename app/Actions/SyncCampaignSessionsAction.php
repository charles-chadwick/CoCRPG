<?php

namespace App\Actions;

use App\Enums\Campaign\SessionStatus;
use App\Models\Campaign;
use App\Models\CampaignSession;

class SyncCampaignSessionsAction
{
    /**
     * Replace a campaign's schedule with the given sessions, keeping any
     * existing session that was submitted back with its id.
     *
     * @param  array<int, array{id?: int|null, title?: string|null, scheduled_at: string, status?: string|null, notes?: string|null}>  $sessions
     */
    public function handle(Campaign $campaign, array $sessions): void
    {
        $retained_session_ids = [];

        foreach ($sessions as $session_data) {
            $attributes = [
                'title' => $session_data['title'] ?? null,
                'scheduled_at' => $session_data['scheduled_at'],
                'status' => $session_data['status'] ?? SessionStatus::Scheduled->value,
                'notes' => $session_data['notes'] ?? null,
            ];

            $session = isset($session_data['id'])
                ? tap($campaign->sessions()->findOrFail($session_data['id']))->update($attributes)
                : $campaign->sessions()->create($attributes);

            $retained_session_ids[] = $session->id;
        }

        $campaign->sessions()
            ->whereNotIn('id', $retained_session_ids)
            ->each(fn (CampaignSession $session) => $session->delete());
    }
}
