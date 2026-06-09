<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
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
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'project_name' => $projectName,
            'assigned_by' => auth()->check() ? auth()->user()->name : 'System',
            'message' => 'Anda ditunjuk sebagai PIC baru untuk tugas "' . $this->task->name . '" di project "' . $projectName . '".',
        ];
    }
}
