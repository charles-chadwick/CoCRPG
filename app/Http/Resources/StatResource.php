<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name->value,
            'value' => $this->value,
            'half' => $this->halfValue(),
            'fifth' => $this->fifthValue(),
        ];
    }
}
