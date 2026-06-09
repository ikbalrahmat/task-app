<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $taskId = $notification->data['task_id'] ?? null;
        if ($taskId) {
            return redirect()->route('tasks.show', $taskId);
        }

        return redirect()->back();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai terbaca.');
    }
}
