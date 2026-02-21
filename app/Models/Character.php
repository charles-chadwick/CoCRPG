<?php

namespace App\Models;

use App\Enums\CharacterClass;
use App\Enums\Race;
use Database\Factories\CharacterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Character extends Base
{
    /** @use HasFactory<CharacterFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'race',
        'character_class',
        'level',
        'experience_points',
    ];

    protected function casts(): array
    {
        return [
            'race' => Race::class,
            'character_class' => CharacterClass::class,
            'level' => 'integer',
            'experience_points' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(Stat::class);
    }

    public function possessions(): BelongsToMany
    {
        return $this->belongsToMany(Possession::class)
            ->using(CharacterPossession::class)
            ->withPivot('modifier_sign', 'modifier')
            ->withTimestamps();
    }
}
