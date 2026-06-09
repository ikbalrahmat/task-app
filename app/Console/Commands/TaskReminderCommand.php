<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDeadlineNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TaskReminderCommand extends Command
{
    protected $signature   = 'task:reminder';
    protected $description = 'Kirim reminder deadline task kepada PIC (H-7, H-3, H-1, Hari H)';

    public function handle(): void
    {
        $thresholds = [7, 3, 1, 0];

        foreach ($thresholds as $days) {
            $date  = now()->addDays($days)->toDateString();
            $tasks = Task::with(['pic', 'project'])
                ->where('due_date', $date)
                ->where('status', '!=', 'Selesai')
                ->whereNotNull('pic_id')
                ->get();

            foreach ($tasks as $task) {
                if ($task->pic) {
                    $type = $days === 0 ? 'hari_h' : "h_minus_{$days}";
                    $task->pic->notify(new TaskDeadlineNotification($task, $type));
                    $this->info("Reminder terkirim: {$task->name} → {$task->pic->name} (H-{$days})");
                    Log::info("TaskReminder: task_id={$task->id}, pic={$task->pic->email}, days={$days}");
                }
            }
        }

        // Overdue tasks
        $overdue = Task::with(['pic', 'project'])
            ->where('due_date', '<', now()->toDateString())
            ->where('status', '!=', 'Selesai')
            ->whereNotNull('pic_id')
            ->get();

        foreach ($overdue as $task) {
            if ($task->pic) {
                $task->pic->notify(new TaskDeadlineNotification($task, 'overdue'));
                $this->warn("Overdue: {$task->name} → {$task->pic->name}");
            }
        }

        $this->info('Reminder selesai dikirim.');
    }
}
