<?php

namespace App\Http\Requests;

use App\Enums\Character\Possession as PossessionType;
use App\Enums\ModifierSign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCharacterPossessionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'possessions' => ['nullable', 'array'],
            'possessions.*.possession_id' => ['required', 'integer', 'exists:possessions,id'],
            'possessions.*.modifier_sign' => ['nullable', 'string', new Enum(ModifierSign::class)],
            'possessions.*.modifier' => ['nullable', 'integer'],
            'new_possessions' => ['nullable', 'array'],
            'new_possessions.*.type' => ['required', 'string', new Enum(PossessionType::class)],
            'new_possessions.*.name' => ['required', 'string', 'max:255'],
            'new_possessions.*.value' => ['required', 'integer', 'min:0'],
            'new_possessions.*.modifier_sign' => ['nullable', 'string', new Enum(ModifierSign::class)],
            'new_possessions.*.modifier' => ['nullable', 'integer'],
        ];
    }
}
