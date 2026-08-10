<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Insert default unit kerja untuk data existing
        DB::table('unit_kerjas')->insert([
            'name'        => 'Unit Kerja Default',
            'code'        => 'DEFAULT',
            'description' => 'Unit kerja default untuk data yang sudah ada sebelumnya.',
            'is_active'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $defaultId = DB::table('unit_kerjas')->where('code', 'DEFAULT')->value('id');

        // 2. Tambah kolom unit_kerja_id ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_kerja_id')->nullable()->after('department');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerjas')->nullOnDelete();
        });

        // 3. Tambah kolom unit_kerja_id ke tabel projects
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_kerja_id')->nullable()->after('created_by');
            $table->foreign('unit_kerja_id')->references('id')->on('unit_kerjas')->nullOnDelete();
        });

        // 4. Assign semua data existing ke unit kerja default
        // User dengan role bukan Super Admin dapat unit kerja default
        DB::table('users')
            ->where('role', '!=', 'Super Admin')
            ->update(['unit_kerja_id' => $defaultId]);

        DB::table('projects')
            ->update(['unit_kerja_id' => $defaultId]);

        // 5. Update enum role di tabel users untuk menambahkan 'Super Admin'
        // SQLite tidak support alter enum, jadi kita pakai string saja
        // (kolom role sudah pakai string sejak migration sebelumnya)
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');
        });
    }
};
