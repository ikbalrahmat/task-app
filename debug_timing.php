<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tasks = App\Models\Task::all();
echo "TASKS:\n";
foreach($tasks as $t) {
    echo "ID: {$t->id} | Name: {$t->name} | Status: {$t->status} | Due: {$t->due_date?->format('Y-m-d')} | Actual End: {$t->actual_end_date?->format('Y-m-d')} | Delay Days: {$t->delay_days}\n";
}

$subs = App\Models\Subproject::all();
echo "\nSUBPROJECTS:\n";
foreach($subs as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Status: {$s->status} | End: {$s->end_date?->format('Y-m-d')} | Actual End: {$s->actual_end_date?->format('Y-m-d')} | Delay Days: {$s->delay_days}\n";
}

$projs = App\Models\Project::all();
echo "\nPROJECTS:\n";
foreach($projs as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Status: {$p->status} | End: {$p->end_date?->format('Y-m-d')} | Actual End: {$p->actual_end_date?->format('Y-m-d')} | Delay Days: {$p->delay_days}\n";
}
