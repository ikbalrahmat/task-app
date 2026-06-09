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
       class="flex items-center gap-2 bg-[#1a1d27] border border-[#333650] text-slate-300 hover:text-white hover:border-blue-500 px-4 py-2 rounded-xl text-sm transition-all">
        ← Sebelumnya
    </a>
    <h2 class="text-xl font-bold text-white">{{ $monthNames[$month] }} {{ $year }}</h2>
    <a href="{{ route('calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
       class="flex items-center gap-2 bg-[#1a1d27] border border-[#333650] text-slate-300 hover:text-white hover:border-blue-500 px-4 py-2 rounded-xl text-sm transition-all">
        Berikutnya →
    </a>
</div>

{{-- Legend --}}
<div class="flex items-center gap-4 mb-4 text-xs">
    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Tanggal Mulai</div>
    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Due Date</div>
</div>

<div class="bg-[#1a1d27] border border-[#333650] rounded-2xl overflow-hidden">
    {{-- Day Names --}}
    <div class="grid grid-cols-7 bg-[#222535]">
        @foreach($dayNames as $d)
            <div class="py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $d }}</div>
        @endforeach
    </div>
    {{-- Days Grid --}}
    <div class="grid grid-cols-7 divide-x divide-y divide-[#333650] border-t border-[#333650]">
        @for($i = 0; $i < $startDow; $i++)
            <div class="cal-day p-2 bg-[#111320]"></div>
        @endfor

        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dateStr = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                $isToday  = $dateStr === $today->format('Y-m-d');
                $dayTasks = $tasksByDate[$dateStr] ?? [];
            @endphp
            <div class="cal-day p-2 {{ $isToday ? 'bg-blue-950/30' : '' }} hover:bg-[#2a2e42] transition-colors">
                <div class="flex items-center justify-center w-7 h-7 rounded-full mb-1 {{ $isToday ? 'bg-blue-500 text-white font-bold' : 'text-slate-400' }} text-sm">
                    {{ $day }}
                </div>
                @foreach(array_slice($dayTasks, 0, 3) as $item)
                    <a href="{{ route('tasks.show', $item['task']->id) }}"
                       title="{{ $item['task']->name }}"
                       class="cal-event block {{ $item['type'] === 'start' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                        {{ $item['task']->name }}
                    </a>
                @endforeach
                @if(count($dayTasks) > 3)
                    <div class="text-[9px] text-slate-400 pl-1">+{{ count($dayTasks) - 3 }} lainnya</div>
                @endif
            </div>
        @endfor
    </div>
</div>

<div class="mt-4 flex items-center justify-center">
    <a href="{{ route('calendar', ['month' => $today->month, 'year' => $today->year]) }}"
       class="bg-[#222535] border border-[#333650] text-slate-300 hover:text-white px-4 py-2 rounded-xl text-sm transition-colors">
        Hari Ini
    </a>
</div>
@endsection
