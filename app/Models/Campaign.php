<?php

namespace App\Models;

use App\Enums\Campaign\SessionStatus;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Base
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    protected $fillable = [
        'game_master_id',
        'title',
        'description',
    ];

    public function gameMaster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'game_master_id');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'campaign_user')
            ->withTimestamps();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CampaignSession::class);
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    public function nextSession(): HasOne
    {
        return $this->hasOne(CampaignSession::class)
            ->where('status', SessionStatus::Scheduled)
            ->where('scheduled_at', '>=', now())
            ->oldest('scheduled_at');
    }
}
