<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskDeadlineNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task, public string $type = 'reminder') {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days    = $this->task->days_until_due;
        $subject = match(true) {
            $days < 0  => "[OVERDUE] Task: {$this->task->name}",
            $days == 0 => "[HARI INI] Task: {$this->task->name}",
            default    => "[H-{$days}] Task: {$this->task->name}",
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Task berikut memerlukan perhatian Anda:")
            ->line("**{$this->task->name}**")
            ->line("Project: " . ($this->task->project->name ?? '-'))
            ->line("Due Date: " . ($this->task->due_date?->format('d M Y') ?? '-'))
            ->line("Progress: {$this->task->progress}%")
            ->action('Lihat Detail Task', url('/tasks/' . $this->task->id))
            ->line("Segera selesaikan task ini sebelum deadline.");
    }

    public function toArray(object $notifiable): array
    {
        $days = $this->task->days_until_due;
        $message = match(true) {
            $days < 0  => "Tugas \"{$this->task->name}\" terlambat (OVERDUE) " . abs($days) . " hari.",
            $days == 0 => "Tugas \"{$this->task->name}\" jatuh tempo HARI INI.",
            default    => "Tugas \"{$this->task->name}\" mendekati deadline dalam {$days} hari.",
        };

        return [
            'task_id'    => $this->task->id,
            'task_name'  => $this->task->name,
            'project'    => $this->task->project->name ?? '-',
            'due_date'   => $this->task->due_date?->format('d M Y'),
            'days_until' => $days,
            'type'       => $this->type,
            'message'    => $message,
        ];
    }
}
