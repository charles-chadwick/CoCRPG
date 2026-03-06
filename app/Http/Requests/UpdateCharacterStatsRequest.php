<?php

namespace App\Http\Requests;

use App\Enums\Character\Stat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCharacterStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stats' => ['required', 'array', 'size:9'],
            'stats.*.name' => ['required', 'string', new Enum(Stat::class)],
            'stats.*.value' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
