<?php

namespace App\Models;

use App\Enums\PlayerPossession;
use Database\Factories\PossessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Possession extends Base
{
    /** @use HasFactory<PossessionFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'type' => PlayerPossession::class,
            'value' => 'integer',
        ];
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class)
            ->using(PlayerPossession::class)
            ->withPivot('modifier_sign', 'modifier')
            ->withTimestamps();
    }
}
