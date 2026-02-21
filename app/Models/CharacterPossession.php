<?php

namespace App\Models;

use App\Enums\ModifierSign;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CharacterPossession extends Pivot
{
    protected function casts(): array
    {
        return [
            'modifier_sign' => ModifierSign::class,
            'modifier' => 'integer',
        ];
    }
}
