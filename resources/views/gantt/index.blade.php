@extends('layouts.app')
@section('title', 'Gantt Chart')
@section('heading', 'Gantt Chart')
@section('subheading', 'Visualisasi timeline project dan task')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
<style>
    #gantt-container .gantt-container { background: #ffffff !important; }
    
    /* Planned Bar (Baseline) */
    .bar-planned .bar { fill: #f8fafc !important; stroke: #94a3b8 !important; stroke-width: 1.5px; stroke-dasharray: 4; opacity: 0.9; }
    .bar-planned .bar-progress { fill: #cbd5e1 !important; opacity: 0.6; }
    .bar-planned .bar-label { fill: #475569 !important; font-weight: 600 !important; font-size: 11px !important; }
    
    /* Actual Bar (Realization) */
    .bar-actual .bar { fill: #3b82f6 !important; }
    .bar-actual .bar-progress { fill: #1d4ed8 !important; }
    .bar-actual .bar-label { fill: #ffffff !important; font-weight: 600 !important; font-size: 11px !important; }
    
    .gantt .grid-header { fill: #f8fafc !important; }
    .gantt .grid-row { fill: #ffffff !important; }
    .gantt .grid-row:nth-child(even) { fill: #f8fafc !important; }
    .gantt .row-line, .gantt .tick { stroke: #e2e8f0 !important; }
    .gantt .upper-text, .gantt .lower-text { fill: #475569 !important; font-weight: 600 !important; }
    .gantt-container { border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; }
</style>
@endpush

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex items-center gap-3">
        <label class="text-sm text-slate-500 font-semibold">Tahun:</label>
        <select name="year" onchange="this.form.submit()"
                class="bg-white border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-medium">
            @php
                $availableYears = \App\Models\Project::select('year')->distinct()->pluck('year')->toArray();
                if (!in_array(date('Y'), $availableYears)) $availableYears[] = date('Y');
                if (!empty($year) && !in_array($year, $availableYears)) $availableYears[] = $year;
                rsort($availableYears);
            @endphp
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
    <div class="flex items-center gap-4 text-xs text-slate-500 font-medium">
        <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded border border-dashed border-slate-400 bg-slate-100 inline-block"></span> Rencana
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded bg-blue-600 inline-block"></span> Realisasi
        </div>
    </div>
</div>

@if($projects->isEmpty())
    <div class="bg-white border border-slate-200 rounded-2xl p-16 text-center shadow-sm">
        <div class="text-4xl mb-3">📉</div>
        <div class="font-bold text-slate-800 mb-1">Belum ada project di tahun {{ $year }}</div>
        <p class="text-sm text-slate-500">Tambahkan project terlebih dahulu.</p>
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
        <div id="gantt-container"></div>
    </div>

    {{-- Project summary --}}
    <div class="mt-6 space-y-3">
        @foreach($projects as $project)
        @if($project->tasks->count())
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow transition-all">
            <div class="flex items-center justify-between mb-3">
                <a href="{{ route('projects.show', $project->id) }}" class="font-bold text-slate-800 hover:text-blue-600 transition-colors">{{ $project->name }}</a>
                <span class="text-xs text-blue-600 font-bold">{{ $project->progress }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full bg-blue-600" style="width:{{ $project->progress }}%"></div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
@php
    $ganttTasks = $projects->flatMap(function($p) {
        $arr = [];
        foreach($p->tasks as $t) {
            $planStart = $t->start_date ? \Carbon\Carbon::parse($t->start_date) : now();
            $planEnd = $t->due_date ? \Carbon\Carbon::parse($t->due_date) : $planStart->copy()->addDays(7);
            
            // Frappe Gantt crashes if end <= start
            if ($planEnd->startOfDay()->lte($planStart->startOfDay())) {
                $planEnd = $planStart->copy()->addDay();
            }

            // Planned Baseline
            $arr[] = [
                'id'           => "plan_" . $t->id,
                'name'         => "[Rencana] " . $t->name,
                'start'        => $planStart->format('Y-m-d 00:00:00'),
                'end'          => $planEnd->format('Y-m-d 23:59:59'),
                'progress'     => $t->progress ?? 0,
                'dependencies' => '',
                'project'      => $p->name,
                'custom_class' => 'bar-planned',
                'real_id'      => $t->id,
                'type'         => 'Rencana'
            ];
            
            // Actual Realization (only if started)
            if ($t->actual_start_date) {
                $actualStart = \Carbon\Carbon::parse($t->actual_start_date);
                $actualEnd = $t->actual_end_date ? \Carbon\Carbon::parse($t->actual_end_date) : now();
                
                if ($actualEnd->startOfDay()->lte($actualStart->startOfDay())) {
                    $actualEnd = $actualStart->copy()->addDay();
                }

                $arr[] = [
                    'id'           => "actual_" . $t->id,
                    'name'         => "[Realisasi] " . $t->name,
                    'start'        => $actualStart->format('Y-m-d 00:00:00'),
                    'end'          => $actualEnd->format('Y-m-d 23:59:59'),
                    'progress'     => $t->progress ?? 0,
                    'dependencies' => '',
                    'project'      => $p->name,
                    'custom_class' => 'bar-actual',
                    'real_id'      => $t->id,
                    'type'         => 'Realisasi'
                ];
            }
        }
        return $arr;
    })->values();
@endphp
<script>
const tasks = @json($ganttTasks);

if (tasks.length > 0) {
    const gantt = new Gantt("#gantt-container", tasks, {
        view_mode: 'Week',
        date_format: 'YYYY-MM-DD',
        language: 'id',
        on_click: function (task) {
            window.location.href = `/tasks/${task.real_id}`;
        },
        custom_popup_html: function (task) {
            return `<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:12px;color:#1e293b;font-size:12px;max-width:200px;box-shadow:0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)">
                <div style="font-weight:700;margin-bottom:4px;color:#0f172a">${task.name}</div>
                <div style="color:#64748b">${task.project}</div>
                <div style="color:#2563eb;font-weight:600;margin-top:4px">Progress: ${task.progress}%</div>
                <div style="color:#64748b;font-size:11px;margin-top:2px">${task._start.format('DD MMM')} → ${task._end.format('DD MMM')}</div>
            </div>`;
        }
    });
}
</script>
@endpush
