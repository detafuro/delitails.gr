<?php

namespace App\Translation;

use App\Models\Translation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Translation\FileLoader;

/**
 * Overlays admin-edited translations (translations table) on top of the
 * lang/{locale}.json strings. Done at the loader level so keys containing
 * dots (full sentences) work — Lang::addLines() would split them into
 * nested arrays and the override would never match.
 */
class DatabaseTranslationLoader extends FileLoader
{
    public function load($locale, $group, $namespace = null)
    {
        $lines = parent::load($locale, $group, $namespace);

        if ($group === '*' && $namespace === '*') {
            return array_replace($lines, $this->overrides($locale));
        }

        return $lines;
    }

    private function overrides(string $locale): array
    {
        try {
            return Schema::hasTable('translations') ? Translation::overrides($locale) : [];
        } catch (\Throwable) {
            return [];
        }
    }
}
