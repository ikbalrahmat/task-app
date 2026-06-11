@extends('layouts.app')
@section('title', 'Kalender')
@section('heading', 'Kalender')
@section('subheading', 'Pantau jadwal dan deadline task bulanan')

@push('styles')
<style>
.cal-day { min-height: 120px; }
.cal-event { font-size: 10px; padding: 4px 8px; border-radius: 6px; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; }
</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;
    $currentDate = Carbon::create($year, $month, 1);
    $prevMonth   = $currentDate->copy()->subMonth();
    $nextMonth   = $currentDate->copy()->addMonth();
    $daysInMonth = $currentDate->daysInMonth;
    $startDow    = $currentDate->dayOfWeek; // 0=Sun
    $today       = Carbon::today();
    $dayNames    = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $monthNames  = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    // Group tasks by dates
    $tasksByDate = [];
    foreach($tasks as $task) {
        if($task->start_date) {
            $key = $task->start_date->format('Y-m-d');
            $tasksByDate[$key][] = ['task' => $task, 'type' => 'start'];
        }
        if($task->due_date) {
            $key = $task->due_date->format('Y-m-d');
            $tasksByDate[$key][] = ['task' => $task, 'type' => 'due'];
        }
    }
    
    // Generate Year List (e.g. from 2020 to current year + 5)
    $currentYear = date('Y');
    $yearsList = range($currentYear - 3, $currentYear + 3);
@endphp

{{-- Header Actions & Nav --}}
<div class="flex flex-col xl:flex-row items-center justify-between gap-6 mb-8 bg-white/80 backdrop-blur-md border border-white/60 p-4 sm:p-6 rounded-3xl shadow-xl shadow-blue-900/5">
    {{-- Prev/Next & Today --}}
    <div class="flex items-center gap-3 w-full xl:w-auto justify-between xl:justify-start">
        <div class="flex items-center gap-2">
            <a href="{{ route('calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
               class="flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 rounded-xl shadow-sm transition-all" title="Bulan Sebelumnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="{{ route('calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
               class="flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 rounded-xl shadow-sm transition-all" title="Bulan Berikutnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        
        <a href="{{ route('calendar', ['month' => $today->month, 'year' => $today->year]) }}"
           class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-4 py-2 rounded-xl text-sm font-bold transition-colors border border-blue-100 flex items-center gap-2">
           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
           Ke Bulan Ini
        </a>
    </div>

    {{-- Month/Year Selector Form --}}
    <form method="GET" action="{{ route('calendar') }}" class="flex items-center gap-3 w-full xl:w-auto">
        <div class="relative flex-1 xl:w-48">
            <select name="month" onchange="this.form.submit()" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-800 font-bold rounded-xl pl-4 pr-10 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 cursor-pointer transition-all">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $monthNames[$m] }}</option>
                @endfor
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
        </div>

        <div class="relative flex-1 xl:w-32">
            <select name="year" onchange="this.form.submit()" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-800 font-bold rounded-xl pl-4 pr-10 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 cursor-pointer transition-all">
                @foreach($yearsList as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
        </div>
    </form>
    
    {{-- Legend --}}
    <div class="flex items-center justify-center gap-5 bg-slate-50 px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 w-full xl:w-auto">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-md bg-emerald-400 shadow-sm shadow-emerald-400/50"></span> Mulai</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-md bg-rose-400 shadow-sm shadow-rose-400/50"></span> Deadline</div>
    </div>
</div>

{{-- Calendar Grid --}}
<div class="bg-white/90 backdrop-blur-md border border-white/60 rounded-3xl overflow-hidden shadow-xl shadow-blue-900/5">
    {{-- Day Names --}}
    <div class="grid grid-cols-7 bg-slate-50/80 border-b border-slate-100/80">
        @foreach($dayNames as $d)
            <div class="py-4 text-center text-xs font-black text-slate-400 uppercase tracking-widest">{{ $d }}</div>
        @endforeach
    </div>
    
    {{-- Days Grid --}}
    <div class="grid grid-cols-7 divide-x divide-y divide-slate-100/80">
        {{-- Empty cells before start of month --}}
        @for($i = 0; $i < $startDow; $i++)
            <div class="cal-day p-2 bg-slate-50/30"></div>
        @endfor

        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dateStr = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isToday  = $dateStr === $today->format('Y-m-d');
                $dayTasks = $tasksByDate[$dateStr] ?? [];
                
                // Determine weekend (0=Sun, 6=Sat)
                $currentDow = ($startDow + $day - 1) % 7;
                $isWeekend = ($currentDow == 0 || $currentDow == 6);
            @endphp
            
            <div class="cal-day p-2.5 transition-colors {{ $isToday ? 'bg-blue-50/50 ring-1 ring-inset ring-blue-200' : ($isWeekend ? 'bg-slate-50/30 hover:bg-slate-50/80' : 'hover:bg-slate-50/80') }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center justify-center w-8 h-8 rounded-xl {{ $isToday ? 'bg-gradient-to-br from-blue-500 to-blue-700 text-white font-black shadow-lg shadow-blue-500/30' : ($isWeekend ? 'text-rose-500 font-bold bg-rose-50' : 'text-slate-600 font-bold bg-slate-100/50') }} text-sm">
                        {{ $day }}
                    </div>
                    @if(count($dayTasks) > 0)
                        <span class="text-[10px] font-bold text-slate-400 bg-white px-1.5 py-0.5 rounded-md border border-slate-100">{{ count($dayTasks) }}</span>
                    @endif
                </div>
                
                <div class="space-y-1">
                    @foreach(array_slice($dayTasks, 0, 3) as $item)
                        <a href="{{ route('tasks.show', ['task' => $item['task']->id, 'source' => 'calendar', 'month' => $month, 'year' => $year]) }}"
                           title="{{ $item['task']->name }} ({{ $item['task']->project->name ?? 'No Project' }})"
                           class="cal-event block border transition-all hover:-translate-y-px hover:shadow-md font-semibold
                                  {{ $item['type'] === 'start' ? 'bg-gradient-to-r from-emerald-50 to-emerald-100/50 text-emerald-700 border-emerald-200/60 hover:shadow-emerald-500/10' : 'bg-gradient-to-r from-rose-50 to-rose-100/50 text-rose-700 border-rose-200/60 hover:shadow-rose-500/10' }}">
                            <div class="truncate">{{ $item['task']->name }}</div>
                        </a>
                    @endforeach
                    
                    @if(count($dayTasks) > 3)
                        <div class="text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100 rounded-md py-1 px-2 text-center mt-1">
                            +{{ count($dayTasks) - 3 }} Lainnya
                        </div>
                    @endif
                </div>
            </div>
        @endfor
        
        {{-- Empty cells to complete the last row --}}
        @php
            $totalCells = $startDow + $daysInMonth;
            $remainingCells = $totalCells % 7 == 0 ? 0 : 7 - ($totalCells % 7);
        @endphp
        @for($i = 0; $i < $remainingCells; $i++)
            <div class="cal-day p-2 bg-slate-50/30"></div>
        @endfor
    </div>
</div>
@endsection
