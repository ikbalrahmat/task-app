<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskDeadlineApproachingNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $projectName = $this->task->project->name ?? '-';
        $dueDateStr = $this->task->due_date ? $this->task->due_date->format('d M Y') : '-';
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'project_name' => $projectName,
            'due_date' => $this->task->due_date ? $this->task->due_date->format('Y-m-d') : null,
            'message' => 'Peringatan: Tugas "' . $this->task->name . '" mendekati deadline pada ' . $dueDateStr . '.',
        ];
    }
}
