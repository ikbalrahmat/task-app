<?php

use App\Console\Commands\TaskReminderCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler: Jalankan reminder task setiap hari pukul 08:00 WIB
Schedule::command(TaskReminderCommand::class)->dailyAt('08:00');

// Scheduler: Auto-sync hari libur nasional setiap 1 Januari jam 00:05
Schedule::command('libur:sync', [date('Y') + 1])->yearlyOn(1, 1, '00:05');

