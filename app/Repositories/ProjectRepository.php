<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function all(array $filters = [])
    {
        return $this->buildQuery($filters)->get();
    }

    public function paginate(int $perPage = 10, array $filters = [])
    {
        return $this->buildQuery($filters)->paginate($perPage)->withQueryString();
    }

    public function find(int $id)
    {
        return Project::with(['tasks.pics', 'creator', 'subprojects.tasks.pics'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Project::create($data);
    }

    public function update(int $id, array $data)
    {
        $project = Project::findOrFail($id);
        $project->update($data);
        return $project;
    }

    public function delete(int $id): bool
    {
        return Project::findOrFail($id)->delete();
    }

    private function buildQuery(array $filters = [])
    {
        $query = Project::with(['tasks', 'creator', 'subprojects']);

        if (auth()->check() && !auth()->user()->hasCrudAccess()) {
            $query->whereHas('tasks.pics', fn($q) => $q->where('users.id', auth()->id()));
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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
