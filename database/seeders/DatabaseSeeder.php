<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create([
            'name'       => 'Admin Sistem',
            'email'      => 'admin@taskflow.id',
            'password'   => Hash::make('password'),
            'role'       => 'Admin',
            'department' => 'Management',
        ]);
        $manager = User::create([
            'name'       => 'Budi Santoso',
            'email'      => 'budi@taskflow.id',
            'password'   => Hash::make('password'),
            'role'       => 'Pengendali Teknis',
            'department' => 'Operations',
        ]);
        $member1 = User::create([
            'name'       => 'Sari Dewi',
            'email'      => 'sari@taskflow.id',
            'password'   => Hash::make('password'),
            'role'       => 'Ketua Tim',
            'department' => 'Finance',
        ]);
        $member2 = User::create([
            'name'       => 'Rina Kartika',
            'email'      => 'rina@taskflow.id',
            'password'   => Hash::make('password'),
            'role'       => 'Ketua Tim',
            'department' => 'HR',
        ]);
        $viewer = User::create([
            'name'       => 'Doni Prasetyo',
            'email'      => 'doni@taskflow.id',
            'password'   => Hash::make('password'),
            'role'       => 'Anggota Tim',
            'department' => 'Management',
        ]);

        // Projects
        $p1 = Project::create([
            'name'        => 'Project Q1 - Pengadaan',
            'year'        => 2025,
            'status'      => 'Berjalan',
            'start_date'  => '2025-01-15',
            'end_date'    => '2025-04-30',
            'description' => 'Proyek pengadaan barang dan jasa Q1 2025',
            'created_by'  => $admin->id,
        ]);
        $p2 = Project::create([
            'name'        => 'Project Q2 - Keuangan',
            'year'        => 2025,
            'status'      => 'Berjalan',
            'start_date'  => '2025-03-01',
            'end_date'    => '2025-06-30',
            'description' => 'Review laporan keuangan dan rekonsiliasi',
            'created_by'  => $admin->id,
        ]);
        $p3 = Project::create([
            'name'        => 'Project Q3 - IT Infra',
            'year'        => 2025,
            'status'      => 'Belum Mulai',
            'start_date'  => '2025-07-01',
            'end_date'    => '2025-09-30',
            'description' => 'Upgrade infrastruktur IT perusahaan',
            'created_by'  => $manager->id,
        ]);
        $p4 = Project::create([
            'name'        => 'Project Q4 - SDM',
            'year'        => 2025,
            'status'      => 'Selesai',
            'start_date'  => '2025-01-02',
            'end_date'    => '2025-02-28',
            'description' => 'Review proses rekrutmen dan penggajian',
            'created_by'  => $manager->id,
        ]);

        // Tasks for P1
        $t1 = Task::create(['project_id'=>$p1->id,'name'=>'Review Dokumen','start_date'=>'2025-01-15','due_date'=>'2025-02-15','progress'=>100,'status'=>'Selesai','created_by'=>$admin->id]);
        $t1->pics()->attach($manager->id);
        
        $t2 = Task::create(['project_id'=>$p1->id,'name'=>'Wawancara Stakeholder','start_date'=>'2025-02-16','due_date'=>'2025-03-10','progress'=>75,'status'=>'Berjalan','created_by'=>$admin->id]);
        $t2->pics()->attach($member1->id);
        
        $t3 = Task::create(['project_id'=>$p1->id,'name'=>'Konfirmasi Vendor','start_date'=>'2025-03-11','due_date'=>'2025-04-10','progress'=>30,'status'=>'Berjalan','created_by'=>$admin->id]);
        $t3->pics()->attach($manager->id);
        
        $t4 = Task::create(['project_id'=>$p1->id,'name'=>'Penyusunan Laporan','start_date'=>'2025-04-11','due_date'=>'2025-04-30','progress'=>0,'status'=>'Belum Mulai','created_by'=>$admin->id]);
        $t4->pics()->attach($member2->id);

        // Tasks for P2
        $t5 = Task::create(['project_id'=>$p2->id,'name'=>'Pengumpulan Data','start_date'=>'2025-03-01','due_date'=>'2025-03-31','progress'=>100,'status'=>'Selesai','created_by'=>$admin->id]);
        $t5->pics()->attach($viewer->id);
        
        $t6 = Task::create(['project_id'=>$p2->id,'name'=>'Analisis Laporan','start_date'=>'2025-04-01','due_date'=>'2025-05-01','progress'=>60,'status'=>'Berjalan','created_by'=>$admin->id]);
        $t6->pics()->attach($member1->id);
        
        $t7 = Task::create(['project_id'=>$p2->id,'name'=>'Rekonsiliasi Bank','start_date'=>'2025-05-02','due_date'=>'2025-05-31','progress'=>20,'status'=>'Berjalan','created_by'=>$admin->id]);
        $t7->pics()->attach($member2->id);

        // Tasks for P3
        $t8 = Task::create(['project_id'=>$p3->id,'name'=>'Inventarisasi Aset IT','start_date'=>'2025-07-01','due_date'=>'2025-07-31','progress'=>0,'status'=>'Belum Mulai','created_by'=>$manager->id]);
        $t8->pics()->attach($manager->id);

        // Tasks for P4
        $t9 = Task::create(['project_id'=>$p4->id,'name'=>'Review Penggajian','start_date'=>'2025-01-02','due_date'=>'2025-01-31','progress'=>100,'status'=>'Selesai','created_by'=>$manager->id]);
        $t9->pics()->attach($viewer->id);
        
        $t10 = Task::create(['project_id'=>$p4->id,'name'=>'Review Rekrutmen','start_date'=>'2025-02-01','due_date'=>'2025-02-28','progress'=>100,'status'=>'Selesai','created_by'=>$manager->id]);
        $t10->pics()->attach($member2->id);
    }
}
