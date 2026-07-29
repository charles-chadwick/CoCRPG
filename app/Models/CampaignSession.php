<?php

namespace App\Models;

use App\Enums\Campaign\SessionStatus;
use Database\Factories\CampaignSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignSession extends Base
{
    /** @use HasFactory<CampaignSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'title',
        'scheduled_at',
        'status',
        'notes',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'status' => SessionStatus::class,
        ];
    }
}
