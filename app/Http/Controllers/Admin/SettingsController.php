<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends AdminController
{
    private const FILE_KEYS = ['logo', 'footer_logo', 'favicon', 'hero_image'];

    public function edit()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        $settings = Setting::all_cached();
        return view('admin.settings.edit', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request)
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

        return redirect()->route('admin.settings.edit')->with('success', 'Settings saved.');
    }

}
