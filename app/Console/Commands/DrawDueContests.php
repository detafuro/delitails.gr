<?php

namespace App\Console\Commands;

use App\Models\Contest;
use App\Support\ContestDrawRunner;
use Illuminate\Console\Command;

/**
 * Draws every contest that has closed with auto-draw on. Runs from the
 * scheduler so a contest that ends at 3am still has its winner by 3:05am.
 */
class DrawDueContests extends Command
{
    protected $signature = 'contests:draw-due';

    protected $description = 'Run the automatic draw for contests that have closed';

    public function handle(): int
    {
        $due = Contest::awaitingAutoDraw()->get();

        if ($due->isEmpty()) {
            $this->info('No contests are due for a draw.');

            return self::SUCCESS;
        }

        foreach ($due as $contest) {
            try {
                $draw = ContestDrawRunner::run($contest, null, automatic: true);
                $this->info("Drew \"{$contest->title}\" — {$draw->entries_count} entries.");
            } catch (\Throwable $e) {
                // An empty contest (or a mail failure) must not stop the others.
                report($e);
                $this->warn("Skipped \"{$contest->title}\": {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
