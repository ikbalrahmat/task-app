<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Super Admin bypass semua check
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool  { return $user->isAdmin(); }
    public function view(User $user, User $model): bool { return $user->isAdmin() || $user->id === $model->id; }
    public function create(User $user): bool   { return $user->isAdmin(); }
    public function update(User $user, User $model): bool { return $user->isAdmin(); }
    public function delete(User $user, User $model): bool { return $user->isAdmin() && $user->id !== $model->id; }
}
