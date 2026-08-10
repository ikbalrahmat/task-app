<?php

namespace App\Policies;

use App\Models\Subproject;
use App\Models\User;

class SubprojectPolicy
{
    public function viewAny(User $user): bool  { return true; }
    public function view(User $user, Subproject $subproject): bool { return true; }
    public function create(User $user): bool   { return true; }
    public function update(User $user, Subproject $subproject): bool { return true; }
    public function delete(User $user, Subproject $subproject): bool { return true; }
}
