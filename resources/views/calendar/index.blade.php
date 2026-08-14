@extends('layouts.app')
@section('title', 'Kalender')
@section('heading', 'Kalender')
@section('subheading', 'Pantau jadwal, deadline task, dan hari libur nasional Indonesia')

@push('styles')
<style>
.cal-day { min-height: 120px; }

/* ── Hari libur nasional (merah) ── */
.cal-day.is-national    { background: linear-gradient(135deg, #fff5f5, #fef2f2); }
.cal-day.is-national:hover { background: linear-gradient(135deg, #fee2e2, #fecaca); }

/* ── Cuti bersama (oranye/amber) ── */
.cal-day.is-cuti        { background: linear-gradient(135deg, #fffbeb, #fef3c7); }
.cal-day.is-cuti:hover  { background: linear-gradient(135deg, #fde68a, #fcd34d); }

.holiday-chip {
    font-size: 9px; font-weight: 700;
    border-radius: 5px; padding: 2px 5px;
    line-height: 1.3; display: block;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 3px;
}
.chip-national { color: #dc2626; background: #fee2e2; border: 1px solid #fca5a5; }
.chip-cuti     { color: #b45309; background: #fef3c7; border: 1px solid #fcd34d; }

.cal-event {
    font-size: 10px; padding: 4px 8px; border-radius: 6px;
    margin-bottom: 4px; white-space: nowrap; overflow: hidden;
    text-overflow: ellipsis; cursor: pointer;
}
</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;
    $currentDate = Carbon::create($year, $month, 1);
    $prevMonth   = $currentDate->copy()->subMonth();
    $nextMonth   = $currentDate->copy()->addMonth();
    $daysInMonth = $currentDate->daysInMonth;
    $startDow    = $currentDate->dayOfWeek;
    $today       = Carbon::today();
    $dayNames    = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $monthNames  = ['','Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'];

    // Group tasks by date
    $tasksByDate = [];
    foreach($tasks as $task) {
        if($task->start_date) $tasksByDate[$task->start_date->format('Y-m-d')][] = ['task'=>$task,'type'=>'start'];
        if($task->due_date)   $tasksByDate[$task->due_date->format('Y-m-d')][]   = ['task'=>$task,'type'=>'due'];
    }

    $yearsList = range(date('Y')-3, date('Y')+3);

    // Hari libur bulan ini
    $holidaysThisMonth = collect($holidays)->filter(
        fn($h, $d) => str_starts_with($d, sprintf('%d-%02d', $year, $month))
    );
    $nationalCount = $holidaysThisMonth->filter(fn($h) => $h['is_national'])->count();
    $cutiCount     = $holidaysThisMonth->filter(fn($h) => !$h['is_national'])->count();
@endphp

{{-- ── Header Nav ── --}}
<div class="flex flex-col xl:flex-row items-center justify-between gap-6 mb-6 bg-white/80 backdrop-blur-md border border-white/60 p-4 sm:p-6 rounded-3xl shadow-xl shadow-blue-900/5">
    <div class="flex items-center gap-3 w-full xl:w-auto justify-between xl:justify-start">
        <div class="flex items-center gap-2">
            <a href="{{ route('calendar', ['month'=>$prevMonth->month,'year'=>$prevMonth->year]) }}"
               class="flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 rounded-xl shadow-sm transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="{{ route('calendar', ['month'=>$nextMonth->month,'year'=>$nextMonth->year]) }}"
               class="flex items-center justify-center w-10 h-10 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-200 rounded-xl shadow-sm transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <a href="{{ route('calendar', ['month'=>$today->month,'year'=>$today->year]) }}"
           class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-4 py-2 rounded-xl text-sm font-bold transition-colors border border-blue-100 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Ke Bulan Ini
        </a>
    </div>

    <form method="GET" action="{{ route('calendar') }}" class="flex items-center gap-3 w-full xl:w-auto">
        <div class="relative flex-1 xl:w-48">
            <select name="month" onchange="this.form.submit()" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-800 font-bold rounded-xl pl-4 pr-10 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 cursor-pointer transition-all">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ $monthNames[$m] }}</option>
                @endfor
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
        </div>
        <div class="relative flex-1 xl:w-32">
            <select name="year" onchange="this.form.submit()" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-800 font-bold rounded-xl pl-4 pr-10 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 cursor-pointer transition-all">
                @foreach($yearsList as $y)
                    <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
        </div>
    </form>

    {{-- Legend --}}
    <div class="flex items-center flex-wrap gap-4 bg-slate-50 px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 w-full xl:w-auto">
        <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-emerald-400"></span> Mulai Task</div>
        <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-rose-400"></span> Deadline</div>
        <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-red-300 border border-red-400"></span> Libur Nasional
            @if($nationalCount) <span class="bg-red-100 text-red-600 text-[10px] font-black px-1.5 py-0.5 rounded-md border border-red-200">{{ $nationalCount }}</span> @endif
        </div>
        <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-amber-300 border border-amber-400"></span> Cuti Bersama
            @if($cutiCount) <span class="bg-amber-100 text-amber-700 text-[10px] font-black px-1.5 py-0.5 rounded-md border border-amber-200">{{ $cutiCount }}</span> @endif
        </div>
    </div>
</div>

{{-- ── Banner Hari Libur Bulan Ini ── --}}
@if($holidaysThisMonth->count() > 0)
<div class="mb-6 bg-white border border-slate-200 rounded-2xl px-5 py-4 shadow-sm">
    <p class="text-xs font-black text-slate-600 mb-3 flex items-center gap-2">
        🗓️ Hari Libur & Cuti Bersama — <span class="text-blue-600">{{ $monthNames[$month] }} {{ $year }}</span>
    </p>
    <div class="flex flex-wrap gap-2">
        @foreach($holidaysThisMonth->sortKeys() as $date => $holiday)
            @php $d = \Carbon\Carbon::parse($date); @endphp
            <span class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl border shadow-sm
                {{ $holiday['is_national']
                    ? 'bg-red-50 text-red-700 border-red-200'
                    : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                {{ $holiday['is_national'] ? '🔴' : '🟡' }}
                <span class="text-slate-500 font-black">{{ $d->format('d') }}</span>
                {{ $holiday['name'] }}
            </span>
        @endforeach
    </div>
</div>
@endif

{{-- ── Calendar Grid ── --}}
<div class="bg-white/90 backdrop-blur-md border border-white/60 rounded-3xl overflow-hidden shadow-xl shadow-blue-900/5">
    {{-- Day Header --}}
    <div class="grid grid-cols-7 bg-slate-50/80 border-b border-slate-100">
        @foreach($dayNames as $idx => $d)
            <div class="py-4 text-center text-xs font-black uppercase tracking-widest
                        {{ $idx==0||$idx==6 ? 'text-rose-400' : 'text-slate-400' }}">{{ $d }}</div>
        @endforeach
    </div>

    {{-- Days --}}
    <div class="grid grid-cols-7 divide-x divide-y divide-slate-100/80">
        @for($i=0;$i<$startDow;$i++)
            <div class="cal-day p-2 bg-slate-50/30"></div>
        @endfor

        @for($day=1;$day<=$daysInMonth;$day++)
            @php
                $dateStr     = sprintf('%d-%02d-%02d', $year, $month, $day);
                $isToday     = $dateStr === $today->format('Y-m-d');
                $dayTasks    = $tasksByDate[$dateStr] ?? [];
                $holiday     = $holidays[$dateStr] ?? null;
                $isNational  = $holiday && $holiday['is_national'];
                $isCuti      = $holiday && !$holiday['is_national'];
                $currentDow  = ($startDow + $day - 1) % 7;
                $isWeekend   = in_array($currentDow, [0, 6]);
                $maxEvents   = $holiday ? 2 : 3;
            @endphp

            <div class="cal-day p-2.5 transition-colors
                {{ $isNational ? 'is-national' : '' }}
                {{ $isCuti     ? 'is-cuti' : '' }}
                {{ $isToday    ? 'ring-1 ring-inset ring-blue-300 bg-blue-50/60' : '' }}
                {{ !$holiday && !$isToday && $isWeekend  ? 'bg-slate-50/30 hover:bg-slate-50/80' : '' }}
                {{ !$holiday && !$isToday && !$isWeekend ? 'hover:bg-slate-50/80' : '' }}">

                {{-- Angka Tanggal --}}
                <div class="flex items-center justify-between mb-1.5">
                    <div title="{{ $holiday['name'] ?? '' }}"
                         class="flex items-center justify-center w-8 h-8 rounded-xl text-sm font-bold
                         {{ $isToday
                             ? 'bg-gradient-to-br from-blue-500 to-blue-700 text-white font-black shadow-lg shadow-blue-500/30'
                             : ($isNational
                                 ? 'bg-red-100 text-red-600 font-black ring-1 ring-red-300'
                                 : ($isCuti
                                     ? 'bg-amber-100 text-amber-700 font-black ring-1 ring-amber-300'
                                     : ($isWeekend
                                         ? 'text-rose-500 bg-rose-50'
                                         : 'text-slate-600 bg-slate-100/50'))) }}">
                        {{ $day }}
                    </div>
                    @if(count($dayTasks)>0)
                        <span class="text-[10px] font-bold text-slate-400 bg-white px-1.5 py-0.5 rounded-md border border-slate-100">{{ count($dayTasks) }}</span>
                    @endif
                </div>

                <div class="space-y-1">
                    {{-- Chip hari libur --}}
                    @if($holiday)
                        <span class="holiday-chip {{ $isNational ? 'chip-national' : 'chip-cuti' }}"
                              title="{{ $holiday['name'] }}">
                            {{ $isNational ? '🔴' : '🟡' }} {{ $holiday['name'] }}
                        </span>
                    @endif

                    {{-- Task events --}}
                    @foreach(array_slice($dayTasks, 0, $maxEvents) as $item)
                        <a href="{{ route('tasks.show', ['task'=>$item['task']->id,'source'=>'calendar','month'=>$month,'year'=>$year]) }}"
                           title="{{ $item['task']->name }}"
                           class="cal-event block border transition-all hover:-translate-y-px hover:shadow-md font-semibold
                                  {{ $item['type']==='start'
                                      ? 'bg-gradient-to-r from-emerald-50 to-emerald-100/50 text-emerald-700 border-emerald-200/60 hover:shadow-emerald-500/10'
                                      : 'bg-gradient-to-r from-rose-50 to-rose-100/50 text-rose-700 border-rose-200/60 hover:shadow-rose-500/10' }}">
                            <div class="truncate">{{ $item['task']->name }}</div>
                        </a>
                    @endforeach

                    @if(count($dayTasks) > $maxEvents)
                        <div class="text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100 rounded-md py-1 px-2 text-center">
                            +{{ count($dayTasks)-$maxEvents }} Lainnya
                        </div>
                    @endif
                </div>
            </div>
        @endfor

        @php
            $total     = $startDow + $daysInMonth;
            $remaining = $total % 7 == 0 ? 0 : 7 - ($total % 7);
        @endphp
        @for($i=0;$i<$remaining;$i++)
            <div class="cal-day p-2 bg-slate-50/30"></div>
        @endfor
    </div>
</div>
@endsection
