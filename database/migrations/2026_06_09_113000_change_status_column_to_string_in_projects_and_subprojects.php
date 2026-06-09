<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update existing 'Perencanaan' status values in database to 'Belum Mulai'
        try {
            DB::table('projects')->where('status', 'Perencanaan')->update(['status' => 'Belum Mulai']);
            DB::table('subprojects')->where('status', 'Perencanaan')->update(['status' => 'Belum Mulai']);
        } catch (\Exception $e) {
            // Ignore if columns do not exist yet or have other issues
        }

        // 2. Change column schemas
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'Belum Mulai'");
            DB::statement("ALTER TABLE subprojects MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'Belum Mulai'");
        } else {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('status')->default('Belum Mulai')->change();
            });
            Schema::table('subprojects', function (Blueprint $table) {
                $table->string('status')->default('Belum Mulai')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE projects MODIFY COLUMN status ENUM('Perencanaan', 'Berjalan', 'Selesai', 'Ditunda') NOT NULL DEFAULT 'Perencanaan'");
            DB::statement("ALTER TABLE subprojects MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'Perencanaan'");
        } else {
            Schema::table('projects', function (Blueprint $table) {
                $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai', 'Ditunda'])->default('Perencanaan')->change();
            });
            Schema::table('subprojects', function (Blueprint $table) {
                $table->string('status')->default('Perencanaan')->change();
            });
        }
    }
};
