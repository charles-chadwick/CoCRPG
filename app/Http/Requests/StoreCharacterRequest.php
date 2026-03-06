<?php

namespace App\Http\Requests;

use App\Enums\Character\Occupation;
use App\Enums\Character\Possession as PossessionType;
use App\Enums\Character\Stat;
use App\Enums\ModifierSign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCharacterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'occupation' => ['required', 'string', new Enum(Occupation::class)],
            'age' => ['required', 'integer', 'min:1', 'max:999'],
            'gender' => ['nullable', 'string', 'max:255'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'residence' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'stats' => ['required', 'array', 'size:9'],
            'stats.*.name' => ['required', 'string', new Enum(Stat::class)],
            'stats.*.value' => ['required', 'integer', 'min:1', 'max:100'],
            'skills' => ['required', 'array'],
            'skills.*.skill_id' => ['required', 'integer', 'exists:skills,id'],
            'skills.*.value' => ['required', 'integer', 'min:0', 'max:99'],
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
