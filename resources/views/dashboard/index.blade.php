@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan seluruh aktivitas project')

@section('content')
{{-- Year Filter --}}
<form method="GET" class="flex items-center gap-3 mb-8">
    <label class="text-sm text-slate-400 font-medium">Tahun:</label>
    <select name="year" onchange="this.form.submit()"
            class="bg-[#1a1d27] border border-[#333650] text-white rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
        @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
</form>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6 hover:border-[#444870] transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-950 flex items-center justify-center text-2xl">📁</div>
            <span class="text-xs text-blue-400 bg-blue-950 px-2 py-1 rounded-lg font-semibold">{{ $year }}</span>
        </div>
        <div class="text-3xl font-bold text-white mb-1">{{ $stats['total_projects'] }}</div>
        <div class="text-sm text-slate-400">Total Project</div>
    </div>
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6 hover:border-[#444870] transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-950 flex items-center justify-center text-2xl">🚀</div>
            <span class="text-xs text-green-400 bg-green-950 px-2 py-1 rounded-lg font-semibold">Aktif</span>
        </div>
        <div class="text-3xl font-bold text-white mb-1">{{ $stats['active_projects'] }}</div>
        <div class="text-sm text-slate-400">Project Aktif</div>
    </div>
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6 hover:border-[#444870] transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-950 flex items-center justify-center text-2xl">✅</div>
            <span class="text-xs text-purple-400 bg-purple-950 px-2 py-1 rounded-lg font-semibold">{{ $stats['done_tasks'] }}/{{ $stats['total_tasks'] }}</span>
        </div>
        <div class="text-3xl font-bold text-white mb-1">{{ $stats['total_tasks'] }}</div>
        <div class="text-sm text-slate-400">Total Task</div>
    </div>
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6 hover:border-[#444870] transition-colors">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-red-950 flex items-center justify-center text-2xl">⚠️</div>
            <span class="text-xs text-red-400 bg-red-950 px-2 py-1 rounded-lg font-semibold">Overdue</span>
        </div>
        <div class="text-3xl font-bold text-white mb-1">{{ $stats['overdue_count'] }}</div>
        <div class="text-sm text-slate-400">Task Overdue</div>
    </div>
</div>

{{-- Progress Tahunan --}}
<div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="font-bold text-white">Progress Tahunan {{ $year }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Rata-rata progres seluruh project</p>
        </div>
        <span class="text-2xl font-bold text-blue-400">{{ $stats['year_progress'] }}%</span>
    </div>
    <div class="w-full bg-[#222535] rounded-full h-3">
        <div class="h-3 rounded-full transition-all duration-700"
             style="width: {{ $stats['year_progress'] }}%; background: linear-gradient(90deg, #4f80ff, #a78bfa)"></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Progress Per Project --}}
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-white">Progress Per Project</h2>
            <a href="{{ route('projects.index') }}" class="text-xs text-blue-400 hover:text-blue-300">Lihat Semua →</a>
        </div>
        <div class="space-y-5">
            @forelse($stats['projects'] as $project)
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <a href="{{ route('projects.show', $project->id) }}" class="text-sm font-semibold text-white hover:text-blue-400 transition-colors truncate max-w-[200px]">
                        {{ $project->name }}
                    </a>
                    <span class="text-sm font-bold text-blue-400 ml-2 shrink-0">{{ $project->progress }}%</span>
                </div>
                <div class="w-full bg-[#222535] rounded-full h-2">
                    @php
                        $p = $project->progress;
                        $color = $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b');
                    @endphp
                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $p }}%; background: {{ $color }}"></div>
                </div>
                <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                    <span>{{ $project->tasks->where('status', 'Selesai')->count() }}/{{ $project->tasks->count() }} task selesai</span>
                    <span>{{ $project->status }}</span>
                </div>
            </div>
            @empty
            <p class="text-slate-400 text-sm text-center py-4">Belum ada project di tahun {{ $year }}.</p>
            @endforelse
        </div>
    </div>

    {{-- Task Overdue --}}
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-white">Task Overdue 🚨</h2>
            <a href="{{ route('reminders') }}" class="text-xs text-red-400 hover:text-red-300">Lihat Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse($overdue->take(5) as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center justify-between p-3 bg-red-950/30 border border-red-900/30 rounded-xl hover:border-red-800/50 transition-colors group">
                <div>
                    <div class="text-sm font-semibold text-white group-hover:text-red-400 transition-colors">{{ $task->name }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">{{ $task->project->name ?? '-' }} • PIC: {{ $task->pic->name ?? '-' }}</div>
                </div>
                <div class="text-right shrink-0 ml-3">
                    <div class="text-xs text-red-400 font-semibold">{{ $task->due_date?->format('d M') }}</div>
                    <div class="text-[10px] text-red-400">+{{ abs($task->days_until_due) }} hari</div>
                </div>
            </a>
            @empty
            <div class="text-center py-6">
                <div class="text-3xl mb-2">🎉</div>
                <p class="text-slate-400 text-sm">Tidak ada task overdue!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Reminder & Active Tasks --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Reminder Deadline --}}
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-white">Reminder Deadline 🔔</h2>
            <span class="text-xs text-amber-400 bg-amber-950 px-2 py-1 rounded-lg">7 Hari ke depan</span>
        </div>
        <div class="space-y-3">
            @forelse($upcomingDeadlines->take(5) as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center justify-between p-3 bg-amber-950/20 border border-amber-900/30 rounded-xl hover:border-amber-700/50 transition-colors group">
                <div>
                    <div class="text-sm font-semibold text-white group-hover:text-amber-400 transition-colors">{{ $task->name }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">{{ $task->project->name ?? '-' }}</div>
                </div>
                <div class="text-right shrink-0 ml-3">
                    @php $days = $task->days_until_due; @endphp
                    <div class="text-xs text-amber-400 font-semibold">{{ $task->due_date?->format('d M') }}</div>
                    <div class="text-[10px] text-amber-400">
                        {{ $days == 0 ? 'Hari ini!' : "H-{$days}" }}
                    </div>
                </div>
            </a>
            @empty
            <p class="text-slate-400 text-sm text-center py-4">Tidak ada deadline mendekati.</p>
            @endforelse
        </div>
    </div>

    {{-- Semua Task Aktif --}}
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-white">Task Aktif</h2>
            <a href="{{ route('tasks.index') }}" class="text-xs text-blue-400 hover:text-blue-300">Lihat Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse($activeTasks as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center gap-3 p-3 bg-[#222535] rounded-xl hover:bg-[#2a2e42] transition-colors group">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white group-hover:text-blue-400 transition-colors truncate">{{ $task->name }}</div>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="flex-1 bg-[#1a1d27] rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ $task->progress }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 shrink-0">{{ $task->progress }}%</span>
                    </div>
                </div>
                <div class="w-7 h-7 rounded-lg bg-blue-950 flex items-center justify-center text-blue-400 text-xs font-bold shrink-0">
                    {{ strtoupper(substr($task->pic->name ?? '?', 0, 2)) }}
                </div>
            </a>
            @empty
            <p class="text-slate-400 text-sm text-center py-4">Tidak ada task aktif.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
