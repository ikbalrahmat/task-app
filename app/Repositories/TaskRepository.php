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

        foreach ($picIds as $userId) {
            if ($userId != auth()->id()) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->notify(new \App\Notifications\TaskAssignedNotification($task));
                }
            }
        }
        return $task;
    }

    public function update(int $id, array $data)
    {
        $task = Task::findOrFail($id);

        if (array_key_exists('pic_ids', $data)) {
            $picIds = $data['pic_ids'] ?? [];
            $oldPicIds = $task->pics->pluck('id')->toArray();
            $task->pics()->sync($picIds);

            $newPicIds = array_diff($picIds, $oldPicIds);
            foreach ($newPicIds as $userId) {
                if ($userId != auth()->id()) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->notify(new \App\Notifications\TaskAssignedNotification($task));
                    }
                }
            }
        }
        unset($data['pic_ids']);

        $task->update($data);
        return $task;
    }

    public function delete(int $id): bool
    {
        return Task::findOrFail($id)->delete();
    }

    public function getOverdue()
    {
        $query = Task::with(['project', 'pics'])
            ->where('due_date', '<', now()->toDateString())
            ->where('status', '!=', 'Selesai')
            ->orderBy('due_date');

        if (auth()->check() && !auth()->user()->hasCrudAccess()) {
            $query->whereHas('pics', fn($q) => $q->where('users.id', auth()->id()));
        }

        return $query->get();
    }

    public function getUpcomingDeadlines(int $days = 7)
    {
        $query = Task::with(['project', 'pics'])
            ->where('status', '!=', 'Selesai')
            ->where('due_date', '>=', now()->toDateString())
            ->where('due_date', '<=', now()->addDays($days)->toDateString())
            ->orderBy('due_date');

        if (auth()->check() && !auth()->user()->hasCrudAccess()) {
            $query->whereHas('pics', fn($q) => $q->where('users.id', auth()->id()));
        }

        return $query->get();
    }

    private function buildQuery(array $filters = [])
    {
        $query = Task::with(['project', 'pics']);

        if (auth()->check() && !auth()->user()->hasCrudAccess()) {
            $query->whereHas('pics', fn($q) => $q->where('users.id', auth()->id()));
        }

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
