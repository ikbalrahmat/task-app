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
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'actual_start_date' => 'date',
        'actual_end_date'   => 'date',
    ];

    const STATUSES = ['Perencanaan', 'Berjalan', 'Selesai', 'Ditunda'];

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
        $tasks = $this->tasks;
        if ($tasks->isEmpty()) return 0;
        return (int) round($tasks->avg('progress'));
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
}
