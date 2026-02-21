<?php

namespace App\Models;

use App\Enums\PlayerStat as StatEnum;
use Database\Factories\StatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stat extends Base
{
    /** @use HasFactory<StatFactory> */
    use HasFactory;

    protected $fillable = [
        'player_id',
        'name',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'name' => StatEnum::class,
            'value' => 'integer',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function halfValue(): int
    {
        return intdiv($this->value, 2);
    }

    public function fifthValue(): int
    {
        return intdiv($this->value, 5);
    }
}
