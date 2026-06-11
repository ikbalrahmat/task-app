@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan seluruh aktivitas project')

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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-2xl shadow-lg shadow-blue-500/30">📁</div>
            <span class="text-[10px] uppercase tracking-widest text-blue-700 bg-blue-100/50 px-2.5 py-1 rounded-lg font-bold">{{ $year }}</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $stats['total_projects'] }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Project</div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-2xl shadow-lg shadow-emerald-500/30">🚀</div>
            <span class="text-[10px] uppercase tracking-widest text-emerald-700 bg-emerald-100/50 px-2.5 py-1 rounded-lg font-bold">Aktif</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $stats['active_projects'] }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Project Aktif</div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white text-2xl shadow-lg shadow-purple-500/30">✅</div>
            <span class="text-[10px] uppercase tracking-widest text-purple-700 bg-purple-100/50 px-2.5 py-1 rounded-lg font-bold">{{ $stats['done_tasks'] }}/{{ $stats['total_tasks'] }}</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $stats['total_tasks'] }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Task</div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center text-white text-2xl shadow-lg shadow-rose-500/30">⚠️</div>
            <span class="text-[10px] uppercase tracking-widest text-rose-700 bg-rose-100/50 px-2.5 py-1 rounded-lg font-bold">Overdue</span>
        </div>
        <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $stats['overdue_count'] }}</div>
        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Task Overdue</div>
    </div>
</div>

{{-- Progress Tahunan --}}
<div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-8 mb-8 relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 relative z-10">
        <div>
            <h2 class="font-bold text-xl text-blue-950">Progress Tahunan {{ $year }}</h2>
            <p class="text-sm text-slate-500 mt-1">Rata-rata progres seluruh project</p>
        </div>
        <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 mt-2 sm:mt-0">{{ $stats['year_progress'] }}%</span>
    </div>
    <div class="w-full bg-slate-100/80 rounded-full h-4 p-0.5 border border-white relative z-10 shadow-inner">
        <div class="h-full rounded-full transition-all duration-1000 ease-out shadow-sm"
             style="width: {{ $stats['year_progress'] }}%; background: linear-gradient(90deg, #3b82f6, #6366f1)"></div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
    {{-- Progress Per Project --}}
    <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-lg text-blue-950">Progress Per Project</h2>
            <a href="{{ route('projects.index') }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl font-bold transition-colors">Lihat Semua →</a>
        </div>
        <div class="space-y-6">
            @forelse($stats['projects'] as $project)
            <div class="group">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('projects.show', $project->id) }}" class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition-colors truncate pr-4">
                        {{ $project->name }}
                    </a>
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
                <div class="mt-4 pl-4 border-l-2 border-slate-100 space-y-3">
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
                <p class="text-slate-400 text-sm font-medium">Belum ada project di tahun {{ $year }}.</p>
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
