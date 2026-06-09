<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create task_user pivot table
        Schema::create('task_user', function (Blueprint $table) {
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['task_id', 'user_id']);
        });

        // 2. Migrate existing data from tasks.pic_id to task_user
        $tasks = DB::table('tasks')->whereNotNull('pic_id')->get();
        foreach ($tasks as $task) {
            DB::table('task_user')->insertOrIgnore([
                'task_id' => $task->id,
                'user_id' => $task->pic_id,
            ]);
        }

        // 3. Drop pic_id column and its foreign key
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['pic_id']);
            $table->dropColumn('pic_id');
        });
    }

    public function down(): void
    {
        // 1. Re-add pic_id column to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('pic_id')->nullable()->constrained('users')->nullOnDelete();
        });

        // 2. Rollback data from task_user back to tasks.pic_id (taking the first assigned user)
        $pivotEntries = DB::table('task_user')->get();
        foreach ($pivotEntries as $entry) {
            DB::table('tasks')
                ->where('id', $entry->task_id)
                ->update(['pic_id' => $entry->user_id]);
        }

        // 3. Drop the pivot table
        Schema::dropIfExists('task_user');
    }
};
