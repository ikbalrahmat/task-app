<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class GanttController extends Controller
{
    public function __construct(private ProjectService $service) {}

    public function index(Request $request)
    {
        $year = $request->integer('year', date('Y'));

        // Ambil tahun dari data proyek yang ada di database
        $yearsFromData = Project::selectRaw('YEAR(start_date) as y')
            ->whereNotNull('start_date')
            ->distinct()
            ->orderBy('y')
            ->pluck('y')
            ->toArray();

        // Selalu sertakan tahun yang sedang dipilih + tahun sekarang
        $availableYears = collect($yearsFromData)
            ->push((int) date('Y'))
            ->push($year)
            ->unique()
            ->sort()
            ->values();

        $projects = $this->service->all(['year' => $year])->sortBy('start_date')->values();

        return view('gantt.index', compact('year', 'projects', 'availableYears'));
    }
}

