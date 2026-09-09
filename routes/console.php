<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Contests with auto-draw are drawn shortly after they close.
Schedule::command('contests:draw-due')->everyFiveMinutes()->withoutOverlapping();
