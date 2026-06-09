<?php

namespace App\Services;

use App\Models\TaskAttachment;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TaskService
{
    public function __construct(private TaskRepositoryInterface $repo) {}

    public function paginate(int $perPage = 15, array $filters = [])
    {
        return $this->repo->paginate($perPage, $filters);
    }

    public function all(array $filters = [])
    {
        return $this->repo->all($filters);
    }

    public function find(int $id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function getOverdue()
    {
        return $this->repo->getOverdue();
    }

    public function getUpcomingDeadlines(int $days = 7)
    {
        return $this->repo->getUpcomingDeadlines($days);
    }

    public function uploadAttachment(int $taskId, UploadedFile $file, int $userId): TaskAttachment
    {
        $path = $file->store("task-attachments/{$taskId}", 'public');
        return TaskAttachment::create([
            'task_id'     => $taskId,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_type'   => $file->getClientOriginalExtension(),
            'file_size'   => $file->getSize(),
            'uploaded_by' => $userId,
        ]);
    }

    public function deleteAttachment(int $attachmentId): bool
    {
        $att = TaskAttachment::findOrFail($attachmentId);
        Storage::disk('public')->delete($att->file_path);
        return $att->delete();
    }
}
