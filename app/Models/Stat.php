<?php

namespace App\Models;

use App\Enums\Character\Stat as StatEnum;
use Database\Factories\StatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stat extends Base
{
    /** @use HasFactory<StatFactory> */
    use HasFactory;

    protected $fillable = [
        'character_id',
        'name',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'name' => StatEnum::class,
            'value' => 'integer',
        ];
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function halfValue(): int
    {
        return intdiv($this->value, 2);
    }

    public function fifthValue(): int
    {
        return intdiv($this->value, 5);
    }
}
