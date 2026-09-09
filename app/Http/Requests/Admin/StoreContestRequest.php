<?php

namespace App\Http\Requests\Admin;

use App\Models\Contest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreContestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('contest')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('contests', 'slug')->ignore($id)],
            'prize' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'winner_message' => ['nullable', 'string'],
            'banner_image' => ['nullable', 'image', 'max:8192'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'auto_draw' => ['nullable', 'boolean'],
            'winners_count' => ['required', 'integer', 'min:1', 'max:20'],
            'runners_up_count' => ['required', 'integer', 'min:0', 'max:20'],
            'phone_field' => ['required', Rule::in([Contest::PHONE_OFF, Contest::PHONE_OPTIONAL, Contest::PHONE_REQUIRED])],
            'newsletter_opt_in' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],

            // Repeater rows; blank labels are dropped in the controller.
            'fields' => ['nullable', 'array', 'max:12'],
            'fields.*.label' => ['nullable', 'string', 'max:120'],
            'fields.*.label_el' => ['nullable', 'string', 'max:120'],
            'fields.*.key' => ['nullable', 'string', 'max:60'],
            'fields.*.type' => ['nullable', Rule::in(array_keys(Contest::FIELD_TYPES))],
            'fields.*.options' => ['nullable', 'string', 'max:2000'],
            'fields.*.required' => ['nullable', 'boolean'],

            'el' => ['nullable', 'array'],
        ];
    }

    /**
     * The two dates are compared here rather than with `after:starts_at`, which
     * parses the rule parameter itself and blows up on a malformed value.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $start = $this->asDate($this->input('starts_at'));
            $end = $this->asDate($this->input('ends_at'));

            if ($start && $end && $end->lessThanOrEqualTo($start)) {
                $validator->errors()->add('ends_at', 'The closing date must be after the opening date.');
            }
        });
    }

    private function asDate(mixed $value): ?Carbon
    {
        try {
            return $value ? Carbon::parse($value) : null;
        } catch (\Throwable) {
            return null; // the `date` rule already reports this
        }
    }
}
