<?php

namespace App\Models;

use App\Enums\Character\Skill as SkillEnum;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Base
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    protected function casts(): array
    {
        return [
            'name' => SkillEnum::class,
        ];
    }

    public function characterSkills(): HasMany
    {
        return $this->hasMany(CharacterSkill::class);
    }
}
