@extends('layouts.app')
@section('title', 'Daftar Task')
@section('heading', 'Task')
@section('subheading', 'Kelola seluruh task project')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari task..."
               class="bg-[#1a1d27] border border-[#333650] text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 min-w-[180px]">
        <select name="project_id" class="bg-[#1a1d27] border border-[#333650] text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua Project</option>
            @foreach($projects as $proj)
                <option value="{{ $proj->id }}" {{ ($filters['project_id'] ?? '') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
            @endforeach
        </select>
        <select name="pic_id" class="bg-[#1a1d27] border border-[#333650] text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua PIC</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ ($filters['pic_id'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
        <select name="status" class="bg-[#1a1d27] border border-[#333650] text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Task::STATUSES as $s)
                <option value="{{ $s }}" {{ ($filters['status'] ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-[#222535] border border-[#333650] text-white px-4 py-2.5 rounded-xl text-sm hover:bg-[#2a2e42] transition-colors">Filter</button>
        @if(!empty(array_filter($filters)))
            <a href="{{ route('tasks.index') }}" class="text-slate-400 hover:text-white text-sm">Reset</a>
        @endif
    </form>
    @can('create', \App\Models\Task::class)
    <a href="{{ route('tasks.create') }}"
       class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 shrink-0">
        + Tambah Task
    </a>
    @endcan
</div>

<div class="bg-[#1a1d27] border border-[#333650] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#222535] text-slate-400 text-xs uppercase tracking-wider">
                    <th class="px-5 py-4 text-left font-semibold">Nama Task</th>
                    <th class="px-5 py-4 text-left font-semibold">Project</th>
                    <th class="px-5 py-4 text-left font-semibold">PIC</th>
                    <th class="px-5 py-4 text-left font-semibold">Due Date</th>
                    <th class="px-5 py-4 text-left font-semibold">Progress</th>
                    <th class="px-5 py-4 text-left font-semibold">Status</th>
                    <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#333650]">
                @forelse($tasks as $task)
                <tr class="hover:bg-[#222535] transition-colors">
                    <td class="px-5 py-4">
                        <a href="{{ route('tasks.show', $task->id) }}" class="font-semibold text-white hover:text-blue-400 transition-colors">
                            {{ $task->name }}
                        </a>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('projects.show', $task->project_id) }}" class="text-slate-300 hover:text-blue-400 transition-colors text-xs">
                            {{ $task->project->name ?? '-' }}
                        </a>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-blue-950 flex items-center justify-center text-blue-400 text-xs font-bold shrink-0">
                                {{ strtoupper(substr($task->pic->name ?? '?', 0, 2)) }}
                            </div>
                            <span class="text-slate-300 text-xs">{{ $task->pic->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-xs">
                        @if($task->due_date)
                            <span class="{{ $task->isOverdue() ? 'text-red-400 font-semibold' : 'text-slate-300' }}">
                                {{ $task->due_date->format('d M Y') }}
                            </span>
                            @if($task->isOverdue())
                                <div class="text-red-400 text-[10px]">Overdue</div>
                            @elseif($task->days_until_due <= 7 && $task->days_until_due >= 0)
                                <div class="text-amber-500 text-[10px]">H-{{ $task->days_until_due }}</div>
                            @endif
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 min-w-[130px]">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-[#222535] rounded-full h-1.5">
                                @php $p = $task->progress; @endphp
                                <div class="h-1.5 rounded-full" style="width:{{ $p }}%; background: {{ $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                            </div>
                            <span class="text-xs text-slate-400 w-8 shrink-0">{{ $p }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $ts = match($task->status) {
                                'Berjalan'    => 'bg-blue-950 text-blue-400 border-blue-900/50',
                                'Selesai'     => 'bg-green-950 text-green-400 border-green-900/50',
                                'Belum Mulai' => 'bg-[#222535] text-slate-400 border-[#333650]',
                                'Overdue'     => 'bg-red-950 text-red-400 border-red-900/50',
                                default       => 'bg-[#222535] text-slate-400',
                            };
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold border {{ $ts }}">{{ $task->status }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('tasks.show', $task->id) }}" class="p-2 text-slate-400 hover:text-blue-400 hover:bg-blue-950/30 rounded-lg transition-colors" title="Detail">👁</a>
                            @can('update', $task)
                            <a href="{{ route('tasks.edit', $task->id) }}" class="p-2 text-slate-400 hover:text-amber-400 hover:bg-amber-950/30 rounded-lg transition-colors" title="Edit">✏️</a>
                            @endcan
                            @can('delete', $task)
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}"
                                  onsubmit="return confirm('Hapus task ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-950/30 rounded-lg transition-colors" title="Hapus">🗑</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-slate-400">
                        <div class="text-4xl mb-3">✅</div>
                        <div class="font-semibold text-white mb-1">Belum ada task</div>
                        <p class="text-sm">Tambahkan task pertama Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tasks->hasPages())
    <div class="px-6 py-4 border-t border-[#333650]">{{ $tasks->links() }}</div>
    @endif
</div>
@endsection
