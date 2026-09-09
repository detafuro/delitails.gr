<?php

namespace App\Http\Requests;

use App\Models\Contest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContestEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Contest $contest */
        $contest = $this->route('contest');

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required', 'email', 'max:255',
                // One entry per email per contest.
                Rule::unique('contest_entries', 'email')->where('contest_id', $contest->id),
            ],
            'phone' => [$contest->phoneRequired() ? 'required' : 'nullable', 'string', 'max:64'],
            'accept_terms' => ['accepted'],
            'marketing_consent' => ['nullable', 'boolean'],
            'hp_field' => ['nullable', 'size:0'], // honeypot
        ];

        if (! $contest->collectsPhone()) {
            $rules['phone'] = ['nullable', 'string', 'max:64'];
        }

        foreach ($contest->fieldDefinitions() as $field) {
            $key = 'extra.'.$field['key'];
            $required = $field['required'];

            $rules[$key] = match ($field['type']) {
                'checkbox' => [$required ? 'accepted' : 'nullable'],
                'select' => [$required ? 'required' : 'nullable', 'string', Rule::in($field['options'])],
                'textarea' => [$required ? 'required' : 'nullable', 'string', 'max:2000'],
                default => [$required ? 'required' : 'nullable', 'string', 'max:255'],
            };
        }

        return $rules;
    }

    public function attributes(): array
    {
        /** @var Contest $contest */
        $contest = $this->route('contest');

        $names = [
            'name' => __('Your name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'accept_terms' => __('Terms & conditions'),
        ];

        foreach ($contest->fieldDefinitions() as $field) {
            $names['extra.'.$field['key']] = $field['label'];
        }

        return $names;
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('This email has already entered this contest.'),
            'accept_terms.accepted' => __('Please accept the terms to enter.'),
        ];
    }
}
