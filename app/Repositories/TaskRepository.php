<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(array $filters = [])
    {
        return $this->buildQuery($filters)->get();
    }

    public function paginate(int $perPage = 15, array $filters = [])
    {
        return $this->buildQuery($filters)->paginate($perPage)->withQueryString();
    }

    public function find(int $id)
    {
        return Task::with(['project', 'pics', 'comments.user', 'attachments.uploader', 'creator'])->findOrFail($id);
    }

    public function create(array $data)
    {
        $picIds = $data['pic_ids'] ?? [];
        unset($data['pic_ids']);
        
        $task = Task::create($data);
        $task->pics()->sync($picIds);
        return $task;
    }

    public function update(int $id, array $data)
    {
        $picIds = $data['pic_ids'] ?? [];
        unset($data['pic_ids']);

        $task = Task::findOrFail($id);
        $task->update($data);
        $task->pics()->sync($picIds);
        return $task;
    }

    public function delete(int $id): bool
    {
        return Task::findOrFail($id)->delete();
    }

    public function getOverdue()
    {
        return Task::with(['project', 'pics'])
            ->where('due_date', '<', now()->toDateString())
            ->where('status', '!=', 'Selesai')
            ->orderBy('due_date')
            ->get();
    }

    public function getUpcomingDeadlines(int $days = 7)
    {
        return Task::with(['project', 'pics'])
            ->where('status', '!=', 'Selesai')
            ->where('due_date', '>=', now()->toDateString())
            ->where('due_date', '<=', now()->addDays($days)->toDateString())
            ->orderBy('due_date')
            ->get();
    }

    private function buildQuery(array $filters = [])
    {
        $query = Task::with(['project', 'pics']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        if (!empty($filters['pic_id'])) {
            $query->whereHas('pics', fn($q) => $q->where('users.id', $filters['pic_id']));
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['year'])) {
            $query->whereHas('project', fn($q) => $q->where('year', $filters['year']));
        }
        if (!empty($filters['sort'])) {
            $dir = $filters['dir'] ?? 'asc';
            $query->orderBy($filters['sort'], $dir);
        } else {
            $query->latest();
        }
        return $query;
    }
}
