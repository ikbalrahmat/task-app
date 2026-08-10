<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Models\UnitKerja;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index(Request $request)
    {
        $user = auth()->user();

        // Super Admin: redirect ke overview semua unit kerja
        if ($user->isSuperAdmin()) {
            $unitKerjas = UnitKerja::withCount(['users', 'projects'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $totalUsers     = User::withoutGlobalScopes()->whereNotNull('unit_kerja_id')->count();
            $totalProjects  = Project::withoutGlobalScopes()->count();
            $totalTasks     = Task::withoutGlobalScopes()->count();
            $totalUnitKerja = UnitKerja::count();

            return view('dashboard.super-admin', compact(
                'unitKerjas', 'totalUsers', 'totalProjects', 'totalTasks', 'totalUnitKerja'
            ));
        }

        $year  = $request->integer('year', date('Y'));
        $stats = $this->service->getStats($year);

        $availableYears = \App\Models\Project::select('year')->distinct()->pluck('year')->toArray();
        if (!in_array(date('Y'), $availableYears)) $availableYears[] = date('Y');
        if (!in_array($year, $availableYears)) $availableYears[] = $year;
        rsort($availableYears);

        $data = [
            'year'              => $year,
            'stats'             => $stats,
            'overdue'           => $this->service->getOverdue(),
            'upcomingDeadlines' => $this->service->getUpcomingDeadlines(7),
            'activeTasks'       => $this->service->getActiveTasks($year),
            'years'             => $availableYears,
        ];

        if ($user->isAdmin()) {
            $data['totalUsers'] = \App\Models\User::count();
            return view('dashboard.admin', $data);
        } elseif ($user->isManager() || $user->isMember()) {
            $data['teamWorkload'] = \App\Models\User::withCount(['tasks' => fn($q) => $q->where('status', '!=', 'Selesai')])
                ->having('tasks_count', '>', 0)
                ->orderByDesc('tasks_count')
                ->take(10)
                ->get();
            return view('dashboard.manager', $data);
        } else {
            $myTotalTasks = \App\Models\Task::whereHas('pics', fn($q) => $q->where('users.id', $user->id))->count();
            $myDoneTasks = \App\Models\Task::whereHas('pics', fn($q) => $q->where('users.id', $user->id))->where('status', 'Selesai')->count();
            $myOverdueTasks = \App\Models\Task::whereHas('pics', fn($q) => $q->where('users.id', $user->id))
                ->where('status', '!=', 'Selesai')
                ->whereDate('due_date', '<', today())
                ->count();

            $data['myStats'] = [
                'total'   => $myTotalTasks,
                'done'    => $myDoneTasks,
                'overdue' => $myOverdueTasks,
            ];

            $data['myTasksToday'] = \App\Models\Task::whereHas('pics', fn($q) => $q->where('users.id', $user->id))
                ->whereDate('due_date', today())
                ->where('status', '!=', 'Selesai')
                ->get();

            $data['overdue'] = \App\Models\Task::whereHas('pics', fn($q) => $q->where('users.id', $user->id))
                ->where('status', '!=', 'Selesai')
                ->whereDate('due_date', '<', today())
                ->get();

            $data['upcomingDeadlines'] = \App\Models\Task::whereHas('pics', fn($q) => $q->where('users.id', $user->id))
                ->where('status', '!=', 'Selesai')
                ->whereDate('due_date', '>=', today())
                ->whereDate('due_date', '<=', today()->addDays(7))
                ->orderBy('due_date')
                ->get();

            $data['activeTasks'] = \App\Models\Task::whereHas('pics', fn($q) => $q->where('users.id', $user->id))
                ->where('status', 'Berjalan')
                ->get();

            return view('dashboard.viewer', $data);
        }
    }
}
