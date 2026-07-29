<?php

namespace App\Actions;

use App\Models\Campaign;

class DeleteCampaignAction
{
    public function handle(Campaign $campaign): void
    {
        $campaign->delete();
    }
}
