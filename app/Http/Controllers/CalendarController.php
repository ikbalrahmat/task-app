<?php

namespace App\Http\Controllers;

use App\Services\IndonesiaHolidayService;
use App\Services\TaskService;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __construct(
        private TaskService $service,
        private IndonesiaHolidayService $holidayService,
    ) {}

    public function index(Request $request)
    {
        $month    = $request->integer('month', date('n'));
        $year     = $request->integer('year', date('Y'));
        $tasks    = $this->service->all(['year' => $year]);

        // Ambil semua hari libur Indonesia untuk tahun yang dipilih
        // Format: ['Y-m-d' => 'Nama Hari Libur']
        $holidays = $this->holidayService->getHolidays($year);

        return view('calendar.index', compact('month', 'year', 'tasks', 'holidays'));
    }
}
