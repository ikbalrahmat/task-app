<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\ProjectService;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private ProjectService $projectService,
        private UserService $userService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
        $filters = $request->only(['search', 'project_id', 'pic_id', 'status', 'sort', 'dir']);
        $tasks = $this->taskService->paginate(15, $filters);
        $projects = $this->projectService->all();
        $users = $this->userService->all();
        return view('tasks.index', compact('tasks', 'projects', 'users', 'filters'));
    }

    public function create()
    {
        $this->authorize('create', Task::class);
        $projects = $this->projectService->all();
        $users = $this->userService->all();
        return view('tasks.create', compact('projects', 'users'));
    }

    public function store(StoreTaskRequest $request)
    {
        $this->authorize('create', Task::class);
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $task = $this->taskService->create($data);
        ActivityLogger::log('task.created', 'Membuat task baru: ' . $task->title);
        return redirect()->route('tasks.index')->with('success', 'Task berhasil ditambahkan.');
    }

    public function show(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    public function edit(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('update', $task);
        $projects = $this->projectService->all();
        $users = $this->userService->all();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(UpdateTaskRequest $request, int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('update', $task);
        $updated = $this->taskService->update($id, $request->validated());
        ActivityLogger::log('task.updated', 'Memperbarui task: ' . $updated->title);
        return redirect()->route('tasks.index')->with('success', 'Task berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('delete', $task);
        $title = $task->title;
        $this->taskService->delete($id);
        ActivityLogger::log('task.deleted', 'Menghapus task: ' . $title);
        return redirect()->route('tasks.index')->with('success', 'Task berhasil dihapus.');
    }
}
