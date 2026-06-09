<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use Illuminate\Http\Request;

class GanttController extends Controller
{
    public function __construct(private ProjectService $service) {}

    public function index(Request $request)
    {
        $year = $request->integer('year', date('Y'));
        $projects = $this->service->all(['year' => $year]);
        return view('gantt.index', compact('year', 'projects'));
    }
}
