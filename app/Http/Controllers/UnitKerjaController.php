<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', UnitKerja::class);

        $unitKerjas = UnitKerja::withCount(['users', 'projects'])
            ->orderBy('name')
            ->paginate(15);

        return view('unit-kerja.index', compact('unitKerjas'));
    }

    public function create()
    {
        $this->authorize('create', UnitKerja::class);
        return view('unit-kerja.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', UnitKerja::class);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:unit_kerjas,code',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $unitKerja = UnitKerja::create($data);

        ActivityLogger::log('unit_kerja.created', 'Super Admin membuat unit kerja baru: ' . $unitKerja->name);

        return redirect()->route('unit-kerja.index')
            ->with('success', 'Unit Kerja berhasil ditambahkan.');
    }

    public function edit(UnitKerja $unitKerja)
    {
        $this->authorize('update', $unitKerja);
        return view('unit-kerja.edit', compact('unitKerja'));
    }

    public function update(Request $request, UnitKerja $unitKerja)
    {
        $this->authorize('update', $unitKerja);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:unit_kerjas,code,' . $unitKerja->id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $unitKerja->update($data);

        ActivityLogger::log('unit_kerja.updated', 'Super Admin memperbarui unit kerja: ' . $unitKerja->name);

        return redirect()->route('unit-kerja.index')
            ->with('success', 'Unit Kerja berhasil diperbarui.');
    }

    public function destroy(UnitKerja $unitKerja)
    {
        $this->authorize('delete', $unitKerja);

        $name = $unitKerja->name;
        $unitKerja->delete();

        ActivityLogger::log('unit_kerja.deleted', 'Super Admin menghapus unit kerja: ' . $name);

        return redirect()->route('unit-kerja.index')
            ->with('success', 'Unit Kerja berhasil dihapus.');
    }

    /**
     * Overview dashboard Super Admin — rekap semua unit kerja
     */
    public function overview()
    {
        $this->authorize('viewAny', UnitKerja::class);

        $unitKerjas = UnitKerja::with(['users', 'projects'])
            ->withCount(['users', 'projects'])
            ->where('is_active', true)
            ->get();

        $totalUsers    = User::withoutGlobalScopes()->whereNotNull('unit_kerja_id')->count();
        $totalProjects = Project::withoutGlobalScopes()->count();

        return view('unit-kerja.overview', compact('unitKerjas', 'totalUsers', 'totalProjects'));
    }
}
