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
            'description',
            'remote_instructions',
            'inperson_instructions',
            'comments_email',
            'start_time',
            'end_time',
            'vote_date',
            'notes',
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

        if ($this->has('passed')) {
            $replacements['passed'] = $this->boolean('passed');
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
            'description' => ['nullable', 'string'],
            'remote_instructions' => ['nullable', 'string'],
            'inperson_instructions' => ['nullable', 'string'],
            'comments_email' => ['nullable', 'email', 'max:255'],
            'start_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'subject_to_vote' => ['required', 'boolean'],
            'vote_date' => ['nullable', 'date'],
            'passed' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
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
