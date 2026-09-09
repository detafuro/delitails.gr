<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One immutable record per draw — the audit trail if a result is ever questioned. */
class ContestDraw extends Model
{
    protected $fillable = ['contest_id', 'performed_by', 'entries_count', 'result', 'is_automatic'];

    protected $casts = [
        'result' => 'array',
        'is_automatic' => 'boolean',
    ];

    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function performerLabel(): string
    {
        return $this->is_automatic ? __('Automatic (scheduler)') : ($this->performer?->name ?? __('Unknown'));
    }
}
