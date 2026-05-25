<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'site_name' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'footer_logo' => ['nullable', 'image', 'max:4096'],
            'favicon' => ['nullable', 'image', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:64'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'newsletter_heading' => ['nullable', 'string', 'max:255'],
            'newsletter_text' => ['nullable', 'string', 'max:500'],
            'announcement_messages' => ['nullable', 'string', 'max:2000'],
            'hero_heading' => ['nullable', 'string', 'max:255'],
            'hero_subheading' => ['nullable', 'string', 'max:500'],
            'hero_image' => ['nullable', 'image', 'max:8192'],
            'hero_cta_text' => ['nullable', 'string', 'max:64'],
            'hero_cta_link' => ['nullable', 'string', 'max:255'],
            'seo_default_title' => ['nullable', 'string', 'max:255'],
            'seo_default_description' => ['nullable', 'string', 'max:500'],
            'analytics_scripts' => ['nullable', 'string', 'max:5000'],
            'footer_text' => ['nullable', 'string', 'max:1000'],
            'map_embed' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
