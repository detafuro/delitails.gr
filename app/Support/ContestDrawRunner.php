<?php

namespace App\Support;

use App\Mail\ContestDrawCompleted;
use App\Mail\ContestWinnerSelected;
use App\Models\Contest;
use App\Models\ContestDraw;
use App\Models\ContestEntry;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Picks the winner(s) and runners-up for a contest, writes the audit record and
 * sends the notifications. Used by both the admin "Draw" button and the
 * scheduled auto-draw, so a manual and an automatic draw behave identically.
 */
class ContestDrawRunner
{
    /**
     * @param  bool  $automatic  true when run by the scheduler (no admin behind it)
     *
     * @throws \RuntimeException when the contest has no entries to draw from
     */
    public static function run(Contest $contest, ?User $by = null, bool $automatic = false): ContestDraw
    {
        $draw = DB::transaction(function () use ($contest, $by, $automatic) {
            $picked = $contest->entries()
                ->inRandomOrder()
                ->limit(max(1, $contest->winners_count) + max(0, $contest->runners_up_count))
                ->get();

            if ($picked->isEmpty()) {
                throw new \RuntimeException('This contest has no entries to draw from.');
            }

            // Clear any previous result — a re-draw supersedes it, the log keeps both.
            $contest->entries()->whereNotNull('award_rank')->update(['award_rank' => null]);

            $rank = 0;
            $result = [];
            foreach ($picked as $entry) {
                $rank++;
                $entry->forceFill(['award_rank' => $rank])->save();
                $result[] = [
                    'rank' => $rank,
                    'entry_id' => $entry->id,
                    'name' => $entry->name,
                    'email' => $entry->email,
                ];
            }

            $draw = $contest->draws()->create([
                'performed_by' => $by?->id,
                'entries_count' => $contest->entries()->count(),
                'result' => $result,
                'is_automatic' => $automatic,
            ]);

            $contest->forceFill(['drawn_at' => now()])->save();

            return $draw;
        });

        self::notify($contest->fresh(['entries']), $draw);

        return $draw;
    }

    /** Winner email + a heads-up to the site owner. Mail problems never fail the draw. */
    private static function notify(Contest $contest, ContestDraw $draw): void
    {
        foreach ($contest->winners as $winner) {
            self::send($winner->email, new ContestWinnerSelected($contest, $winner), function () use ($winner) {
                $winner->forceFill(['notified_at' => now()])->save();
            });
        }

        $admin = Setting::get('contest_notify_email') ?: Setting::get('contact_email') ?: config('mail.from.address');
        if ($admin) {
            self::send($admin, new ContestDrawCompleted($contest, $draw));
        }
    }

    private static function send(string $to, $mailable, ?callable $after = null): void
    {
        try {
            Mail::to($to)->send($mailable);
            $after && $after();
        } catch (\Throwable $e) {
            report($e); // a mail hiccup must not undo a completed draw
        }
    }

    /**
     * Wipes entrant personal data after a contest (GDPR). Keeps the entry rows
     * (so counts and the draw log stay honest) but strips the personal fields.
     */
    public static function purgeEntrantData(Contest $contest): int
    {
        $count = $contest->entries()->count();

        $contest->entries()->each(function (ContestEntry $entry) {
            $entry->forceFill([
                'name' => __('Removed'),
                'email' => 'purged+'.$entry->id.'@delitails.invalid',
                'phone' => null,
                'extra' => null,
                'ip_address' => null,
                'user_agent' => null,
            ])->save();
        });

        $contest->forceFill(['entries_purged_at' => now()])->save();

        return $count;
    }
}
