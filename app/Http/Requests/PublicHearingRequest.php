<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicHearingRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $normalizeToNull = [
            'title',
            'street_address',
            'postal_code',
            'units',
            'below_market_units',
            'replaced_units',
            'more_info_url',
        ];

        $replacements = [];

        foreach ($normalizeToNull as $field) {
            if (!$this->filled($field)) {
                $replacements[$field] = null;
            }
        }

        if ($this->has('subject_to_vote')) {
            $replacements['subject_to_vote'] = $this->boolean('subject_to_vote');
        }

        if ($this->has('rental')) {
            $replacements['rental'] = $this->boolean('rental');
        }

        $this->merge($replacements);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $organization = $this->route('organization');

        $rules = [
            'type' => ['required', Rule::in(['development', 'policy'])],
            'description' => ['required', 'string'],
            'remote_instructions' => ['required', 'string'],
            'inperson_instructions' => ['required', 'string'],
            'comments_email' => ['required', 'email', 'max:255'],
            'start_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'subject_to_vote' => ['required', 'boolean'],
            'region_id' => [
                'required',
                Rule::exists('regions', 'id')->where(function ($query) use ($organization) {
                    if ($organization) {
                        $query->where('organization_id', $organization->id);
                    }
                }),
            ],
            'more_info_url' => ['nullable', 'url'],
        ];

        $type = $this->input('type');

        if ($type === 'development') {
            $rules = array_merge($rules, [
                'street_address' => ['required', 'string', 'max:255'],
                'postal_code' => ['required', 'string', 'max:20'],
                'rental' => ['required', 'boolean'],
                'units' => ['required', 'integer', 'min:1'],
                'below_market_units' => ['required', 'integer', 'min:0'],
                'replaced_units' => ['nullable', 'integer', 'min:0'],
                'title' => ['nullable', 'string', 'max:255'],
            ]);
        } else {
            $rules = array_merge($rules, [
                'title' => ['required', 'string', 'max:255'],
                'street_address' => ['nullable', 'string', 'max:255'],
                'postal_code' => ['nullable', 'string', 'max:20'],
                'rental' => ['nullable', 'boolean'],
                'units' => ['nullable', 'integer', 'min:1'],
                'below_market_units' => ['nullable', 'integer', 'min:0'],
                'replaced_units' => ['nullable', 'integer', 'min:0'],
            ]);
        }

        return $rules;
    }
}
