<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PossessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'modifier_sign' => $this->pivot->modifier_sign?->value,
            'modifier' => $this->pivot->modifier,
        ];
    }
}
