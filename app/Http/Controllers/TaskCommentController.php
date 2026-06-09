<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function store(Request $request, int $taskId)
    {
        $task = Task::findOrFail($taskId);
        $this->authorize('addComment', $task);

        $request->validate([
            'comment' => 'required|string',
        ]);

        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $comment = TaskComment::findOrFail($id);
        
        if ($request->user()->id !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Komentar berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $comment = TaskComment::findOrFail($id);

        if ($request->user()->id !== $comment->user_id && !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
