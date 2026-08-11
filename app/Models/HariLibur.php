<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HariLibur extends Model
{
    use HasFactory;

    protected $table = 'hari_libur';

    protected $fillable = [
        'tanggal',
        'nama_libur',
        'tahun',
        'sumber',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tahun'   => 'integer',
    ];

    // Scopes
    public function scopeForYear($query, int $year)
    {
        return $query->where('tahun', $year);
    }

    public function scopeNasional($query)
    {
        return $query->where('sumber', 'nasional');
    }

    public function scopeInternal($query)
    {
        return $query->where('sumber', 'internal');
    }
}
