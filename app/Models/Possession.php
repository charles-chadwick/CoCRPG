<?php

namespace App\Models;

use App\Enums\CharacterPossession;
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
            'type' => CharacterPossession::class,
            'value' => 'integer',
        ];
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class)
            ->using(CharacterPossession::class)
            ->withPivot('modifier_sign', 'modifier')
            ->withTimestamps();
    }
}
