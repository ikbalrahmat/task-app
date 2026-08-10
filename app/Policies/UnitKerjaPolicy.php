<?php

namespace App\Policies;

use App\Models\UnitKerja;
use App\Models\User;

class UnitKerjaPolicy
{
    // Hanya Super Admin yang bisa manage unit kerja
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool  { return false; }
    public function view(User $user, UnitKerja $unitKerja): bool { return false; }
    public function create(User $user): bool   { return false; }
    public function update(User $user, UnitKerja $unitKerja): bool { return false; }
    public function delete(User $user, UnitKerja $unitKerja): bool { return false; }
}
