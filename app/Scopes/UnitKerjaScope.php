<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * UnitKerjaScope: Otomatis mem-filter query ke unit kerja user yang sedang login.
 * Super Admin melewati filter ini dan bisa lihat semua data.
 */
class UnitKerjaScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Gunakan hasUser() bukan check() — mencegah circular dependency
        // saat user masih di-load dari session
        if (!Auth::hasUser()) {
            return;
        }

        $user = Auth::user();

        // Super Admin bisa lihat semua data tanpa filter
        if ($user->isSuperAdmin()) {
            return;
        }

        // User biasa hanya lihat data di unit kerjanya
        if ($user->unit_kerja_id) {
            $builder->where($model->getTable() . '.unit_kerja_id', $user->unit_kerja_id);
        }
    }
}
