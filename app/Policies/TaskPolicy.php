<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Task $task): bool { return true; }
    public function create(User $user): bool { return $user->isAdminOrManager(); }

    public function update(User $user, Task $task): bool
    {
        // Admin & Manager can update any task
        if ($user->isAdminOrManager()) return true;
        // Member can only update their own tasks
        if ($user->isMember()) return $task->pics()->where('users.id', $user->id)->exists();
        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isAdminOrManager();
    }

    public function addComment(User $user, Task $task): bool
    {
        return !$user->isViewer();
    }

    public function uploadAttachment(User $user, Task $task): bool
    {
        return !$user->isViewer();
    }

    public function deleteAttachment(User $user, Task $task): bool
    {
        return $user->isAdminOrManager();
    }
}
