<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Contest extends Model
{
    use HasTranslations;

    protected array $translatable = [
        'title', 'prize', 'excerpt', 'description', 'terms', 'winner_message',
        'seo_title', 'seo_description',
    ];

    /** Lifecycle, derived from the dates + the draw — never stored, so it cannot drift. */
    public const STATE_DRAFT = 'draft';

    public const STATE_SCHEDULED = 'scheduled';

    public const STATE_ACTIVE = 'active';

    public const STATE_ENDED = 'ended';

    public const STATE_COMPLETED = 'completed';

    public const PHONE_OFF = 'off';

    public const PHONE_OPTIONAL = 'optional';

    public const PHONE_REQUIRED = 'required';

    public const FIELD_TYPES = [
        'text' => 'Single line',
        'textarea' => 'Paragraph',
        'select' => 'Dropdown',
        'checkbox' => 'Checkbox',
    ];

    protected $fillable = [
        'title', 'slug', 'prize', 'excerpt', 'description', 'terms', 'winner_message',
        'banner_image', 'starts_at', 'ends_at', 'is_published', 'auto_draw',
        'winners_count', 'runners_up_count', 'phone_field', 'newsletter_opt_in',
        'extra_fields', 'seo_title', 'seo_description',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'drawn_at' => 'datetime',
        'entries_purged_at' => 'datetime',
        'is_published' => 'boolean',
        'auto_draw' => 'boolean',
        'newsletter_opt_in' => 'boolean',
        'extra_fields' => 'array',
        'winners_count' => 'integer',
        'runners_up_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $contest) {
            if (empty($contest->slug)) {
                $contest->slug = Str::slug($contest->title);
            }
        });
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ContestEntry::class);
    }

    public function draws(): HasMany
    {
        return $this->hasMany(ContestDraw::class)->latest();
    }

    public function latestDraw(): HasOne
    {
        return $this->hasOne(ContestDraw::class)->latestOfMany();
    }

    /** Winner + runners-up, best rank first. */
    public function awarded(): HasMany
    {
        return $this->hasMany(ContestEntry::class)->whereNotNull('award_rank')->orderBy('award_rank');
    }

    /** Ranks 1..winners_count win; the ranks after them are the runners-up, in order. */
    public function winners(): HasMany
    {
        return $this->hasMany(ContestEntry::class)
            ->whereBetween('award_rank', [1, $this->winnerSlots()])
            ->orderBy('award_rank');
    }

    public function runnersUp(): HasMany
    {
        return $this->hasMany(ContestEntry::class)
            ->where('award_rank', '>', $this->winnerSlots())
            ->orderBy('award_rank');
    }

    public function winnerSlots(): int
    {
        return max(1, (int) $this->winners_count);
    }

    public function isWinningRank(?int $rank): bool
    {
        return $rank !== null && $rank >= 1 && $rank <= $this->winnerSlots();
    }

    /**
     * "Winner" / "Winner #2" / "Runner-up 1" for a draw rank. Public pages get
     * the translated label; the admin (English UI) passes $translate = false.
     */
    public function awardLabel(int $rank, bool $translate = true): string
    {
        $t = fn (string $key, array $r = []) => $translate ? __($key, $r) : strtr($key, array_combine(
            array_map(fn ($k) => ':'.$k, array_keys($r)), array_values($r)
        ) ?: []);

        if ($this->isWinningRank($rank)) {
            return $this->winnerSlots() === 1 ? $t('Winner') : $t('Winner #:n', ['n' => $rank]);
        }

        return $t('Runner-up :n', ['n' => $rank - $this->winnerSlots()]);
    }

    /* ---------------------------------------------------------------- state */

    public function getStateAttribute(): string
    {
        if ($this->drawn_at) {
            return self::STATE_COMPLETED;
        }
        if (! $this->is_published) {
            return self::STATE_DRAFT;
        }
        if ($this->starts_at?->isFuture()) {
            return self::STATE_SCHEDULED;
        }
        if ($this->ends_at?->isFuture()) {
            return self::STATE_ACTIVE;
        }

        return self::STATE_ENDED;
    }

    public function getStateLabelAttribute(): string
    {
        return [
            self::STATE_DRAFT => __('Draft'),
            self::STATE_SCHEDULED => __('Scheduled'),
            self::STATE_ACTIVE => __('Running'),
            self::STATE_ENDED => __('Ended'),
            self::STATE_COMPLETED => __('Completed'),
        ][$this->state];
    }

    /** Accepting entries right now. */
    public function isOpen(): bool
    {
        return $this->state === self::STATE_ACTIVE;
    }

    public function hasEnded(): bool
    {
        return in_array($this->state, [self::STATE_ENDED, self::STATE_COMPLETED], true);
    }

    public function isDrawn(): bool
    {
        return $this->drawn_at !== null;
    }

    /** 0-100: how far through its run the contest is (for the admin progress bar). */
    public function getProgressAttribute(): int
    {
        if (! $this->starts_at || ! $this->ends_at) {
            return 0;
        }
        $total = $this->ends_at->getTimestamp() - $this->starts_at->getTimestamp();
        if ($total <= 0) {
            return $this->ends_at->isPast() ? 100 : 0;
        }
        $done = now()->getTimestamp() - $this->starts_at->getTimestamp();

        return (int) max(0, min(100, round($done / $total * 100)));
    }

    /* --------------------------------------------------------------- scopes */

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function scopeOpen(Builder $q): Builder
    {
        return $q->published()->where('starts_at', '<=', now())->where('ends_at', '>', now());
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->published()->where('starts_at', '>', now());
    }

    public function scopeFinished(Builder $q): Builder
    {
        return $q->published()->where('ends_at', '<=', now());
    }

    /** Ended, auto-draw on, not drawn yet — the queue the scheduler works through. */
    public function scopeAwaitingAutoDraw(Builder $q): Builder
    {
        return $q->published()->where('auto_draw', true)->whereNull('drawn_at')->where('ends_at', '<=', now());
    }

    /* ------------------------------------------------------- form structure */

    /**
     * The extra entry fields, normalised and localised.
     *
     * @return array<int, array{key:string,label:string,type:string,required:bool,options:array<int,string>}>
     */
    public function fieldDefinitions(): array
    {
        $greek = app()->getLocale() === 'el';
        $out = [];

        foreach ((array) $this->extra_fields as $i => $field) {
            $label = trim((string) ($field['label'] ?? ''));
            $labelEl = trim((string) ($field['label_el'] ?? ''));
            $label = $greek && $labelEl !== '' ? $labelEl : $label;

            if ($label === '') {
                continue;
            }

            $key = Str::slug((string) ($field['key'] ?? ''), '_') ?: 'field_'.($i + 1);
            $type = isset(self::FIELD_TYPES[$field['type'] ?? '']) ? $field['type'] : 'text';

            $options = collect(preg_split("/\r\n|\n|\r/", (string) ($field['options'] ?? '')))
                ->map(fn ($o) => trim($o))->filter()->values()->all();

            $out[] = [
                'key' => $key,
                'label' => $label,
                'type' => $type,
                'required' => (bool) ($field['required'] ?? false),
                'options' => $options,
            ];
        }

        return $out;
    }

    public function collectsPhone(): bool
    {
        return $this->phone_field !== self::PHONE_OFF;
    }

    public function phoneRequired(): bool
    {
        return $this->phone_field === self::PHONE_REQUIRED;
    }
}
