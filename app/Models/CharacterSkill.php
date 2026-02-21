<?php

namespace App\Models;

use Database\Factories\CharacterSkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterSkill extends Base
{
    /** @use HasFactory<CharacterSkillFactory> */
    use HasFactory;

    protected $table = 'character_skills';

    protected $fillable = [
        'character_id',
        'skill_id',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
        ];
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
