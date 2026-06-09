@extends('layouts.app')
@section('title', 'Laporan Progress')
@section('heading', 'Laporan')
@section('subheading', 'Progress report per project dan tahunan')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
{{-- Tabs --}}
<div class="flex items-center gap-2 mb-6 bg-white border border-slate-200 rounded-2xl p-1.5 w-fit shadow-sm">
    <a href="{{ route('reports', ['tab' => 'progress', 'year' => $year]) }}"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-all {{ $tab === 'progress' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'text-slate-500 hover:text-slate-900' }}">
        Progress Per Project
    </a>
    <a href="{{ route('reports', ['tab' => 'annual', 'year' => $year]) }}"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-all {{ $tab === 'annual' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/10' : 'text-slate-500 hover:text-slate-900' }}">
        Progress Tahunan
    </a>
</div>

@if($tab === 'progress')
    {{-- Year Filter --}}
    <form method="GET" class="flex items-center gap-3 mb-6">
        <input type="hidden" name="tab" value="progress">
        <label class="text-sm text-slate-500 font-medium">Tahun:</label>
        <select name="year" onchange="this.form.submit()"
                class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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

    {{-- Chart --}}
    @if($projects->isNotEmpty())
    <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm">
        <h2 class="font-bold text-slate-855 text-slate-800 mb-4">Grafik Progress Project {{ $year }}</h2>
        <div class="h-64">
            <canvas id="progressChart"></canvas>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h2 class="font-bold text-slate-800">Detail Per Project — {{ $year }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">Project</th>
                        <th class="px-6 py-4 text-center font-semibold">Total Task</th>
                        <th class="px-6 py-4 text-center font-semibold">Selesai</th>
                        <th class="px-6 py-4 text-center font-semibold">Berjalan</th>
                        <th class="px-6 py-4 text-center font-semibold">Overdue</th>
                        <th class="px-6 py-4 text-left font-semibold min-w-[200px]">Progress</th>
                        <th class="px-6 py-4 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projects as $project)
                    @php
                        $done    = $project->tasks->where('status', 'Selesai')->count();
                        $running = $project->tasks->where('status', 'Berjalan')->count();
                        $prog    = $project->progress;
                        $sc = match($project->status) {
                            'Berjalan'   => 'bg-blue-50 text-blue-600 border-blue-100',
                            'Selesai'    => 'bg-green-50 text-green-600 border-green-100',
                            'Perencanaan'=> 'bg-slate-50 text-slate-500 border-slate-200',
                            'Ditunda'    => 'bg-red-50 text-red-600 border-red-100',
                            default => 'bg-slate-50 text-slate-500 border-slate-200',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('projects.show', $project->id) }}" class="font-semibold text-slate-800 hover:text-blue-600 transition-colors">{{ $project->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-center text-slate-600">{{ $project->tasks->count() }}</td>
                        <td class="px-6 py-4 text-center"><span class="text-green-600 font-bold">{{ $done }}</span></td>
                        <td class="px-6 py-4 text-center"><span class="text-blue-600 font-bold">{{ $running }}</span></td>
                        <td class="px-6 py-4 text-center"><span class="{{ $project->overdue_tasks_count > 0 ? 'text-red-600 font-bold' : 'text-slate-500' }}">{{ $project->overdue_tasks_count }}</span></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-slate-100 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width:{{ $prog }}%; background: {{ $prog >= 75 ? '#22c55e' : ($prog >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                                </div>
                                <span class="text-sm font-bold text-slate-800 w-10 shrink-0">{{ $prog }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $sc }}">{{ $project->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-16 text-center text-slate-500 font-medium">Tidak ada project di tahun {{ $year }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@else
    {{-- Annual Report --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm">
        <h2 class="font-bold text-slate-800 mb-4">Grafik Progress Tahunan</h2>
        <div class="h-64">
            <canvas id="annualChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($annualStats as $y => $stats)
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 text-lg mb-4">{{ $y }}</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Total Project</span><span class="font-bold text-slate-800">{{ $stats['total_projects'] }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Total Task</span><span class="font-bold text-slate-800">{{ $stats['total_tasks'] }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Selesai</span><span class="font-bold text-green-600">{{ $stats['done_tasks'] }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Overdue</span><span class="font-bold text-red-600">{{ $stats['overdue_count'] }}</span></div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="flex justify-between mb-2 text-sm"><span class="text-slate-500">Avg Progress</span><span class="font-bold text-blue-600">{{ $stats['year_progress'] }}%</span></div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full" style="width:{{ $stats['year_progress'] }}%; background: linear-gradient(90deg,#4f80ff,#a78bfa)"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
@php
    $progressLabels = $projects->pluck('name');
    $progressData = $projects->map(fn($p) => $p->progress);
    $progressBgColors = $projects->map(fn($p) => $p->progress >= 75 ? 'rgba(34,197,94,0.6)' : ($p->progress >= 40 ? 'rgba(79,128,255,0.6)' : 'rgba(245,158,11,0.6)'));
    $progressBorderColors = $projects->map(fn($p) => $p->progress >= 75 ? '#22c55e' : ($p->progress >= 40 ? '#4f80ff' : '#f59e0b'));

    $annualLabels = [];
    $annualData = [];
    if (!empty($annualStats)) {
        $annualLabels = array_keys($annualStats);
        $annualData = array_values(array_map(fn($s) => $s['year_progress'], $annualStats));
    }
@endphp
<script>
@if($tab === 'progress' && $projects->isNotEmpty())
const ctx = document.getElementById('progressChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($progressLabels),
            datasets: [{
                label: 'Progress (%)',
                data: @json($progressData),
                backgroundColor: @json($progressBgColors),
                borderColor: @json($progressBorderColors),
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { color: '#64748b', callback: v => v + '%' }, grid: { color: '#f1f5f9' } },
                x: { ticks: { color: '#64748b' }, grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
}
@endif
@if($tab === 'annual' && !empty($annualStats))
const ctx2 = document.getElementById('annualChart');
if (ctx2) {
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: @json($annualLabels),
            datasets: [{
                label: 'Avg Progress (%)',
                data: @json($annualData),
                borderColor: '#4f80ff',
                backgroundColor: 'rgba(79,128,255,0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4f80ff',
                pointRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { color: '#64748b', callback: v => v + '%' }, grid: { color: '#f1f5f9' } },
                x: { ticks: { color: '#64748b' }, grid: { display: false } }
            },
            plugins: { legend: { labels: { color: '#64748b' } } }
        }
    });
}
@endif
</script>
@endpush
