<?php

namespace App\Models;

use App\Enums\Character\Occupation;
use App\Http\Resources\PossessionResource;
use Database\Factories\CharacterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Character extends Base
{
    /** @use HasFactory<CharacterFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'occupation',
        'age',
        'gender',
        'birthplace',
        'residence',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'occupation' => Occupation::class,
            'age' => 'integer',
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

    public function characterSkills(): HasMany
    {
        return $this->hasMany(CharacterSkill::class);
    }

    public function possessions(): BelongsToMany
    {
        return $this->belongsToMany(Possession::class, 'character_possessions')
            ->using(CharacterPossession::class)
            ->withPivot('modifier_sign', 'modifier')
            ->withTimestamps();
    }

    public function groupedPossessions(): Collection
    {
        return $this->possessions
            ->groupBy(fn ($possession) => $possession->type->value)
            ->map(fn ($group) => PossessionResource::collection($group)->resolve());
    }
}
