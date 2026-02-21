<?php

namespace App\Models;

use App\Enums\ModifierSign;
use App\Enums\PlayerPossession;
use Database\Factories\PossessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Possession extends Base
{
    /** @use HasFactory<PossessionFactory> */
    use HasFactory;

    protected $fillable = [
        'player_id',
        'name',
        'value',
        'modifier_sign',
        'modifier',
    ];

    protected function casts(): array
    {
        return [
            'name' => PlayerPossession::class,
            'value' => 'integer',
            'modifier_sign' => ModifierSign::class,
            'modifier' => 'integer',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
