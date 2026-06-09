<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskAttachmentController extends Controller
{
    public function __construct(private TaskService $service) {}

    public function store(Request $request, int $taskId)
    {
        $task = Task::findOrFail($taskId);
        $this->authorize('uploadAttachment', $task);

        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ], [
            'file.max' => 'Ukuran file maksimal 10MB.',
            'file.mimes' => 'Format file tidak didukung.',
        ]);

        $this->service->uploadAttachment($taskId, $request->file('file'), $request->user()->id);

        return back()->with('success', 'File berhasil diunggah.');
    }

    public function destroy(int $id)
    {
        $attachment = TaskAttachment::findOrFail($id);
        $this->authorize('deleteAttachment', $attachment->task);
        $this->service->deleteAttachment($id);
        return back()->with('success', 'File berhasil dihapus.');
    }
}
