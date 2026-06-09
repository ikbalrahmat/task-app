<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
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
        return User::withTrashed()->findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete(int $id): bool
    {
        return User::findOrFail($id)->delete();
    }

    private function buildQuery(array $filters = [])
    {
        $query = User::query();
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                  ->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        return $query->orderBy('name');
    }
}
