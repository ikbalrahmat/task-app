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
            ]);

        $response->assertRedirect(route('projects.show', $this->project->id));
        $this->assertDatabaseHas('subprojects', [
            'name' => 'Subproject A',
            'project_id' => $this->project->id,
            'status' => 'Belum Mulai',
        ]);
    }

    public function test_member_cannot_create_subproject(): void
    {
        $response = $this->actingAs($this->member)
            ->post(route('subprojects.store'), [
                'project_id' => $this->project->id,
                'name' => 'Subproject A',
                'description' => 'Subproject Description',
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

    public function test_admin_can_convert_project_to_subproject(): void
    {
        // Target project
        $targetProject = Project::create([
            'name' => 'Target Project Parent',
            'description' => 'Target Description',
            'status' => 'Berjalan',
            'created_by' => $this->admin->id,
            'year' => 2026,
        ]);

        // Source project (the one to be converted)
        $sourceProject = Project::create([
            'name' => 'Source Project',
            'description' => 'Source Description',
            'status' => 'Belum Mulai',
            'created_by' => $this->admin->id,
            'year' => 2026,
        ]);

        // Create a direct task under source project
        $task = \App\Models\Task::create([
            'project_id' => $sourceProject->id,
            'subproject_id' => null,
            'name' => 'Direct Task of Source',
            'status' => 'Belum Mulai',
            'progress' => 0,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('projects.convert', $sourceProject->id), [
                'target_project_id' => $targetProject->id,
            ]);

        // Should redirect to target project show
        $response->assertRedirect(route('projects.show', $targetProject->id));

        // Source project should be deleted
        $this->assertDatabaseMissing('projects', [
            'id' => $sourceProject->id,
        ]);

        // A new Subproject should exist under target project
        $this->assertDatabaseHas('subprojects', [
            'project_id' => $targetProject->id,
            'name' => 'Source Project',
        ]);

        $newSubproject = Subproject::where('project_id', $targetProject->id)
            ->where('name', 'Source Project')
            ->first();

        // The task should now belong to target project and the new Subproject
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'project_id' => $targetProject->id,
            'subproject_id' => $newSubproject->id,
        ]);
    }

    public function test_subproject_progress_is_average_of_tasks_progress(): void
    {
        $subproject = Subproject::create([
            'name' => 'Progress Subproject',
            'project_id' => $this->project->id,
            'created_by' => $this->admin->id,
        ]);

        \App\Models\Task::create([
            'project_id' => $this->project->id,
            'subproject_id' => $subproject->id,
            'name' => 'Task 1',
            'status' => 'Berjalan',
            'progress' => 50,
            'created_by' => $this->admin->id,
        ]);

        \App\Models\Task::create([
            'project_id' => $this->project->id,
            'subproject_id' => $subproject->id,
            'name' => 'Task 2',
            'status' => 'Selesai',
            'progress' => 100,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals(75, $subproject->fresh()->progress);
        $this->assertEquals(75, $this->project->fresh()->progress);

        // Add a direct task
        \App\Models\Task::create([
            'project_id' => $this->project->id,
            'subproject_id' => null,
            'name' => 'Direct Task 1',
            'status' => 'Selesai',
            'progress' => 100,
            'created_by' => $this->admin->id,
        ]);

        // Project should now have 1 subproject (75% progress) and 1 direct task (100% progress)
        // Average: (75 + 100) / 2 = 87.5% -> round to 88%
        $this->assertEquals(88, $this->project->fresh()->progress);
    }

    public function test_project_and_subproject_status_is_recalculated_automatically(): void
    {
        // 1. Initial State (No tasks)
        $project = Project::create([
            'name' => 'Auto Status Project',
            'year' => 2026,
            'created_by' => $this->admin->id,
        ]);
        $this->assertEquals('Belum Mulai', $project->status);

        $subproject = Subproject::create([
            'project_id' => $project->id,
            'name' => 'Auto Status Subproject',
            'created_by' => $this->admin->id,
        ]);
        $this->assertEquals('Belum Mulai', $subproject->status);

        // 2. Add 0% progress task -> Status should remain 'Belum Mulai'
        $task1 = \App\Models\Task::create([
            'project_id' => $project->id,
            'subproject_id' => $subproject->id,
            'name' => 'Task 1',
            'status' => 'Belum Mulai',
            'progress' => 0,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals('Belum Mulai', $subproject->fresh()->status);
        $this->assertEquals('Belum Mulai', $project->fresh()->status);

        // 3. Update Task 1 to 50% -> Status should change to 'Berjalan'
        $task1->update([
            'status' => 'Berjalan',
            'progress' => 50,
        ]);

        $this->assertEquals('Berjalan', $subproject->fresh()->status);
        $this->assertEquals('Berjalan', $project->fresh()->status);

        // 4. Update Task 1 to 100% -> Status should change to 'Selesai'
        $task1->update([
            'status' => 'Selesai',
            'progress' => 100,
        ]);

        $this->assertEquals('Selesai', $subproject->fresh()->status);
        $this->assertEquals('Selesai', $project->fresh()->status);

        // 5. Add second task at 0% -> Status should go back to 'Berjalan'
        $task2 = \App\Models\Task::create([
            'project_id' => $project->id,
            'subproject_id' => $subproject->id,
            'name' => 'Task 2',
            'status' => 'Belum Mulai',
            'progress' => 0,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals('Berjalan', $subproject->fresh()->status);
        $this->assertEquals('Berjalan', $project->fresh()->status);

        // 6. Complete task 2 -> Status should be 'Selesai' again
        $task2->update([
            'status' => 'Selesai',
            'progress' => 100,
        ]);

        $this->assertEquals('Selesai', $subproject->fresh()->status);
        $this->assertEquals('Selesai', $project->fresh()->status);

        // 7. Delete Task 2 -> Status should remain 'Selesai' since task 1 is still completed
        $task2->delete();

        $this->assertEquals('Selesai', $subproject->fresh()->status);
        $this->assertEquals('Selesai', $project->fresh()->status);
    }

    public function test_subproject_requires_remarks_if_actual_dates_differ_from_planned_dates(): void
    {
        // 1. Deviating start date without remarks -> validation fails
        $response = $this->actingAs($this->admin)
            ->post(route('subprojects.store'), [
                'project_id' => $this->project->id,
                'name' => 'Subproject Dev Start',
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-20',
                'actual_start_date' => '2026-06-11',
                'actual_start_remarks' => '', // Empty!
            ]);
        $response->assertSessionHasErrors(['actual_start_remarks']);

        // 2. Deviating start date with remarks -> validation succeeds
        $response = $this->actingAs($this->admin)
            ->post(route('subprojects.store'), [
                'project_id' => $this->project->id,
                'name' => 'Subproject Dev Start OK',
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-20',
                'actual_start_date' => '2026-06-11',
                'actual_start_remarks' => 'Delay due to weather',
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('subprojects', [
            'name' => 'Subproject Dev Start OK',
            'actual_start_remarks' => 'Delay due to weather',
        ]);

        // 3. Deviating end date without remarks -> validation fails
        $response = $this->actingAs($this->admin)
            ->post(route('subprojects.store'), [
                'project_id' => $this->project->id,
                'name' => 'Subproject Dev End',
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-20',
                'actual_end_date' => '2026-06-21',
                'actual_end_remarks' => '', // Empty!
            ]);
        $response->assertSessionHasErrors(['actual_end_remarks']);

        // 4. Deviating end date with remarks -> validation succeeds
        $response = $this->actingAs($this->admin)
            ->post(route('subprojects.store'), [
                'project_id' => $this->project->id,
                'name' => 'Subproject Dev End OK',
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-20',
                'actual_end_date' => '2026-06-21',
                'actual_end_remarks' => 'Additional testing requested',
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('subprojects', [
            'name' => 'Subproject Dev End OK',
            'actual_end_remarks' => 'Additional testing requested',
        ]);
    }

    public function test_project_requires_remarks_if_actual_dates_differ_from_planned_dates(): void
    {
        // 1. Deviating start date without remarks -> validation fails
        $response = $this->actingAs($this->admin)
            ->post(route('projects.store'), [
                'name' => 'Project Dev Start',
                'year' => 2026,
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-20',
                'actual_start_date' => '2026-06-11',
                'actual_start_remarks' => '', // Empty!
            ]);
        $response->assertSessionHasErrors(['actual_start_remarks']);

        // 2. Deviating start date with remarks -> validation succeeds
        $response = $this->actingAs($this->admin)
            ->post(route('projects.store'), [
                'name' => 'Project Dev Start OK',
                'year' => 2026,
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-20',
                'actual_start_date' => '2026-06-11',
                'actual_start_remarks' => 'Delay due to weather',
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'Project Dev Start OK',
            'actual_start_remarks' => 'Delay due to weather',
        ]);
    }

    public function test_task_requires_remarks_if_actual_dates_differ_from_planned_dates(): void
    {
        // 1. Deviating start date without remarks -> validation fails
        $response = $this->actingAs($this->admin)
            ->post(route('tasks.store'), [
                'project_id' => $this->project->id,
                'name' => 'Task Dev Start',
                'start_date' => '2026-06-10',
                'due_date' => '2026-06-20',
                'actual_start_date' => '2026-06-11',
                'actual_start_remarks' => '', // Empty!
                'progress' => 0,
                'status' => 'Belum Mulai',
            ]);
        $response->assertSessionHasErrors(['actual_start_remarks']);

        // 2. Deviating start date with remarks -> validation succeeds
        $response = $this->actingAs($this->admin)
            ->post(route('tasks.store'), [
                'project_id' => $this->project->id,
                'name' => 'Task Dev Start OK',
                'start_date' => '2026-06-10',
                'due_date' => '2026-06-20',
                'actual_start_date' => '2026-06-11',
                'actual_start_remarks' => 'Delay due to weather',
                'progress' => 0,
                'status' => 'Belum Mulai',
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'name' => 'Task Dev Start OK',
            'actual_start_remarks' => 'Delay due to weather',
        ]);
    }
}

