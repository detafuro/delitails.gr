<?php

namespace App\Http\Requests\Admin;

use App\Http\Controllers\Admin\AboutPageController;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $rules = [];

        foreach (AboutPageController::TEXT_KEYS as $key) {
            $max = str_ends_with($key, '_body') ? 5000 : (str_ends_with($key, '_lead') || str_ends_with($key, '_text') ? 1000 : 255);
            $rules[$key] = ['nullable', 'string', 'max:'.$max];
            $rules[$key.'_el'] = ['nullable', 'string', 'max:'.$max];
        }

        foreach (AboutPageController::FILE_KEYS as $key) {
            $rules[$key] = ['nullable', 'image', 'max:5120'];
        }

        return $rules;
    }
}
