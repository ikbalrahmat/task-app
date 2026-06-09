@extends('layouts.app')
@section('title', 'Gantt Chart')
@section('heading', 'Gantt Chart')
@section('subheading', 'Visualisasi timeline project dan task')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
<style>
    #gantt-container .gantt-container { background: #1a1d27 !important; }
    
    /* Planned Bar (Baseline) */
    .bar-planned .bar { fill: #333650 !important; stroke: #4f80ff !important; stroke-width: 1px; stroke-dasharray: 4; opacity: 0.8; }
    .bar-planned .bar-progress { fill: #4f80ff !important; opacity: 0.3; }
    
    /* Actual Bar (Realization) */
    .bar-actual .bar { fill: #4f80ff !important; }
    .bar-actual .bar-progress { fill: #2563eb !important; }
    
    .gantt .bar-label { fill: #fff !important; }
    .gantt .grid-header { fill: #222535 !important; }
    .gantt .grid-row { fill: #1a1d27 !important; }
    .gantt .grid-row:nth-child(even) { fill: #1e2130 !important; }
    .gantt .row-line, .gantt .tick { stroke: #333650 !important; }
    .gantt .upper-text, .gantt .lower-text { fill: #9ca3c8 !important; }
    .gantt-container { border-radius: 16px; overflow: hidden; }
</style>
@endpush

@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex items-center gap-3">
        <label class="text-sm text-slate-400">Tahun:</label>
        <select name="year" onchange="this.form.submit()"
                class="bg-[#1a1d27] border border-[#333650] text-white rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
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
    <div class="flex items-center gap-2 text-xs text-slate-400">
        <span class="w-3 h-3 rounded bg-blue-500 inline-block"></span> Task
    </div>
</div>

@if($projects->isEmpty())
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-16 text-center">
        <div class="text-4xl mb-3">📉</div>
        <div class="font-bold text-white mb-1">Belum ada project di tahun {{ $year }}</div>
        <p class="text-sm text-slate-400">Tambahkan project terlebih dahulu.</p>
    </div>
@else
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-4">
        <div id="gantt-container"></div>
    </div>

    {{-- Project summary --}}
    <div class="mt-6 space-y-3">
        @foreach($projects as $project)
        @if($project->tasks->count())
        <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <a href="{{ route('projects.show', $project->id) }}" class="font-bold text-white hover:text-blue-400 transition-colors">{{ $project->name }}</a>
                <span class="text-xs text-blue-400 font-bold">{{ $project->progress }}%</span>
            </div>
            <div class="w-full bg-[#222535] rounded-full h-1.5">
                <div class="h-1.5 rounded-full bg-blue-500" style="width:{{ $project->progress }}%"></div>
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
            // Planned Baseline
            $arr[] = [
                'id'           => "plan_" . $t->id,
                'name'         => "[Rencana] " . $t->name,
                'start'        => $t->start_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'end'          => $t->due_date?->format('Y-m-d') ?? now()->addDays(7)->format('Y-m-d'),
                'progress'     => $t->progress,
                'project'      => $p->name,
                'custom_class' => 'bar-planned',
                'real_id'      => $t->id,
                'type'         => 'Rencana'
            ];
            
            // Actual Realization (only if started)
            if ($t->actual_start_date) {
                $arr[] = [
                    'id'           => "actual_" . $t->id,
                    'name'         => "[Realisasi] " . $t->name,
                    'start'        => $t->actual_start_date->format('Y-m-d'),
                    'end'          => $t->actual_end_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'progress'     => $t->progress,
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
            return `<div style="background:#222535;border:1px solid #333650;border-radius:12px;padding:12px;color:#e8eaf6;font-size:12px;max-width:200px">
                <div style="font-weight:700;margin-bottom:4px">${task.name}</div>
                <div style="color:#9ca3c8">${task.project}</div>
                <div style="color:#4f80ff;margin-top:4px">Progress: ${task.progress}%</div>
                <div style="color:#9ca3c8">${task._start.format('DD MMM')} → ${task._end.format('DD MMM')}</div>
            </div>`;
        }
    });
}
</script>
@endpush
