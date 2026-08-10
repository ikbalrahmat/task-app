<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * UnitKerjaUserScope: Filter user query ke unit kerja yang sama.
 * Super Admin bisa lihat semua user.
 * Admin per unit kerja hanya bisa lihat user di unitnya.
 */
class UnitKerjaUserScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Gunakan hasUser() — mencegah circular dependency saat user masih di-load
        if (!Auth::hasUser()) {
            return;
        }

        $user = Auth::user();

        // Super Admin bisa lihat semua user
        if ($user->isSuperAdmin()) {
            return;
        }

        // User biasa hanya lihat user sesama unit kerja
        if ($user->unit_kerja_id) {
            $builder->where('unit_kerja_id', $user->unit_kerja_id);
        }
    }
}
