<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TaskStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task, public string $oldStatus, public User $updater)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->notification(FcmNotification::create()
                ->title('✅ Status Diperbarui: ' . $this->task->name)
                ->body($this->updater->name . ' mengubah status menjadi "' . $this->task->status . '".')
            )
            ->data([
                'task_id' => (string) $this->task->id,
                'url' => url('/tasks/' . $this->task->id)
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->task->status,
            'message' => $this->updater->name . ' mengubah status tugas "' . $this->task->name . '" menjadi ' . $this->task->status . '.',
        ];
    }
}
