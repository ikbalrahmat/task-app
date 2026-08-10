<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // Super Admin bypass: Laravel auto-calls 'before()' first
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return $user->unit_kerja_id === $project->unit_kerja_id;
    }

    public function create(User $user): bool
    {
        return $user->hasCrudAccess();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasCrudAccess() && $user->unit_kerja_id === $project->unit_kerja_id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->isAdminOrManager() && $user->unit_kerja_id === $project->unit_kerja_id;
    }
}
