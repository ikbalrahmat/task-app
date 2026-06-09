<?php

namespace App\Services;

use App\Repositories\Interfaces\ProjectRepositoryInterface;

class ProjectService
{
    public function __construct(private ProjectRepositoryInterface $repo) {}

    public function paginate(int $perPage = 10, array $filters = [])
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
}
