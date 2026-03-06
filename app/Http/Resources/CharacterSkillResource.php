<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CharacterSkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->skill->name->value,
            'value' => $this->value,
            'base' => $this->skill->name->baseValue(),
        ];
    }
}
