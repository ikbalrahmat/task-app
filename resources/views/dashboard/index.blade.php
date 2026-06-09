@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan seluruh aktivitas project')

@section('content')
{{-- Year Filter --}}
<form method="GET" class="flex items-center gap-3 mb-8">
    <label class="text-sm text-slate-500 font-medium">Tahun:</label>
    <select name="year" onchange="this.form.submit()"
            class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
</form>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-2xl">📁</div>
            <span class="text-xs text-blue-600 bg-blue-50 border border-blue-100 px-2 py-1 rounded-lg font-semibold">{{ $year }}</span>
        </div>
        <div class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['total_projects'] }}</div>
        <div class="text-sm text-slate-500">Total Project</div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 border border-green-100 flex items-center justify-center text-2xl">🚀</div>
            <span class="text-xs text-green-600 bg-green-50 border border-green-100 px-2 py-1 rounded-lg font-semibold">Aktif</span>
        </div>
        <div class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['active_projects'] }}</div>
        <div class="text-sm text-slate-500">Project Aktif</div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-2xl">✅</div>
            <span class="text-xs text-purple-600 bg-purple-50 border border-purple-100 px-2 py-1 rounded-lg font-semibold">{{ $stats['done_tasks'] }}/{{ $stats['total_tasks'] }}</span>
        </div>
        <div class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['total_tasks'] }}</div>
        <div class="text-sm text-slate-500">Total Task</div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-2xl">⚠️</div>
            <span class="text-xs text-red-600 bg-red-50 border border-red-100 px-2 py-1 rounded-lg font-semibold">Overdue</span>
        </div>
        <div class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['overdue_count'] }}</div>
        <div class="text-sm text-slate-500">Task Overdue</div>
    </div>
</div>

{{-- Progress Tahunan --}}
<div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-bold text-slate-800">Progress Tahunan {{ $year }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rata-rata progres seluruh project</p>
        </div>
        <span class="text-2xl font-bold text-blue-600">{{ $stats['year_progress'] }}%</span>
    </div>
    <div class="w-full bg-slate-100 rounded-full h-3">
        <div class="h-3 rounded-full transition-all duration-700"
             style="width: {{ $stats['year_progress'] }}%; background: linear-gradient(90deg, #4f80ff, #a78bfa)"></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Progress Per Project --}}
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-800">Progress Per Project</h2>
            <a href="{{ route('projects.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Lihat Semua →</a>
        </div>
        <div class="space-y-5">
            @forelse($stats['projects'] as $project)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <a href="{{ route('projects.show', $project->id) }}" class="text-sm font-semibold text-slate-800 hover:text-blue-600 transition-colors truncate max-w-[200px]">
                        {{ $project->name }}
                    </a>
                    <span class="text-sm font-bold text-blue-600 ml-2 shrink-0">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    @php
                        $p = $project->progress;
                        $color = $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b');
                    @endphp
                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $p }}%; background: {{ $color }}"></div>
                </div>
                <div class="flex justify-between text-[10px] text-slate-500 mt-1">
                    <span>{{ $project->tasks->where('status', 'Selesai')->count() }}/{{ $project->tasks->count() }} task selesai</span>
                    <span>{{ $project->status }}</span>
                </div>
                @if($project->subprojects->isNotEmpty())
                <div class="mt-3 pl-4 border-l-2 border-slate-100 space-y-2">
                    @foreach($project->subprojects as $sub)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <a href="{{ route('subprojects.show', $sub->id) }}" class="text-[11px] font-medium text-slate-600 hover:text-blue-600 transition-colors truncate max-w-[180px]" title="{{ $sub->name }}">
                                📂 {{ $sub->name }}
                            </a>
                            <span class="text-[10px] font-bold text-slate-500">{{ $sub->progress }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1">
                            <div class="h-1 rounded-full bg-blue-500" style="width: {{ $sub->progress }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <p class="text-slate-500 text-sm text-center py-4">Belum ada project di tahun {{ $year }}.</p>
            @endforelse
        </div>
    </div>

    {{-- Task Overdue --}}
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-800">Task Overdue 🚨</h2>
            <a href="{{ route('reminders') }}" class="text-xs text-red-600 hover:text-red-700 font-semibold">Lihat Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse($overdue->take(5) as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center justify-between p-3 bg-red-50 border border-red-100 rounded-xl hover:border-red-200 transition-colors group">
                <div>
                    <div class="text-sm font-semibold text-slate-800 group-hover:text-red-600 transition-colors">{{ $task->name }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $task->project->name ?? '-' }} • PIC: {{ $task->pics->isNotEmpty() ? $task->pics->pluck('name')->join(', ') : '-' }}</div>
                </div>
                <div class="text-right shrink-0 ml-3">
                    <div class="text-xs text-red-600 font-semibold">{{ $task->due_date?->format('d M') }}</div>
                    <div class="text-[10px] text-red-600 font-medium">+{{ abs($task->days_until_due) }} hari</div>
                </div>
            </a>
            @empty
            <div class="text-center py-6">
                <div class="text-3xl mb-2">🎉</div>
                <p class="text-slate-500 text-sm">Tidak ada task overdue!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Reminder & Active Tasks --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Reminder Deadline --}}
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-800">Reminder Deadline 🔔</h2>
            <span class="text-xs text-amber-700 bg-amber-50 border border-amber-100 px-2 py-1 rounded-lg font-semibold">7 Hari ke depan</span>
        </div>
        <div class="space-y-3">
            @forelse($upcomingDeadlines->take(5) as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center justify-between p-3 bg-amber-50 border border-amber-100 rounded-xl hover:border-amber-200 transition-colors group">
                <div>
                    <div class="text-sm font-semibold text-slate-800 group-hover:text-amber-700 transition-colors">{{ $task->name }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $task->project->name ?? '-' }}</div>
                </div>
                <div class="text-right shrink-0 ml-3">
                    @php $days = $task->days_until_due; @endphp
                    <div class="text-xs text-amber-700 font-semibold">{{ $task->due_date?->format('d M') }}</div>
                    <div class="text-[10px] text-amber-700 font-medium">
                        {{ $days == 0 ? 'Hari ini!' : "H-{$days}" }}
                    </div>
                </div>
            </a>
            @empty
            <p class="text-slate-500 text-sm text-center py-4">Tidak ada deadline mendekati.</p>
            @endforelse
        </div>
    </div>

    {{-- Semua Task Aktif --}}
    <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-slate-800">Task Aktif</h2>
            <a href="{{ route('tasks.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">Lihat Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse($activeTasks as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100 transition-colors group">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-800 group-hover:text-blue-600 transition-colors truncate">{{ $task->name }}</div>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="flex-1 bg-slate-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ $task->progress }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-500 shrink-0">{{ $task->progress }}%</span>
                    </div>
                </div>
                <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold shrink-0">
                    {{ strtoupper(substr($task->pics->first()->name ?? '?', 0, 2)) }}
                </div>
            </a>
            @empty
            <p class="text-slate-500 text-sm text-center py-4">Tidak ada task aktif.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
