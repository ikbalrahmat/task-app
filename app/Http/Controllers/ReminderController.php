<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index()
    {
        $overdue = $this->service->getOverdue();
        $upcoming = $this->service->getUpcomingDeadlines(7);
        return view('reminders.index', compact('overdue', 'upcoming'));
    }
}
