@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan seluruh aktivitas program')

@section('content')
{{-- Year Filter --}}
<form method="GET" class="flex items-center gap-3 mb-8">
    <label class="text-sm text-slate-500 font-medium">Tahun:</label>
    <div class="relative">
        <select name="year" onchange="this.form.submit()"
                class="appearance-none bg-white/80 backdrop-blur-md border border-white/50 text-blue-900 font-bold rounded-xl pl-4 pr-10 py-2 text-sm shadow-sm shadow-blue-900/5 focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer transition-all">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>
</form>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            </div>
            <span class="text-[10px] uppercase tracking-widest text-blue-700 bg-blue-100/50 px-2.5 py-1 rounded-lg font-bold">{{ $year }}</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $stats['total_projects'] }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Program</div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-[10px] uppercase tracking-widest text-emerald-700 bg-emerald-100/50 px-2.5 py-1 rounded-lg font-bold">Aktif</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $stats['active_projects'] }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Program Aktif</div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <span class="text-[10px] uppercase tracking-widest text-purple-700 bg-purple-100/50 px-2.5 py-1 rounded-lg font-bold">{{ $stats['done_tasks'] }}/{{ $stats['total_tasks'] }}</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $stats['total_tasks'] }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Task</div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center text-white shadow-lg shadow-rose-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span class="text-[10px] uppercase tracking-widest text-rose-700 bg-rose-100/50 px-2.5 py-1 rounded-lg font-bold">Overdue</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $stats['overdue_count'] }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Task Overdue</div>
    </div>

    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <span class="text-[10px] uppercase tracking-widest text-indigo-700 bg-indigo-100/50 px-2.5 py-1 rounded-lg font-bold">ALL</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $totalUsers }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total User</div>
    </div>
</div>

{{-- Progress Tahunan --}}
<div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-8 mb-8 relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative z-10 items-center">
        {{-- Progress Bar Kiri --}}
        <div class="lg:col-span-2 flex flex-col justify-center">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
                <div>
                    <h2 class="font-bold text-xl text-blue-950">Progress Tahunan {{ $year }}</h2>
                    <p class="text-sm text-slate-500 mt-1">Rata-rata progres seluruh program</p>
                </div>
                <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 mt-2 sm:mt-0">{{ $stats['year_progress'] }}%</span>
            </div>
            <div class="w-full bg-slate-100/80 rounded-full h-4 p-0.5 border border-white shadow-inner">
                <div class="h-full rounded-full transition-all duration-1000 ease-out shadow-sm"
                     style="width: {{ $stats['year_progress'] }}%; background: linear-gradient(90deg, #3b82f6, #6366f1)"></div>
            </div>
        </div>
        
        {{-- Pie Chart 3D Kanan --}}
        <div class="lg:col-span-1 lg:border-l border-slate-200/60 lg:pl-8 flex flex-col items-center justify-center">
            <div id="annualProgressPie3D" class="w-full h-[200px]"></div>
        </div>
    </div>
</div>

