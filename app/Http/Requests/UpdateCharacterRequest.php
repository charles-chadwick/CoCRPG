<?php

namespace App\Http\Requests;

use App\Enums\Character\Occupation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateCharacterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * A character may only be attached to a campaign the user actually belongs to.
     *
     * @return array<int, int>
     */
    protected function campaignIdsAvailableToUser(): array
    {
        return $this->user()->campaigns()->pluck('campaigns.id')->all();
    }

    public function rules(): array
    {
        return [
            'campaign_id' => ['nullable', 'integer', Rule::in($this->campaignIdsAvailableToUser())],
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
