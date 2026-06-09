@extends('layouts.app')
@section('title', 'Reminder Deadline')
@section('heading', 'Reminder Deadline')
@section('subheading', 'Pantau task overdue dan mendekati deadline')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Overdue --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="flex items-center gap-3 px-6 py-5 bg-red-50/60 border-b border-slate-100">
            <span class="text-xl">🚨</span>
            <div>
                <h2 class="font-bold text-red-800">Task Overdue</h2>
                <p class="text-xs text-slate-500">{{ $overdue->count() }} task melewati deadline</p>
            </div>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($overdue as $task)
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center justify-between px-6 py-4 hover:bg-red-50/20 transition-colors group">
                <div class="min-w-0">
                    <div class="font-semibold text-slate-800 group-hover:text-red-600 transition-colors text-sm truncate">{{ $task->name }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $task->project->name ?? '-' }}</div>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="w-5 h-5 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-[9px] font-bold">
                            {{ strtoupper(substr($task->pics->first()->name ?? '?', 0, 2)) }}
                        </div>
                        <span class="text-xs text-slate-500">{{ $task->pics->isNotEmpty() ? $task->pics->pluck('name')->join(', ') : 'Tanpa PIC' }}</span>
                    </div>
                </div>
                <div class="text-right shrink-0 ml-4">
                    <div class="text-sm font-bold text-red-600">{{ $task->due_date?->format('d M Y') ?? '-' }}</div>
                    <div class="text-xs text-red-600 font-semibold">+{{ abs($task->days_until_due) }} hari</div>
                    <div class="mt-1.5 flex items-center gap-1 justify-end">
                        <div class="w-16 bg-slate-100 rounded-full h-1">
                            <div class="h-1 rounded-full bg-red-500" style="width:{{ $task->progress }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-500">{{ $task->progress }}%</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="px-6 py-16 text-center">
                <div class="text-4xl mb-3">🎉</div>
                <div class="font-semibold text-slate-800 mb-1">Tidak ada task overdue!</div>
                <p class="text-sm text-slate-500">Semua task masih on-track.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Upcoming --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="flex items-center gap-3 px-6 py-5 bg-amber-50/60 border-b border-slate-100">
            <span class="text-xl">⏰</span>
            <div>
                <h2 class="font-bold text-amber-800">Mendekati Deadline</h2>
                <p class="text-xs text-slate-500">{{ $upcoming->count() }} task dalam 7 hari ke depan</p>
            </div>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($upcoming as $task)
            @php $days = $task->days_until_due; @endphp
            <a href="{{ route('tasks.show', $task->id) }}"
               class="flex items-center justify-between px-6 py-4 hover:bg-amber-50/20 transition-colors group">
                <div class="min-w-0">
                    <div class="font-semibold text-slate-800 group-hover:text-amber-600 transition-colors text-sm truncate">{{ $task->name }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $task->project->name ?? '-' }}</div>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="w-5 h-5 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-[9px] font-bold">
                            {{ strtoupper(substr($task->pics->first()->name ?? '?', 0, 2)) }}
                        </div>
                        <span class="text-xs text-slate-500">{{ $task->pics->isNotEmpty() ? $task->pics->pluck('name')->join(', ') : 'Tanpa PIC' }}</span>
                    </div>
                </div>
                <div class="text-right shrink-0 ml-4">
                    <div class="text-sm font-bold text-amber-600">{{ $task->due_date?->format('d M Y') }}</div>
                    <div class="text-xs {{ $days == 0 ? 'text-red-600 font-bold' : 'text-amber-600 font-semibold' }}">
                        {{ $days == 0 ? 'Hari ini!' : "H-{$days}" }}
                    </div>
                    <div class="mt-1.5 flex items-center gap-1 justify-end">
                        <div class="w-16 bg-slate-100 rounded-full h-1">
                            <div class="h-1 rounded-full bg-amber-500" style="width:{{ $task->progress }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-500">{{ $task->progress }}%</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="px-6 py-16 text-center">
                <div class="text-4xl mb-3">✅</div>
                <div class="font-semibold text-slate-800 mb-1">Tidak ada deadline mendekat</div>
                <p class="text-sm text-slate-500">Semua aman dalam 7 hari ke depan.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
