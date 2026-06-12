<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index(Request $request)
    {
        $year  = $request->integer('year', date('Y'));
        $stats = $this->service->getStats($year);

        $availableYears = \App\Models\Project::select('year')->distinct()->pluck('year')->toArray();
        if (!in_array(date('Y'), $availableYears)) $availableYears[] = date('Y');
        if (!in_array($year, $availableYears)) $availableYears[] = $year;
        rsort($availableYears);

        $recentUsers = auth()->user()->isAdmin() ? \App\Models\User::latest()->take(5)->get() : collect();
        $recentActivities = auth()->user()->isAdmin() ? \App\Models\ActivityLog::with('user')->latest()->take(6)->get() : collect();
        $totalUsers = auth()->user()->isAdmin() ? \App\Models\User::count() : 0;

        return view('dashboard.index', [
            'year'              => $year,
            'stats'             => $stats,
            'overdue'           => $this->service->getOverdue(),
            'upcomingDeadlines' => $this->service->getUpcomingDeadlines(7),
            'activeTasks'       => $this->service->getActiveTasks($year),
            'years'             => $availableYears,
            'recentUsers'       => $recentUsers,
            'recentActivities'  => $recentActivities,
            'totalUsers'        => $totalUsers,
        ]);
    }
}
