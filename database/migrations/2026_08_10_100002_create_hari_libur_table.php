<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari_libur', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_libur');
            $table->unsignedSmallInteger('tahun');
            $table->string('sumber')->default('nasional'); // 'nasional' | 'internal'
            $table->timestamps();

            // Satu tanggal hanya bisa punya 1 entry per sumber
            $table->unique(['tanggal', 'sumber']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari_libur');
    }
};
