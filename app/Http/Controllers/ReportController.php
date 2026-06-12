<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $tab  = $request->query('tab', 'schedule');
        $year = $request->integer('year', date('Y'));
        
        $projects = collect();
        $annualStats = [];

        $scheduleStats = [];
        $scheduleDetails = collect();
        $chartData = [];

        if ($tab === 'progress') {
            $projects = $this->projectService->all(['year' => $year]);
        } elseif ($tab === 'annual') {
            $years = \App\Models\Project::select('year')->distinct()->pluck('year')->toArray();
            if (empty($years)) {
                $years = [date('Y')];
            }
            sort($years);
            foreach ($years as $y) {
                $annualStats[$y] = $this->dashboardService->getStats($y);
            }
        } elseif ($tab === 'schedule') {
            $allProjects = \App\Models\Project::where('year', $year)->get();
            $allSubprojects = \App\Models\Subproject::whereHas('project', function($q) use ($year) {
                $q->where('year', $year);
            })->get();
            $allTasks = \App\Models\Task::whereHas('project', function($q) use ($year) {
                $q->where('year', $year);
            })->get();

            $evaluate = function($item, $type) use (&$scheduleDetails) {
                $hasStart = $item->start_date && $item->actual_start_date;
                $hasEnd = ($item->end_date ?? $item->due_date) && $item->actual_end_date;

                $startDiff = 0;
                $endDiff = 0;

                if ($hasStart) {
                    $startDiff = (int) \Carbon\Carbon::parse($item->start_date)->startOfDay()->diffInDays(\Carbon\Carbon::parse($item->actual_start_date)->startOfDay(), false);
                }

                if ($hasEnd) {
                    $plannedEnd = $item->end_date ?? $item->due_date;
                    $endDiff = (int) \Carbon\Carbon::parse($plannedEnd)->startOfDay()->diffInDays(\Carbon\Carbon::parse($item->actual_end_date)->startOfDay(), false);
                }

                if (($hasStart && $startDiff != 0) || ($hasEnd && $endDiff != 0)) {
                    $scheduleDetails->push([
                        'type' => $type,
                        'name' => $item->name,
                        'start_diff' => $hasStart ? $startDiff : null,
                        'end_diff' => $hasEnd ? $endDiff : null,
                        'status' => $item->status,
                        'id' => $item->id,
                        'project_id' => $item->project_id ?? $item->id
                    ]);
                }
            };

            $allProjects->each(fn($p) => $evaluate($p, 'Project'));
            $allSubprojects->each(fn($sp) => $evaluate($sp, 'Subproject'));
            $allTasks->each(fn($t) => $evaluate($t, 'Task'));

            $chartData = [];

            $buildChartData = function($item, $type) use (&$chartData) {
                if ($item->start_date || $item->end_date || $item->due_date) {
                    $plannedEnd = $item->end_date ?? $item->due_date;
                    $chartData[] = [
                        'type' => $type,
                        'name' => \Illuminate\Support\Str::limit($item->name, 20),
                        'plan_start' => $item->start_date ? \Carbon\Carbon::parse($item->start_date)->startOfDay()->timestamp * 1000 : null,
                        'actual_start' => $item->actual_start_date ? \Carbon\Carbon::parse($item->actual_start_date)->startOfDay()->timestamp * 1000 : null,
                        'plan_end' => $plannedEnd ? \Carbon\Carbon::parse($plannedEnd)->startOfDay()->timestamp * 1000 : null,
                        'actual_end' => $item->actual_end_date ? \Carbon\Carbon::parse($item->actual_end_date)->startOfDay()->timestamp * 1000 : null,
                    ];
                }
            };

            $allProjects->each(fn($p) => $buildChartData($p, 'Project'));
            $allSubprojects->each(fn($sp) => $buildChartData($sp, 'Subproject'));
            $allTasks->each(fn($t) => $buildChartData($t, 'Task'));
        }

        return view('reports.index', compact('tab', 'year', 'projects', 'annualStats', 'scheduleDetails', 'chartData'));
    }
}
