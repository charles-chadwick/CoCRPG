<?php

namespace App\Http\Requests;

use App\Enums\Campaign\SessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'player_ids' => ['nullable', 'array'],
            'player_ids.*' => ['integer', 'exists:users,id'],
            'sessions' => ['nullable', 'array'],
            'sessions.*.title' => ['nullable', 'string', 'max:255'],
            'sessions.*.scheduled_at' => ['required', 'date'],
            'sessions.*.status' => ['nullable', 'string', new Enum(SessionStatus::class)],
            'sessions.*.notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A campaign needs a title.',
            'sessions.*.scheduled_at.required' => 'Every scheduled session needs a date.',
            'sessions.*.scheduled_at.date' => 'Session dates must be valid dates.',
        ];
    }
}
