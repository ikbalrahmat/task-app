<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, Project $project): bool
    {
        if ($user->hasCrudAccess()) return true;
        return $project->tasks()->whereHas('pics', fn($q) => $q->where('users.id', $user->id))->exists();
    }
    public function create(User $user): bool   { return $user->hasCrudAccess(); }
    public function update(User $user, Project $project): bool { return $user->hasCrudAccess(); }
    public function delete(User $user, Project $project): bool { return $user->isAdmin(); }
}
