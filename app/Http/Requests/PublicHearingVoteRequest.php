<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicHearingVoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vote_date' => ['required', 'date'],
            'passed' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
