<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateAboutPageRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

/**
 * About page content (What we do / Our philosophy / Our four bones).
 * Stored as settings: about_* keys hold English, about_*_el the Greek override.
 * The public page falls back to the built-in copy when a key is empty.
 */
class AboutPageController extends AdminController
{
    public const FILE_KEYS = ['about_what_image', 'about_philosophy_image'];

    /** Text keys (each has an `_el` twin). */
    public const TEXT_KEYS = [
        'about_what_label', 'about_what_heading', 'about_what_lead', 'about_what_body',
        'about_philosophy_label', 'about_philosophy_heading', 'about_philosophy_lead', 'about_philosophy_body',
        'about_bones_label', 'about_bones_heading',
        'about_bone_1_title', 'about_bone_1_text',
        'about_bone_2_title', 'about_bone_2_text',
        'about_bone_3_title', 'about_bone_3_text',
        'about_bone_4_title', 'about_bone_4_text',
    ];

    public function edit()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $settings = Setting::all_cached();
        return view('admin.about.edit', compact('settings'));
    }

    public function update(UpdateAboutPageRequest $request)
    {
        $data = $request->validated();

        foreach (self::FILE_KEYS as $fileKey) {
            $existing = Setting::get($fileKey);

            if ($request->hasFile($fileKey)) {
                if ($existing) Storage::disk('public')->delete($existing);
                $data[$fileKey] = $request->file($fileKey)->store('settings', 'public');
            } elseif ($request->boolean('__remove_'.$fileKey)) {
                if ($existing) Storage::disk('public')->delete($existing);
                $data[$fileKey] = null;
            } else {
                unset($data[$fileKey]);
            }
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.about.edit')->with('success', 'About page saved.');
    }
}
