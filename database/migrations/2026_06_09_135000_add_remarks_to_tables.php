<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('actual_start_remarks')->nullable();
            $table->text('actual_end_remarks')->nullable();
        });

        Schema::table('subprojects', function (Blueprint $table) {
            $table->text('actual_start_remarks')->nullable();
            $table->text('actual_end_remarks')->nullable();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->text('actual_start_remarks')->nullable();
            $table->text('actual_end_remarks')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['actual_start_remarks', 'actual_end_remarks']);
        });

        Schema::table('subprojects', function (Blueprint $table) {
            $table->dropColumn(['actual_start_remarks', 'actual_end_remarks']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['actual_start_remarks', 'actual_end_remarks']);
        });
    }
};
