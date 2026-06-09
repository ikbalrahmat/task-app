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
        $tasks    = Task::whereHas('project', fn($q) => $q->where('year', $year))->get();
        $overdue  = $this->taskRepo->getOverdue();

        $totalProgress = $projects->count()
            ? $projects->map(fn($p) => $p->progress)->avg()
            : 0;

        return [
            'total_projects'  => $projects->count(),
            'active_projects' => $projects->where('status', 'Berjalan')->count(),
            'total_tasks'     => $tasks->count(),
            'done_tasks'      => $tasks->where('status', 'Selesai')->count(),
            'overdue_count'   => $overdue->where(fn($t) => in_array($t->project->year ?? 0, [$year]))->count(),
            'year_progress'   => (int) round($totalProgress),
            'projects'        => $projects,
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
        return Task::with(['project', 'pic'])
            ->whereHas('project', fn($q) => $q->where('year', $year))
            ->where('status', '!=', 'Selesai')
            ->orderBy('due_date')
            ->limit($limit)
            ->get();
    }
}
