<?php

namespace App\Models;

use App\Enums\Character\Skill as SkillEnum;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Base
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    protected $table = 'character_skills';

    protected $fillable = [
        'character_id',
        'name',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'name'  => SkillEnum::class,
            'value' => 'integer',
        ];
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
