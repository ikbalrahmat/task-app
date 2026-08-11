<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class AgendaKegiatan extends Model
{
    use HasFactory;

    protected $table = 'agenda_kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'kategori',
        'keterangan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tahun',
        'project_id',
        'created_by',
        'unit_kerja_id',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'tahun'           => 'integer',
    ];

    // Kategori yang tersedia
    const KATEGORI = [
        'Audit Internal',
        'Audit Eksternal',
        'Assessment',
        'Training',
        'Monitoring',
        'Lainnya',
    ];

    // Warna per kategori (untuk Gantt bar dan Kalender event)
    const WARNA_KATEGORI = [
        'Audit Internal'  => '#3b82f6',
        'Audit Eksternal' => '#8b5cf6',
        'Assessment'      => '#10b981',
        'Training'        => '#f59e0b',
        'Monitoring'      => '#06b6d4',
        'Lainnya'         => '#6b7280',
    ];

    protected static function booted(): void
    {
        // Isolasi per unit kerja (sama dengan modul lain)
        static::addGlobalScope('unit_kerja', function ($query) {
            if (!Auth::hasUser()) return;
            $user = Auth::user();
            if ($user->isSuperAdmin()) return;
            if (!$user->unit_kerja_id) {
                $query->whereRaw('1 = 0');
                return;
            }
            $query->where('unit_kerja_id', $user->unit_kerja_id);
        });
    }

    // Relations
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    // Accessor warna
    public function getWarnaAttribute(): string
    {
        return self::WARNA_KATEGORI[$this->kategori] ?? '#6b7280';
    }

    // Sudah di-mapping ke tanggal?
    public function getSudahDimappingAttribute(): bool
    {
        return !is_null($this->tanggal_mulai) && !is_null($this->tanggal_selesai);
    }
}
