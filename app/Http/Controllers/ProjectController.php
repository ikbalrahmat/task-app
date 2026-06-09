<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Subproject;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;

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
        $project = $this->service->create($data);
        ActivityLogger::log('project.created', 'Membuat project baru: ' . $project->name);
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
        $updated = $this->service->update($id, $request->validated());
        ActivityLogger::log('project.updated', 'Memperbarui project: ' . $updated->name);
        return redirect()->route('projects.index')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $project = $this->service->find($id);
        $this->authorize('delete', $project);
        $name = $project->name;
        $this->service->delete($id);
        ActivityLogger::log('project.deleted', 'Menghapus project: ' . $name);
        return redirect()->route('projects.index')->with('success', 'Project berhasil dihapus.');
    }

    public function convertToSubproject(Request $request, int $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);
        ActivityLogger::log('project.converted', "Mengonversi project '{$project->name}' menjadi subproject.");

        $request->validate([
            'target_project_id' => 'required|exists:projects,id|not_in:' . $project->id,
        ], [
            'target_project_id.required' => 'Project induk sasaran wajib dipilih.',
            'target_project_id.exists' => 'Project induk sasaran tidak valid.',
            'target_project_id.not_in' => 'Tidak dapat memindahkan project ke dirinya sendiri.',
        ]);

        $targetProjectId = $request->input('target_project_id');

        DB::transaction(function () use ($project, $targetProjectId) {
            // 1. Create new Subproject under the target Project
            $subproject = Subproject::create([
                'project_id'        => $targetProjectId,
                'name'              => $project->name,
                'description'       => $project->description,
                'status'            => in_array($project->status, Subproject::STATUSES) ? $project->status : 'Berjalan',
                'start_date'        => $project->start_date,
                'end_date'          => $project->end_date,
                'actual_start_date' => $project->actual_start_date,
                'actual_end_date'   => $project->actual_end_date,
                'created_by'        => auth()->id(),
            ]);

            // 2. Reassign all direct tasks of the source project (where subproject_id is null)
            $project->tasks()->whereNull('subproject_id')->update([
                'project_id'    => $targetProjectId,
                'subproject_id' => $subproject->id,
            ]);

            // 3. Reassign all subprojects of the source project
            foreach ($project->subprojects as $sp) {
                // Update project_id for all tasks under this child subproject
                $sp->tasks()->update(['project_id' => $targetProjectId]);
                // Update parent project of the subproject
                $sp->update(['project_id' => $targetProjectId]);
            }

            // 4. Force delete the source project
            $project->forceDelete();
        });

        return redirect()->route('projects.show', $targetProjectId)
            ->with('success', 'Project berhasil diubah menjadi Sub-Project beserta seluruh tugas di dalamnya!');
    }
}
