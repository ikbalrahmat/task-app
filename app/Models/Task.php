<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'subproject_id',
        'name',
        'start_date',
        'due_date',
        'actual_start_date',
        'actual_end_date',
        'progress',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'due_date'          => 'date',
        'actual_start_date' => 'date',
        'actual_end_date'   => 'date',
        'progress'          => 'integer',
    ];

    const STATUSES = ['Belum Mulai', 'Berjalan', 'Selesai', 'Overdue'];

    // Relations
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function subproject()
    {
        return $this->belongsTo(Subproject::class);
    }

    public function pics()
    {
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    // Helpers
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'Selesai';
    }

    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->due_date) return 0;
        return now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);
    }

    public function getGoogleCalendarUrlAttribute(): string
    {
        $start = $this->start_date ? $this->start_date->format('Ymd') : now()->format('Ymd');
        $end   = $this->due_date  ? $this->due_date->copy()->addDay()->format('Ymd') : now()->addDay()->format('Ymd');
        $title = urlencode('[TaskFlow] ' . $this->name);
        $picNames = $this->pics->pluck('name')->join(', ');
        $desc  = urlencode('Project: ' . ($this->project->name ?? '') . ' | PIC: ' . $picNames);
        return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$start}/{$end}&details={$desc}";
    }

    public function getDelayDaysAttribute(): int
    {
        if (!$this->due_date || !$this->actual_end_date) return 0;
        return (int) $this->due_date->startOfDay()->diffInDays($this->actual_end_date->startOfDay(), false);
    }

    protected static function booted()
    {
        static::saved(function ($task) {
            $task->recalculateParentStatuses();
        });

        static::deleted(function ($task) {
            $task->recalculateParentStatuses();
        });
    }

    public function recalculateParentStatuses()
    {
        if ($this->subproject) {
            $this->subproject->refresh()->recalculateStatus();
        }
        if ($this->project) {
            $this->project->refresh()->recalculateStatus();
        }

        if ($this->isDirty('subproject_id')) {
            $originalSubprojectId = $this->getOriginal('subproject_id');
            if ($originalSubprojectId) {
                $originalSubproject = \App\Models\Subproject::find($originalSubprojectId);
                if ($originalSubproject) $originalSubproject->refresh()->recalculateStatus();
            }
        }
        if ($this->isDirty('project_id')) {
            $originalProjectId = $this->getOriginal('project_id');
            if ($originalProjectId) {
                $originalProject = \App\Models\Project::find($originalProjectId);
                if ($originalProject) $originalProject->refresh()->recalculateStatus();
            }
        }
    }
}
