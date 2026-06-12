<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;

class DashboardService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepo,
        private TaskRepositoryInterface $taskRepo,
    ) {}

    public function getStats(int $year): array
    {
        $projects = $this->projectRepo->all(['year' => $year]);
        $subprojects = \App\Models\Subproject::whereHas('project', fn($q) => $q->where('year', $year))->get();
        
        $tasksQuery = Task::whereHas('project', fn($q) => $q->where('year', $year));
        if (auth()->check() && !auth()->user()->hasCrudAccess()) {
            $tasksQuery->whereHas('pics', fn($q) => $q->where('users.id', auth()->id()));
        }
        $tasks = $tasksQuery->get();
        
        $overdue  = $this->taskRepo->getOverdue();

        $totalProgress = $projects->count()
            ? $projects->map(fn($p) => $p->progress)->avg()
            : 0;

        // Fungsi bantuan untuk menghitung kesesuaian waktu
        $calculateTiming = function($items, $dateField = 'end_date') {
            $result = ['tepat' => 0, 'telat' => 0, 'maju' => 0];
            $now = now()->startOfDay();
            
            foreach ($items as $item) {
                if ($item->status === 'Selesai' || !empty($item->actual_end_date)) {
                    $delay = $item->delay_days ?? 0;
                    if ($delay > 0) $result['telat']++;
                    elseif ($delay < 0) $result['maju']++;
                    else $result['tepat']++;
                } else {
                    // Belum selesai, cek apakah sudah lewat deadline dari waktu sekarang
                    $due = $item->{$dateField} ? \Carbon\Carbon::parse($item->{$dateField})->startOfDay() : null;
                    if ($due && $due->lt($now)) {
                        $result['telat']++;
                    } else {
                        $result['tepat']++;
                    }
                }
            }
            return $result;
        };

        return [
            'total_projects'    => $projects->count(),
            'active_projects'   => $projects->where('status', 'Berjalan')->count(),
            'total_tasks'       => $tasks->count(),
            'done_tasks'        => $tasks->where('status', 'Selesai')->count(),
            'ongoing_tasks'     => $tasks->where('status', 'Berjalan')->count(),
            'not_started_tasks' => $tasks->where('status', 'Belum Mulai')->count(),
            'overdue_count'     => $overdue->where(fn($t) => in_array($t->project->year ?? 0, [$year]))->count(),
            'year_progress'     => (int) round($totalProgress),
            'projects'          => $projects,
            'timing_stats'      => [
                'projects'    => $calculateTiming($projects, 'end_date'),
                'subprojects' => $calculateTiming($subprojects, 'end_date'),
                'tasks'       => $calculateTiming($tasks, 'due_date'),
            ]
        ];
    }

    public function getOverdue()
    {
        return $this->taskRepo->getOverdue();
    }

    public function getUpcomingDeadlines(int $days = 7)
    {
        return $this->taskRepo->getUpcomingDeadlines($days);
    }

    public function getActiveTasks(int $year, int $limit = 10)
    {
        $query = Task::with(['project', 'pics'])
            ->whereHas('project', fn($q) => $q->where('year', $year))
            ->where('status', '!=', 'Selesai')
            ->orderBy('due_date')
            ->limit($limit);

        if (auth()->check() && !auth()->user()->hasCrudAccess()) {
            $query->whereHas('pics', fn($q) => $q->where('users.id', auth()->id()));
        }

        return $query->get();
    }
}
