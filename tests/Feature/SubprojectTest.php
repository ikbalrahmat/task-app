<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Subproject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubprojectTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->member = User::factory()->create([
            'role' => User::ROLE_KETUA,
        ]);

        $this->project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'status' => 'Berjalan',
            'created_by' => $this->admin->id,
            'year' => 2026,
        ]);
    }

    public function test_admin_can_create_subproject(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('subprojects.store'), [
                'project_id' => $this->project->id,
                'name' => 'Subproject A',
                'description' => 'Subproject Description',
                'status' => 'Perencanaan',
            ]);

        $response->assertRedirect(route('projects.show', $this->project->id));
        $this->assertDatabaseHas('subprojects', [
            'name' => 'Subproject A',
            'project_id' => $this->project->id,
        ]);
    }

    public function test_member_cannot_create_subproject(): void
    {
        $response = $this->actingAs($this->member)
            ->post(route('subprojects.store'), [
                'project_id' => $this->project->id,
                'name' => 'Subproject A',
                'description' => 'Subproject Description',
                'status' => 'Perencanaan',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('subprojects', [
            'name' => 'Subproject A',
        ]);
    }

    public function test_admin_can_edit_subproject(): void
    {
        $subproject = Subproject::create([
            'name' => 'Old Name',
            'project_id' => $this->project->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('subprojects.update', $subproject->id), [
                'project_id' => $this->project->id,
                'name' => 'New Name',
                'description' => 'Updated Description',
                'status' => 'Berjalan',
            ]);

        $response->assertRedirect(route('projects.show', $this->project->id));
        $this->assertDatabaseHas('subprojects', [
            'id' => $subproject->id,
            'name' => 'New Name',
        ]);
    }

    public function test_admin_can_delete_subproject(): void
    {
        $subproject = Subproject::create([
            'name' => 'Delete Me',
            'project_id' => $this->project->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('subprojects.destroy', $subproject->id));

        $response->assertRedirect(route('projects.show', $this->project->id));
        $this->assertDatabaseMissing('subprojects', [
            'id' => $subproject->id,
        ]);
    }

    public function test_admin_can_view_subproject_detail(): void
    {
        $subproject = Subproject::create([
            'name' => 'Subproject Detail View',
            'project_id' => $this->project->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('subprojects.show', $subproject->id));

        $response->assertStatus(200);
        $response->assertSee('Subproject Detail View');
    }

    public function test_moving_subproject_updates_tasks_project_id(): void
    {
        $newProject = Project::create([
            'name' => 'New Project Parent',
            'description' => 'Test Description',
            'status' => 'Berjalan',
            'created_by' => $this->admin->id,
            'year' => 2026,
        ]);

        $subproject = Subproject::create([
            'name' => 'Moving Subproject',
            'project_id' => $this->project->id,
            'created_by' => $this->admin->id,
        ]);

        // Create a task under this subproject
        $task = \App\Models\Task::create([
            'project_id' => $this->project->id,
            'subproject_id' => $subproject->id,
            'name' => 'Task in Subproject',
            'status' => 'Belum Mulai',
            'progress' => 0,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('subprojects.update', $subproject->id), [
                'project_id' => $newProject->id,
                'name' => 'Moving Subproject',
                'status' => 'Berjalan',
            ]);

        $response->assertRedirect(route('projects.show', $newProject->id));
        $this->assertDatabaseHas('subprojects', [
            'id' => $subproject->id,
            'project_id' => $newProject->id,
        ]);

        // Verify task project_id is updated!
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'project_id' => $newProject->id,
            'subproject_id' => $subproject->id,
        ]);
    }
}
