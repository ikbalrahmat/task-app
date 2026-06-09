<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $service) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);
        $filters = $request->only(['search', 'year', 'status', 'sort', 'dir']);
        $projects = $this->service->paginate(10, $filters);
        return view('projects.index', compact('projects', 'filters'));
    }

    public function create()
    {
        $this->authorize('create', Project::class);
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', Project::class);
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $this->service->create($data);
        return redirect()->route('projects.index')->with('success', 'Project berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $project = $this->service->find($id);
        $this->authorize('view', $project);
        return view('projects.show', compact('project'));
    }

    public function edit(int $id)
    {
        $project = $this->service->find($id);
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, int $id)
    {
        $project = $this->service->find($id);
        $this->authorize('update', $project);
        $this->service->update($id, $request->validated());
        return redirect()->route('projects.index')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $project = $this->service->find($id);
        $this->authorize('delete', $project);
        $this->service->delete($id);
        return redirect()->route('projects.index')->with('success', 'Project berhasil dihapus.');
    }
}
