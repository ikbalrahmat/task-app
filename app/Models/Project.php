<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'year',
        'status',
        'start_date',
        'end_date',
        'actual_start_date',
        'actual_end_date',
        'actual_start_remarks',
        'actual_end_remarks',
        'description',
        'created_by',
    ];

    protected $attributes = [
        'status' => 'Belum Mulai',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'actual_start_date' => 'date',
        'actual_end_date'   => 'date',
    ];

    const STATUSES = ['Belum Mulai', 'Berjalan', 'Selesai'];

    // Relations
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function subprojects()
    {
        return $this->hasMany(Subproject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getProgressAttribute(): int
    {
        $subprojects = $this->subprojects;
        $directTasks = $this->tasks()->whereNull('subproject_id')->get();

        $totalItems = $subprojects->count() + $directTasks->count();
        if ($totalItems === 0) return 0;

        $sumProgress = $subprojects->sum('progress') + $directTasks->sum('progress');

        return (int) round($sumProgress / $totalItems);
    }

    public function getOverdueTasksCountAttribute(): int
    {
        return $this->tasks()->where('due_date', '<', now()->toDateString())
            ->where('status', '!=', 'Selesai')->count();
    }

    public function getDelayDaysAttribute(): int
    {
        if (!$this->end_date || !$this->actual_end_date) return 0;
        return (int) $this->end_date->startOfDay()->diffInDays($this->actual_end_date->startOfDay(), false);
    }

    public function recalculateStatus(): void
    {
        $subprojects = $this->subprojects;
        $directTasks = $this->tasks()->whereNull('subproject_id')->get();

        $totalItems = $subprojects->count() + $directTasks->count();

        if ($totalItems === 0) {
            $status = 'Belum Mulai';
        } else {
            $allSelesai = true;
            $allBelumMulai = true;

            foreach ($subprojects as $sp) {
                if ($sp->status !== 'Selesai') {
                    $allSelesai = false;
                }
                if ($sp->status !== 'Belum Mulai') {
                    $allBelumMulai = false;
                }
            }

            foreach ($directTasks as $t) {
                if ($t->status !== 'Selesai' && $t->progress !== 100) {
                    $allSelesai = false;
                }
                if ($t->status !== 'Belum Mulai' || $t->progress > 0) {
                    $allBelumMulai = false;
                }
            }

            if ($allSelesai) {
                $status = 'Selesai';
            } elseif ($allBelumMulai) {
                $status = 'Belum Mulai';
            } else {
                $status = 'Berjalan';
            }
        }

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }
}
