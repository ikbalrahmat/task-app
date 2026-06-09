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
        $tab  = $request->query('tab', 'progress');
        $year = $request->integer('year', date('Y'));
        
        $projects = collect();
        $annualStats = [];

        if ($tab === 'progress') {
            $projects = $this->projectService->all(['year' => $year]);
        } else {
            $years = range(date('Y') - 1, date('Y') + 1);
            foreach ($years as $y) {
                $annualStats[$y] = $this->dashboardService->getStats($y);
            }
        }

        return view('reports.index', compact('tab', 'year', 'projects', 'annualStats'));
    }
}
