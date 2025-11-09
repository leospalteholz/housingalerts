<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HearingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->is_admin || auth()->user()->is_superuser;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'type' => 'required|in:development,policy',
            'description' => 'required|string',
            'remote_instructions' => 'required|string',
            'inperson_instructions' => 'required|string',
            'comments_email' => 'required|email|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'organization_id' => 'nullable|exists:organizations,id',
            'region_id' => 'nullable|exists:regions,id',
            'image' => 'nullable|image|mimes:jpeg,jpg,webp|max:2048', // 2MB max, JPEG and WebP only
            'more_info_url' => 'nullable|url',
            'approved' => 'nullable|boolean',
        ];

        // Add conditional validation based on hearing type
        if ($this->type === 'development') {
            $rules = array_merge($rules, [
                'street_address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'rental' => 'required|boolean',
                'units' => 'required|integer|min:1',
                'below_market_units' => 'required|integer|min:0',
                'replaced_units' => 'nullable|integer|min:0',
                'subject_to_vote' => 'required|boolean',
                'title' => 'nullable|string|max:255',
            ]);
        } else if ($this->type === 'policy') {
            $rules = array_merge($rules, [
                'title' => 'required|string|max:255',
                'street_address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'rental' => 'nullable|boolean',
                'units' => 'nullable|integer|min:1',
                'below_market_units' => 'nullable|integer|min:0',
                'replaced_units' => 'nullable|integer|min:0',
                'subject_to_vote' => 'nullable|boolean',
            ]);
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a JPEG or WebP file.',
            'image.max' => 'The image must be smaller than 2MB.',
        ];
    }
}
