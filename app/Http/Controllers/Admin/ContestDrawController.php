<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contest;
use App\Support\ContestDrawRunner;

class ContestDrawController extends AdminController
{
    /** The "Draw" button: pick the winner now, even before the closing date. */
    public function store(Contest $contest)
    {
        $this->authorize('update', $contest);

        try {
            $draw = ContestDrawRunner::run($contest, auth()->user());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['draw' => $e->getMessage()]);
        }

        $winner = $contest->fresh()->winners()->first();

        return redirect()->route('admin.contests.edit', $contest)->with(
            'success',
            'Draw complete — '.($winner?->name ?? 'winner picked').' won out of '.$draw->entries_count.' entries.'
        );
    }
}
