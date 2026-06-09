<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Pengendali Teknis', 'Ketua Tim', 'Anggota Tim'])->default('Anggota Tim')->after('email');
            $table->string('department')->nullable()->after('role');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department', 'deleted_at']);
        });
    }
};
