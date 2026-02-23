<?php

namespace App\Http\Resources;

use App\Enums\Character\Skill as SkillEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CharacterSkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'base' => SkillEnum::tryFrom($this->name)?->baseValue() ?? 0,
        ];
    }
}
