@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan aktivitas dan tugas Anda')

@section('content')
{{-- Stats Cards Personal --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <span class="text-[10px] uppercase tracking-widest text-blue-700 bg-blue-100/50 px-2.5 py-1 rounded-lg font-bold">ALL</span>
        </div>
        <div>
            <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $myStats['total'] }}</div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Task Saya</div>
        </div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span class="text-[10px] uppercase tracking-widest text-emerald-700 bg-emerald-100/50 px-2.5 py-1 rounded-lg font-bold">DONE</span>
        </div>
        <div>
            <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $myStats['done'] }}</div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Task Selesai</div>
        </div>
    </div>
    
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-6 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all hover:-translate-y-1 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center text-white shadow-lg shadow-rose-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span class="text-[10px] uppercase tracking-widest text-rose-700 bg-rose-100/50 px-2.5 py-1 rounded-lg font-bold">LATE</span>
        </div>
        <div>
            <div class="text-4xl font-black text-blue-950 mb-1 tracking-tight">{{ $myStats['overdue'] }}</div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Task Overdue</div>
        </div>
    </div>
</div>

{{-- Fokus Hari Ini --}}
<div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8 mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-bold text-lg text-blue-950">Fokus Hari Ini 🎯</h2>
            <p class="text-xs text-slate-500 mt-1">Task milik Anda yang tenggat waktunya hari ini.</p>
        </div>
        <span class="text-[10px] uppercase tracking-widest text-indigo-700 bg-indigo-100/50 px-2.5 py-1 rounded-lg font-bold">Today</span>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse($myTasksToday as $task)
        <a href="{{ route('tasks.show', $task->id) }}" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gradient-to-r from-indigo-50/50 to-transparent border border-indigo-100/50 rounded-2xl hover:bg-indigo-50 transition-colors group gap-3">
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors mb-1 truncate">{{ $task->name }}</div>
                <div class="text-xs text-slate-500 font-medium">{{ $task->project->name ?? '-' }}</div>
            </div>
            <div class="sm:text-right shrink-0 bg-white px-3 py-2 rounded-xl border border-indigo-100 shadow-sm flex flex-col justify-center">
                <div class="text-xs text-indigo-600 font-bold mb-0.5">Hari Ini</div>
                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">{{ $task->status }}</div>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl mx-auto mb-3 shadow-sm border border-slate-100">🏖️</div>
            <p class="text-slate-500 text-sm font-bold">Tidak ada task yang deadlinenya hari ini.</p>
            <p class="text-slate-400 text-xs mt-1">Bisa fokus mengerjakan tugas lain atau mereview project.</p>
        </div>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
    {{-- Reminder Deadline Personal --}}
    <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8 flex flex-col h-full">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-lg text-blue-950">Mendekati Deadline 🔔</h2>
            <span class="text-[10px] uppercase tracking-widest text-amber-700 bg-amber-100/50 px-2.5 py-1 rounded-lg font-bold">7 Hari ke depan</span>
        </div>
        <div class="space-y-3 flex-1">
            @forelse($upcomingDeadlines->take(5) as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gradient-to-r from-amber-50/50 to-transparent border border-amber-100/50 rounded-2xl hover:bg-amber-50 transition-colors group gap-3">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors mb-1 truncate">{{ $task->name }}</div>
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
                <p class="text-slate-400 text-sm font-medium">Santai, tidak ada deadline terdekat untuk Anda.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Task Aktif Personal --}}
    <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl p-6 sm:p-8 flex flex-col h-full">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-bold text-lg text-blue-950">Semua Task Aktif Saya</h2>
            <a href="{{ route('tasks.index') }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-xl font-bold transition-colors">Lihat Semua →</a>
        </div>
        <div class="space-y-3 flex-1">
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
            </a>
            @empty
            <div class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-slate-400 text-sm font-medium">Anda tidak memiliki task yang berstatus Berjalan saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
