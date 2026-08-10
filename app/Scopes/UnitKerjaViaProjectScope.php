<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * UnitKerjaViaProjectScope
 *
 * Dipakai oleh model yang tidak punya unit_kerja_id langsung (Task, Subproject),
 * tapi terhubung ke Project yang sudah punya UnitKerjaScope.
 * Filter: hanya tampilkan record yang project-nya milik unit kerja user.
 */
class UnitKerjaViaProjectScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Hanya filter jika user sudah ter-load di memory (hindari circular dependency)
        if (!Auth::hasUser()) {
            return;
        }

        $user = Auth::user();

        // Super Admin lihat semua data
        if ($user->isSuperAdmin()) {
            return;
        }

        // Tidak ada unit kerja → tidak tampilkan apa-apa (safety)
        if (!$user->unit_kerja_id) {
            $builder->whereRaw('1 = 0');
            return;
        }

        // Filter via relasi project → unit_kerja_id
        $builder->whereHas('project', function ($q) use ($user) {
            $q->withoutGlobalScope(UnitKerjaScope::class)
              ->where('unit_kerja_id', $user->unit_kerja_id);
        });
    }
}
