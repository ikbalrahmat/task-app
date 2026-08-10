<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite tidak support ALTER COLUMN untuk enum, tapi enum di SQLite
        // disimpan sebagai text biasa, sehingga kita bisa langsung insert 'Super Admin'.
        // Untuk MySQL/PostgreSQL, kita perlu modify column.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin','Admin','Pengendali Teknis','Ketua Tim','Anggota Tim') NOT NULL DEFAULT 'Anggota Tim'");
        }
        // SQLite: tidak perlu alter, langsung support karena enum = text
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin','Pengendali Teknis','Ketua Tim','Anggota Tim') NOT NULL DEFAULT 'Anggota Tim'");
        }
    }
};
