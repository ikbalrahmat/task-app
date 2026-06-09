@extends('layouts.app')
@section('title', 'Kalender')
@section('heading', 'Kalender')
@section('subheading', 'Tampilan bulanan task berdasarkan tanggal')

@push('styles')
<style>
.cal-day { min-height: 100px; }
.cal-event { font-size: 10px; padding: 2px 6px; border-radius: 4px; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; }
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
    $dayNames    = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
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
@endphp

{{-- Nav --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
       class="flex items-center gap-2 bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm transition-all">
        ← Sebelumnya
    </a>
    <h2 class="text-xl font-bold text-slate-800">{{ $monthNames[$month] }} {{ $year }}</h2>
    <a href="{{ route('calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
       class="flex items-center gap-2 bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm transition-all">
        Berikutnya →
    </a>
</div>

{{-- Legend --}}
<div class="flex items-center gap-4 mb-4 text-xs text-slate-500 font-medium">
    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Tanggal Mulai</div>
    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span> Due Date</div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    {{-- Day Names --}}
    <div class="grid grid-cols-7 bg-slate-50 border-b border-slate-200">
        @foreach($dayNames as $d)
            <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $d }}</div>
        @endforeach
    </div>
    {{-- Days Grid --}}
    <div class="grid grid-cols-7 divide-x divide-y divide-slate-100 border-t border-slate-100">
        @for($i = 0; $i < $startDow; $i++)
            <div class="cal-day p-2 bg-slate-50/50"></div>
        @endfor

        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dateStr = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isToday  = $dateStr === $today->format('Y-m-d');
                $dayTasks = $tasksByDate[$dateStr] ?? [];
            @endphp
            <div class="cal-day p-2 {{ $isToday ? 'bg-blue-50/40' : '' }} hover:bg-slate-50/80 transition-colors">
                <div class="flex items-center justify-center w-7 h-7 rounded-full mb-1.5 {{ $isToday ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-500/20' : 'text-slate-500 font-semibold' }} text-xs">
                    {{ $day }}
                </div>
                @foreach(array_slice($dayTasks, 0, 3) as $item)
                    <a href="{{ route('tasks.show', $item['task']->id) }}"
                       title="{{ $item['task']->name }}"
                       class="cal-event block border transition-all {{ $item['type'] === 'start' ? 'bg-emerald-50/60 text-emerald-700 border-emerald-100 hover:bg-emerald-100/50' : 'bg-rose-50/60 text-rose-700 border-rose-100 hover:bg-rose-100/50' }}">
                        {{ $item['task']->name }}
                    </a>
                @endforeach
                @if(count($dayTasks) > 3)
                    <div class="text-[9px] font-medium text-slate-400 pl-1 mt-1">+{{ count($dayTasks) - 3 }} lainnya</div>
                @endif
            </div>
        @endfor
    </div>
</div>

<div class="mt-6 flex items-center justify-center">
    <a href="{{ route('calendar', ['month' => $today->month, 'year' => $today->year]) }}"
       class="bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all">
        Hari Ini
    </a>
</div>
@endsection
