<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Task $task): bool
    {
        if ($user->hasCrudAccess()) return true;
        return $task->pics()->where('users.id', $user->id)->exists();
    }
    public function create(User $user): bool { return $user->hasCrudAccess(); }

    public function update(User $user, Task $task): bool
    {
        if ($user->hasCrudAccess()) return true;
        return $task->pics()->where('users.id', $user->id)->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->hasCrudAccess();
    }

    public function addComment(User $user, Task $task): bool
    {
        if ($user->hasCrudAccess()) return true;
        return $task->pics()->where('users.id', $user->id)->exists();
    }

    public function uploadAttachment(User $user, Task $task): bool
    {
        if ($user->hasCrudAccess()) return true;
        return $task->pics()->where('users.id', $user->id)->exists();
    }

    public function deleteAttachment(User $user, Task $task): bool
    {
        return $user->hasCrudAccess();
    }
}
