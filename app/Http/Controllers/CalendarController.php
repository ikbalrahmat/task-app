<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __construct(private TaskService $service) {}

    public function index(Request $request)
    {
        $month = $request->integer('month', date('n'));
        $year  = $request->integer('year', date('Y'));
        $tasks = $this->service->all(['year' => $year]); // Simple implementation, passing tasks
        
        return view('calendar.index', compact('month', 'year', 'tasks'));
    }
}
