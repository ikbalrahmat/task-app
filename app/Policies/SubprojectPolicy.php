<?php

namespace App\Policies;

use App\Models\Subproject;
use App\Models\User;

class SubprojectPolicy
{
    public function viewAny(User $user): bool { return true; }
    
    public function view(User $user, Subproject $subproject): bool
    {
        if ($user->hasCrudAccess()) return true;
        return $subproject->tasks()->whereHas('pics', fn($q) => $q->where('users.id', $user->id))->exists();
    }

    public function create(User $user): bool { return $user->hasCrudAccess(); }
    public function update(User $user, Subproject $subproject): bool { return $user->hasCrudAccess(); }
    public function delete(User $user, Subproject $subproject): bool { return $user->hasCrudAccess(); }
}
