<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create subprojects table
        Schema::create('subprojects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('Perencanaan');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Add subproject_id column to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('subproject_id')->nullable()->constrained('subprojects')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['subproject_id']);
            $table->dropColumn('subproject_id');
        });

        Schema::dropIfExists('subprojects');
    }
};
