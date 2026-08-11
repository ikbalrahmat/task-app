<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class TaskCommentNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task, public TaskComment $comment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        $commenterName = $this->comment->user->name ?? 'Seseorang';
        // Strip tags in case comment has HTML
        $commentText = strip_tags($this->comment->comment);
        // Truncate to 50 chars for the push notification body
        $commentText = strlen($commentText) > 50 ? substr($commentText, 0, 50) . '...' : $commentText;

        return FcmMessage::create()
            ->notification(FcmNotification::create()
                ->title('💬 Komentar: ' . $this->task->name)
                ->body($commenterName . ': ' . $commentText)
            )
            ->data([
                'task_id' => (string) $this->task->id,
                'url' => url('/tasks/' . $this->task->id)
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $commenterName = $this->comment->user->name ?? 'Seseorang';
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'comment_id' => $this->comment->id,
            'message' => $commenterName . ' mengomentari tugas "' . $this->task->name . '".',
        ];
    }
}
