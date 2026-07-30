<?php

namespace App\Http\Controllers\Admin;

use App\Models\Translation;
use Illuminate\Http\Request;

class TranslationController extends AdminController
{
    private const LOCALE = 'el';

    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $defaults = $this->defaults();
        $overrides = Translation::query()
            ->where('locale', self::LOCALE)
            ->pluck('value', 'key_hash');

        $strings = collect($defaults)->map(fn ($default, $key) => [
            'hash' => sha1($key),
            'source' => $key,
            'default' => $default,
            'value' => $overrides[sha1($key)] ?? $default,
            'overridden' => isset($overrides[sha1($key)]),
        ])->values();

        return view('admin.translations.index', compact('strings'));
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $posted = $request->input('t', []);
        $changed = 0;

        foreach ($this->defaults() as $key => $default) {
            $hash = sha1($key);
            if (! array_key_exists($hash, $posted)) {
                continue;
            }

            $value = trim((string) $posted[$hash]);

            if ($value === '' || $value === $default) {
                $changed += Translation::query()
                    ->where('locale', self::LOCALE)->where('key_hash', $hash)->delete();
            } else {
                $existing = Translation::query()
                    ->where('locale', self::LOCALE)->where('key_hash', $hash)->first();
                if (! $existing || $existing->value !== $value) {
                    Translation::updateOrCreate(
                        ['locale' => self::LOCALE, 'key_hash' => $hash],
                        ['key' => $key, 'value' => $value]
                    );
                    $changed++;
                }
            }
        }

        Translation::flushCache(self::LOCALE);

        return redirect()->route('admin.translations.index')
            ->with('success', $changed.' translation(s) updated.');
    }

    /** Source strings (English) => default Greek translation, from lang/el.json. */
    private function defaults(): array
    {
        $path = lang_path(self::LOCALE.'.json');

        return file_exists($path) ? (json_decode(file_get_contents($path), true) ?: []) : [];
    }
}
