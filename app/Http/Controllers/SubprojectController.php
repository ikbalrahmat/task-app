<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Subproject;
use App\Http\Requests\StoreSubprojectRequest;
use App\Http\Requests\UpdateSubprojectRequest;
use Illuminate\Http\Request;

class SubprojectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subproject::with('project', 'tasks');

        if (!auth()->user()->hasCrudAccess()) {
            $query->whereHas('tasks.pics', fn($q) => $q->where('users.id', auth()->id()));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $subprojects = $query->latest()->paginate(10)->withQueryString();
        
        if (!auth()->user()->hasCrudAccess()) {
            $projects = Project::whereHas('tasks.pics', fn($q) => $q->where('users.id', auth()->id()))->orderBy('name')->get();
        } else {
            $projects = Project::orderBy('name')->get();
        }

        return view('subprojects.index', compact('subprojects', 'projects'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->isAdminOrManager()) {
            abort(403);
        }
        $projects = Project::orderBy('name')->get();
        $selectedProjectId = $request->query('project_id');
        return view('subprojects.create', compact('projects', 'selectedProjectId'));
    }

    public function store(StoreSubprojectRequest $request)
    {
        if (!auth()->user()->isAdminOrManager()) {
            abort(403);
        }
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $subproject = Subproject::create($data);

        return redirect()->route('projects.show', $subproject->project_id)
            ->with('success', 'List berhasil dibuat.');
    }

    public function edit(Subproject $subproject)
    {
        if (!auth()->user()->isAdminOrManager()) {
            abort(403);
        }
        $projects = Project::orderBy('name')->get();
        return view('subprojects.edit', compact('subproject', 'projects'));
    }

    public function show(Subproject $subproject)
    {
        if (!auth()->user()->hasCrudAccess() && !$subproject->tasks()->whereHas('pics', fn($q) => $q->where('users.id', auth()->id()))->exists()) {
            abort(403);
        }
        $subproject->load('project', 'tasks.pics');
        return view('subprojects.show', compact('subproject'));
    }

    public function update(UpdateSubprojectRequest $request, Subproject $subproject)
    {
        if (!auth()->user()->isAdminOrManager()) {
            abort(403);
        }
        
        $oldProjectId = $subproject->project_id;
        $subproject->update($request->validated());

        if ($subproject->project_id != $oldProjectId) {
            $subproject->tasks()->update(['project_id' => $subproject->project_id]);
        }

        return redirect()->route('projects.show', $subproject->project_id)
            ->with('success', 'List berhasil diperbarui.');
    }

    public function destroy(Subproject $subproject)
    {
        if (!auth()->user()->isAdminOrManager()) {
            abort(403);
        }
        $projectId = $subproject->project_id;
        $subproject->delete();

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'List berhasil dihapus.');
    }
}
