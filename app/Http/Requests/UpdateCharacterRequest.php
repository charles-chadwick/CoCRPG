<?php

namespace App\Http\Requests;

use App\Enums\Character\Occupation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCharacterRequest extends FormRequest
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
            'gender' => ['required', 'string', 'max:255'],
            'birthplace' => ['required', 'string', 'max:255'],
            'residence' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
