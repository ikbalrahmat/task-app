<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\UnitKerja;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================
        // 1. SUPER ADMIN (tidak terikat unit kerja manapun)
        // =========================================================
        User::create([
            'name'          => 'Super Administrator',
            'email'         => 'superadmin@taskflow.id',
            'password'      => Hash::make('password'),
            'role'          => 'Super Admin',
            'department'    => 'System',
            'unit_kerja_id' => null,
        ]);

        // =========================================================
        // 2. UNIT KERJA A — Dinas Pertanian
        // =========================================================
        $ukA = UnitKerja::create([
            'name'        => 'Dinas Pertanian',
            'code'        => 'DINTAN',
            'description' => 'Dinas Pertanian dan Ketahanan Pangan Kota',
            'is_active'   => true,
        ]);

        $adminA = User::create([
            'name' => 'Admin Pertanian', 'email' => 'admin@dintan.id',
            'password' => Hash::make('password'), 'role' => 'Admin',
            'department' => 'Management', 'unit_kerja_id' => $ukA->id,
        ]);
        $managerA = User::create([
            'name' => 'Budi Santoso', 'email' => 'budi@dintan.id',
            'password' => Hash::make('password'), 'role' => 'Pengendali Teknis',
            'department' => 'Operations', 'unit_kerja_id' => $ukA->id,
        ]);
        $ketuaA = User::create([
            'name' => 'Sari Dewi', 'email' => 'sari@dintan.id',
            'password' => Hash::make('password'), 'role' => 'Ketua Tim',
            'department' => 'Field', 'unit_kerja_id' => $ukA->id,
        ]);
        $anggotaA = User::create([
            'name' => 'Doni Prasetyo', 'email' => 'doni@dintan.id',
            'password' => Hash::make('password'), 'role' => 'Anggota Tim',
            'department' => 'Field', 'unit_kerja_id' => $ukA->id,
        ]);

        $p1 = Project::create([
            'name' => 'Program Pengadaan Bibit Q1', 'year' => 2026,
            'status' => 'Berjalan', 'start_date' => '2026-01-15',
            'end_date' => '2026-04-30', 'description' => 'Pengadaan bibit unggul Q1 2026',
            'created_by' => $adminA->id, 'unit_kerja_id' => $ukA->id,
        ]);
        Project::create([
            'name' => 'Pelatihan Petani', 'year' => 2026,
            'status' => 'Belum Mulai', 'start_date' => '2026-05-01',
            'end_date' => '2026-08-31', 'description' => 'Program pelatihan dan penyuluhan petani',
            'created_by' => $managerA->id, 'unit_kerja_id' => $ukA->id,
        ]);

        $t1 = Task::create(['project_id' => $p1->id, 'name' => 'Survey Kebutuhan Bibit',
            'start_date' => '2026-01-15', 'due_date' => '2026-02-15',
            'progress' => 100, 'status' => 'Selesai', 'created_by' => $adminA->id]);
        $t1->pics()->attach($managerA->id);

        $t2 = Task::create(['project_id' => $p1->id, 'name' => 'Proses Pengadaan',
            'start_date' => '2026-02-16', 'due_date' => '2026-03-31',
            'progress' => 60, 'status' => 'Berjalan', 'created_by' => $adminA->id]);
        $t2->pics()->attach($ketuaA->id);

        $t3 = Task::create(['project_id' => $p1->id, 'name' => 'Distribusi Bibit',
            'start_date' => '2026-04-01', 'due_date' => '2026-04-30',
            'progress' => 0, 'status' => 'Belum Mulai', 'created_by' => $adminA->id]);
        $t3->pics()->attach($anggotaA->id);

        // =========================================================
        // 3. UNIT KERJA B — Dinas Pendidikan
        // =========================================================
        $ukB = UnitKerja::create([
            'name'        => 'Dinas Pendidikan',
            'code'        => 'DINDIK',
            'description' => 'Dinas Pendidikan dan Kebudayaan Kota',
            'is_active'   => true,
        ]);

        $adminB = User::create([
            'name' => 'Admin Pendidikan', 'email' => 'admin@dindik.id',
            'password' => Hash::make('password'), 'role' => 'Admin',
            'department' => 'Management', 'unit_kerja_id' => $ukB->id,
        ]);
        $managerB = User::create([
            'name' => 'Rina Kartika', 'email' => 'rina@dindik.id',
            'password' => Hash::make('password'), 'role' => 'Pengendali Teknis',
            'department' => 'Akademik', 'unit_kerja_id' => $ukB->id,
        ]);
        $anggotaB = User::create([
            'name' => 'Hendra Wijaya', 'email' => 'hendra@dindik.id',
            'password' => Hash::make('password'), 'role' => 'Anggota Tim',
            'department' => 'IT', 'unit_kerja_id' => $ukB->id,
        ]);

        $p3 = Project::create([
            'name' => 'Digitalisasi Administrasi Sekolah', 'year' => 2026,
            'status' => 'Berjalan', 'start_date' => '2026-02-01',
            'end_date' => '2026-07-31', 'description' => 'Implementasi sistem digital administrasi sekolah negeri',
            'created_by' => $adminB->id, 'unit_kerja_id' => $ukB->id,
        ]);
        $p4 = Project::create([
            'name' => 'Review Kurikulum 2026', 'year' => 2026,
            'status' => 'Selesai', 'start_date' => '2026-01-05',
            'end_date' => '2026-03-31', 'description' => 'Review dan pembaruan kurikulum 2026',
            'created_by' => $managerB->id, 'unit_kerja_id' => $ukB->id,
        ]);

        $t4 = Task::create(['project_id' => $p3->id, 'name' => 'Analisis Kebutuhan Sistem',
            'start_date' => '2026-02-01', 'due_date' => '2026-03-01',
            'progress' => 100, 'status' => 'Selesai', 'created_by' => $adminB->id]);
        $t4->pics()->attach($managerB->id);

        $t5 = Task::create(['project_id' => $p3->id, 'name' => 'Pengembangan Aplikasi',
            'start_date' => '2026-03-02', 'due_date' => '2026-05-31',
            'progress' => 45, 'status' => 'Berjalan', 'created_by' => $adminB->id]);
        $t5->pics()->attach($anggotaB->id);

        $t6 = Task::create(['project_id' => $p4->id, 'name' => 'Review Dokumen Kurikulum',
            'start_date' => '2026-01-05', 'due_date' => '2026-02-28',
            'progress' => 100, 'status' => 'Selesai', 'created_by' => $managerB->id]);
        $t6->pics()->attach($managerB->id);
    }
}