{{-- Grafik & Visualisasi --}}
{{-- Bar Chart: Kesesuaian Waktu (Full Width with max-w to prevent "melar") --}}
<div class="max-w-5xl mx-auto w-full mb-8">
    <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8 relative overflow-hidden min-w-0">
        <h2 class="font-bold text-lg text-blue-950 mb-1">Kesesuaian Jadwal (Rencana vs Realisasi)</h2>
        <p class="text-xs text-slate-500 mb-4">Melihat data Program, List, dan Task yang sesuai, lebih cepat, atau terlambat.</p>
        <div id="taskTimelineChart" class="w-full min-h-[280px]"></div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
    {{-- Pie Chart: Status Task --}}
    <div class="xl:col-span-1 bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 relative overflow-hidden flex flex-col h-full">
        <h2 class="font-bold text-lg text-blue-950 mb-1">Status Task</h2>
        <p class="text-xs text-slate-500 mb-4">Distribusi task tahun {{ $year }}</p>
        <div id="taskStatusChart" class="flex-1 flex items-center justify-center -mt-4"></div>
    </div>

    {{-- Progress Per Project --}}
    <div class="xl:col-span-2 bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-lg text-blue-950">Progress Per Program</h2>
            <a href="{{ route('projects.index') }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl font-bold transition-colors">Lihat Semua →</a>
        </div>
        <div class="space-y-6">
            @forelse($stats['projects'] as $project)
            <div class="group" x-data="{ expanded: false }">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1">
                        @if($project->subprojects->isNotEmpty())
                        <button @click="expanded = !expanded" class="p-1 -ml-1 rounded-md hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                            <svg class="w-4 h-4 transition-transform duration-200" :class="expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @endif
                        <a href="{{ route('projects.show', $project->id) }}" class="text-sm font-bold text-slate-700 hover:text-blue-600 transition-colors truncate pr-4">
                            {{ $project->name }}
                        </a>
                    </div>
                    <span class="text-sm font-black text-blue-600 shrink-0">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 mb-2">
                    @php
                        $p = $project->progress;
                        $colorClass = $p >= 75 ? 'from-emerald-400 to-emerald-500' : ($p >= 40 ? 'from-blue-400 to-blue-500' : 'from-amber-400 to-amber-500');
                    @endphp
                    <div class="h-full rounded-full bg-gradient-to-r {{ $colorClass }} transition-all duration-700" style="width: {{ $p }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] font-medium text-slate-400">
                    <span>{{ $project->tasks->where('status', 'Selesai')->count() }}/{{ $project->tasks->count() }} task selesai</span>
                    <span class="uppercase tracking-wider">{{ $project->status }}</span>
                </div>
                @if($project->subprojects->isNotEmpty())
                <div x-show="expanded" style="display: none;" class="mt-4 pl-4 border-l-2 border-slate-100 space-y-3">
                    @foreach($project->subprojects as $sub)
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <a href="{{ route('subprojects.show', $sub->id) }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors truncate pr-4" title="{{ $sub->name }}">
                                ↳ {{ $sub->name }}
                            </a>
                            <span class="text-[10px] font-bold text-slate-400">{{ $sub->progress }}%</span>
                        </div>
                        <div class="w-full bg-slate-50 rounded-full h-1.5">
                            <div class="h-full rounded-full bg-slate-300" style="width: {{ $sub->progress }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-slate-400 text-sm font-medium">Belum ada program di tahun {{ $year }}.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Task Overdue --}}
    <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-lg text-blue-950">Task Overdue 🚨</h2>
            <a href="{{ route('reminders') }}" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 px-3 py-1.5 rounded-xl font-bold transition-colors">Lihat Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse($overdue->take(5) as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gradient-to-r from-rose-50/50 to-transparent border border-rose-100/50 rounded-2xl hover:bg-rose-50 transition-colors group gap-3">
                <div>
                    <div class="text-sm font-bold text-slate-800 group-hover:text-rose-600 transition-colors mb-1">{{ $task->name }}</div>
                    <div class="text-xs text-slate-500 font-medium">{{ $task->project->name ?? '-' }}</div>
                </div>
                <div class="sm:text-right shrink-0 bg-white px-3 py-2 rounded-xl border border-rose-100 shadow-sm">
                    <div class="text-xs text-rose-600 font-bold mb-0.5">{{ $task->due_date?->format('d M Y') }}</div>
                    <div class="text-[10px] text-rose-500 font-semibold uppercase tracking-wider">+{{ abs($task->days_until_due) }} hari lewat</div>
                </div>
            </a>
            @empty
            <div class="text-center py-12 bg-emerald-50/30 rounded-2xl border border-dashed border-emerald-100">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl mx-auto mb-3 shadow-sm border border-emerald-100">🎉</div>
                <p class="text-emerald-600 text-sm font-bold">Hebat! Tidak ada task overdue.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Reminder & Active Tasks --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
    {{-- Reminder Deadline --}}
    <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-lg text-blue-950">Mendekati Deadline 🔔</h2>
            <span class="text-[10px] uppercase tracking-widest text-amber-700 bg-amber-100/50 px-2.5 py-1 rounded-lg font-bold">7 Hari ke depan</span>
        </div>
        <div class="space-y-3">
            @forelse($upcomingDeadlines->take(5) as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gradient-to-r from-amber-50/50 to-transparent border border-amber-100/50 rounded-2xl hover:bg-amber-50 transition-colors group gap-3">
                <div>
                    <div class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors mb-1">{{ $task->name }}</div>
                    <div class="text-xs text-slate-500 font-medium">{{ $task->project->name ?? '-' }}</div>
                </div>
                <div class="sm:text-right shrink-0 bg-white px-3 py-2 rounded-xl border border-amber-100 shadow-sm">
                    @php $days = $task->days_until_due; @endphp
                    <div class="text-xs text-amber-700 font-bold mb-0.5">{{ $task->due_date?->format('d M') }}</div>
                    <div class="text-[10px] text-amber-600 font-semibold uppercase tracking-wider">
                        {{ $days == 0 ? 'HARI INI!' : "H-{$days}" }}
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-slate-400 text-sm font-medium">Santai, tidak ada deadline terdekat.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Semua Task Aktif --}}
    <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-lg text-blue-950">Task Aktif</h2>
            <a href="{{ route('tasks.index') }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl font-bold transition-colors">Lihat Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse($activeTasks->take(5) as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center gap-4 p-4 border border-slate-100 rounded-2xl hover:border-blue-200 hover:shadow-md hover:shadow-blue-900/5 transition-all group bg-white">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors truncate mb-2">{{ $task->name }}</div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                            <div class="h-full rounded-full bg-blue-500" style="width: {{ $task->progress }}%"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 w-6 text-right">{{ $task->progress }}%</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-50 border border-blue-200 flex items-center justify-center text-blue-700 text-xs font-bold shrink-0 shadow-sm" title="{{ $task->pics->first()->name ?? 'Unknown' }}">
                    {{ strtoupper(substr($task->pics->first()->name ?? '?', 0, 2)) }}
                </div>
            </a>
            @empty
            <div class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-slate-400 text-sm font-medium">Tidak ada task aktif saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 3D Pie Chart using Google Charts
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(draw3DPieChart);
    
    function draw3DPieChart() {
        var progress = {{ $stats['year_progress'] }};
        var remaining = 100 - progress;
        
        var data = google.visualization.arrayToDataTable([
            ['Status', 'Persentase'],
            ['Selesai', progress],
            ['Belum', remaining]
        ]);

        var options = {
            is3D: true,
            backgroundColor: 'transparent',
            colors: ['#3b82f6', '#e2e8f0'],
            legend: { position: 'bottom', textStyle: { color: '#64748b', fontSize: 11, bold: true } },
            chartArea: { width: '100%', height: '85%' },
            pieSliceText: 'percentage',
            pieSliceTextStyle: { fontSize: 12, bold: true },
            tooltip: { textStyle: { fontSize: 12 } }
        };

        var chart = new google.visualization.PieChart(document.getElementById('annualProgressPie3D'));
        chart.draw(data, options);
    }

    // 1. Pie Chart - Status Task
    var optionsPie = {
        series: [{{ $stats['done_tasks'] }}, {{ $stats['ongoing_tasks'] }}, {{ $stats['not_started_tasks'] }}, {{ $stats['overdue_count'] }}],
        chart: { type: 'donut', height: 260, background: 'transparent' },
        labels: ['Selesai', 'Berjalan', 'Belum Mulai', 'Overdue'],
        colors: ['#10b981', '#3b82f6', '#cbd5e1', '#f43f5e'],
        plotOptions: {
            pie: { 
                donut: { size: '75%' },
                expandOnClick: false
            }
        },
        dataLabels: { enabled: false },
        stroke: { width: 0 },
        legend: { position: 'bottom', fontSize: '12px', fontWeight: 600, markers: { radius: 12 } }
    };
    var chartPie = new ApexCharts(document.querySelector("#taskStatusChart"), optionsPie);
    chartPie.render();

    // 2. Bar Chart - Kesesuaian Jadwal
    var optionsBar = {
        series: [
            {
                name: 'Tepat Waktu',
                data: [
                    {{ $stats['timing_stats']['projects']['tepat'] }}, 
                    {{ $stats['timing_stats']['subprojects']['tepat'] }}, 
                    {{ $stats['timing_stats']['tasks']['tepat'] }}
                ]
            },
            {
                name: 'Lebih Cepat',
                data: [
                    {{ $stats['timing_stats']['projects']['maju'] }}, 
                    {{ $stats['timing_stats']['subprojects']['maju'] }}, 
                    {{ $stats['timing_stats']['tasks']['maju'] }}
                ]
            },
            {
                name: 'Terlambat (Molor)',
                data: [
                    {{ $stats['timing_stats']['projects']['telat'] }}, 
                    {{ $stats['timing_stats']['subprojects']['telat'] }}, 
                    {{ $stats['timing_stats']['tasks']['telat'] }}
                ]
            }
        ],
        chart: {
            type: 'bar',
            height: 280,
            width: '100%',
            toolbar: { show: false },
            background: 'transparent'
        },
        colors: ['#3b82f6', '#10b981', '#f43f5e'], // Biru (Tepat), Hijau (Maju), Merah (Telat)
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: ['Program', 'List', 'Task'],
            labels: { style: { colors: '#64748b', fontWeight: 600 } }
        },
        yaxis: {
            title: { text: 'Jumlah' },
            labels: { style: { colors: '#334155', fontWeight: 600, fontSize: '11px' } }
        },
        fill: {
            opacity: 1
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            padding: {
                right: 20,
                left: 10
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            fontSize: '12px',
            fontWeight: 600,
            markers: { radius: 12 }
        }
    };
    var chartBar = new ApexCharts(document.querySelector("#taskTimelineChart"), optionsBar);
    chartBar.render();
});
</script>
@endpush

