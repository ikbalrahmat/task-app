<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private UserRepositoryInterface $repo) {}

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
        $data['password'] = Hash::make($data['password']);
        $data['password_changed_at'] = null; // force change on first login
        return $this->repo->create($data);
    }

    public function update(int $id, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['password_changed_at'] = null; // force change on reset
        } else {
            unset($data['password']);
        }
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
