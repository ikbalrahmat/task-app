<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subproject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'actual_start_date',
        'actual_end_date',
        'actual_start_remarks',
        'actual_end_remarks',
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
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Dynamic progress based on average progress of child tasks
    public function getProgressAttribute(): int
    {
        if ($this->tasks->isEmpty()) return 0;
        return (int) round($this->tasks->avg('progress'));
    }

    public function recalculateStatus(): void
    {
        $tasks = $this->tasks;
        if ($tasks->isEmpty()) {
            $status = 'Belum Mulai';
        } elseif ($tasks->every(fn($t) => $t->status === 'Selesai' || $t->progress === 100)) {
            $status = 'Selesai';
        } elseif ($tasks->every(fn($t) => $t->status === 'Belum Mulai' && $t->progress === 0)) {
            $status = 'Belum Mulai';
        } else {
            $status = 'Berjalan';
        }

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }
}
