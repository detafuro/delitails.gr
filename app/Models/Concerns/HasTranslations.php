<?php

namespace App\Models\Concerns;

use App\Models\ContentTranslation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Bilingual content: base columns hold English, per-locale overrides live in
 * content_translations. Models define the translatable columns in $translatable.
 */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::deleting(fn ($model) => $model->translations()->delete());
    }

    public function initializeHasTranslations(): void
    {
        $this->with[] = 'translations';
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(ContentTranslation::class, 'translatable');
    }

    /** The field's value in the current locale, falling back to the base (English) column. */
    public function t(string $field): mixed
    {
        $locale = app()->getLocale();

        if ($locale === config('app.fallback_locale', 'en')) {
            return $this->getAttribute($field);
        }

        $value = $this->translation($field, $locale);

        return ($value !== null && $value !== '') ? $value : $this->getAttribute($field);
    }

    /** The stored translation itself (no fallback) — used by admin forms. */
    public function translation(string $field, string $locale = 'el'): ?string
    {
        return $this->translations
            ->first(fn ($tr) => $tr->locale === $locale && $tr->field === $field)
            ?->value;
    }

    /** Upsert posted translations (e.g. the el[...] fields); empty values delete the row. */
    public function saveTranslations(?array $values, string $locale = 'el'): void
    {
        $values = array_intersect_key((array) $values, array_flip($this->translatable ?? []));

        foreach ($values as $field => $value) {
            $value = is_string($value) ? trim($value) : $value;

            if ($value === null || $value === '') {
                $this->translations()->where(['locale' => $locale, 'field' => $field])->delete();
            } else {
                $this->translations()->updateOrCreate(
                    ['locale' => $locale, 'field' => $field],
                    ['value' => $value]
                );
            }
        }

        $this->unsetRelation('translations');
    }
}
