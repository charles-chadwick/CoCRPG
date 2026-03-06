<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCharacterSkillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skills' => ['required', 'array'],
            'skills.*.skill_id' => ['required', 'integer', 'exists:skills,id'],
            'skills.*.value' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }
}
