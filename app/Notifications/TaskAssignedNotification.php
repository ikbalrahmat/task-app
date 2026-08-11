<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        $projectName = $this->task->project->name ?? '-';
        return FcmMessage::create()
            ->notification(FcmNotification::create()
                ->title('Tugas Baru: ' . $this->task->name)
                ->body('Anda ditunjuk sebagai PIC baru untuk tugas ini.')
            )
            ->data([
                'task_id' => (string) $this->task->id,
                'url' => url('/tasks/' . $this->task->id)
            ]);
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
