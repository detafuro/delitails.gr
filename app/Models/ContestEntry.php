<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ContestEntry extends Model
{
    protected $fillable = [
        'contest_id', 'name', 'email', 'phone', 'extra',
        'accepted_terms', 'marketing_consent', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'extra' => 'array',
        'accepted_terms' => 'boolean',
        'marketing_consent' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function isWinner(): bool
    {
        return $this->award_rank === 1;
    }

    /**
     * GDPR-safe public name: first name in full, every following word as an
     * initial — "Μαρία Παπαδοπούλου" becomes "Μαρία Π.".
     */
    public function getMaskedNameAttribute(): string
    {
        $parts = preg_split('/\s+/u', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (! $parts) {
            return __('Anonymous');
        }

        $first = array_shift($parts);
        $initials = array_map(fn ($p) => Str::upper(Str::substr($p, 0, 1)).'.', $parts);

        return trim($first.' '.implode(' ', $initials));
    }

    /** j***@example.com — used in the admin draw log, never on the public page. */
    public function getMaskedEmailAttribute(): string
    {
        [$user, $domain] = array_pad(explode('@', (string) $this->email, 2), 2, '');

        return Str::substr($user, 0, 1).str_repeat('*', max(3, Str::length($user) - 1)).($domain ? '@'.$domain : '');
    }

    /** The value the entrant gave for one of the contest's extra fields. */
    public function extraValue(string $key): ?string
    {
        $value = ($this->extra ?? [])[$key] ?? null;

        if (is_bool($value)) {
            return $value ? __('Yes') : __('No');
        }

        return $value === null || $value === '' ? null : (string) $value;
    }
}
