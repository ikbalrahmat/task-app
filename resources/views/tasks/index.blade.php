@extends('layouts.app')
@section('title', 'Daftar Task')
@section('heading', 'Task')
@section('subheading', 'Kelola seluruh task program')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari task..."
               class="bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 min-w-[180px]">
        <select name="project_id" class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">Semua Program</option>
            @foreach($projects as $proj)
                <option value="{{ $proj->id }}" {{ ($filters['project_id'] ?? '') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
            @endforeach
        </select>
        <select name="pic_id" class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">Semua PIC</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ ($filters['pic_id'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
        <select name="status" class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Task::STATUSES as $s)
                <option value="{{ $s }}" {{ ($filters['status'] ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-slate-100 border border-slate-200 text-slate-800 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-200 transition-colors">Filter</button>
        @if(!empty(array_filter($filters)))
            <a href="{{ route('tasks.index') }}" class="text-slate-500 hover:text-slate-800 text-sm">Reset</a>
        @endif
    </form>
    @can('create', \App\Models\Task::class)
    <a href="{{ route('tasks.create') }}"
       class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 shrink-0">
        + Tambah Task
    </a>
    @endcan
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200 divide-x divide-slate-200">
                    <th class="px-5 py-4 text-center font-semibold">Nama Task</th>
                    <th class="px-5 py-4 text-center font-semibold">Program</th>
                    <th class="px-5 py-4 text-center font-semibold">PIC</th>
                    <th class="px-5 py-4 text-center font-semibold">Due Date</th>
                    <th class="px-5 py-4 text-center font-semibold">Progress</th>
                    <th class="px-5 py-4 text-center font-semibold">Status</th>
                    <th class="px-5 py-4 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tasks as $task)
                <tr class="hover:bg-slate-50 transition-colors divide-x divide-slate-100">
                    <td class="px-5 py-4">
                        <a href="{{ route('tasks.show', $task->id) }}" class="font-semibold text-slate-855 text-slate-800 hover:text-blue-600 transition-colors">
                            {{ $task->name }}
                        </a>
                    </td>
                    <td class="px-5 py-4">
                        <a href="{{ route('projects.show', $task->project_id) }}" class="text-slate-500 hover:text-blue-600 transition-colors text-xs">
                            {{ $task->project->name ?? '-' }}
                        </a>
                        @if($task->subproject)
                            <span class="text-slate-400 text-[10px] block font-medium mt-0.5">/ {{ $task->subproject->name }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($task->pics->count() === $users->count() && $users->count() > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                                Semua PIC (ALL)
                            </span>
                            <div class="flex justify-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                                    Semua PIC (ALL)
                                </span>
                            </div>
                        @else
                            <div class="flex items-center justify-center -space-x-1.5 overflow-hidden">
                                @foreach($task->pics->take(3) as $pic)
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-[10px] font-bold shrink-0 ring-2 ring-white" title="{{ $pic->name }}">
                                        {{ strtoupper(substr($pic->name, 0, 2)) }}
                                    </div>
                                @endforeach
                                @if($task->pics->count() > 3)
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 text-[10px] font-bold shrink-0 ring-2 ring-white" title="Dan {{ $task->pics->count() - 3 }} lainnya">
                                        +{{ $task->pics->count() - 3 }}
                                    </div>
                                @endif
                                @if($task->pics->isEmpty())
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center text-xs">
                        @if($task->due_date)
                            <span class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                {{ $task->due_date->format('d M Y') }}
                            </span>
                            @if($task->isOverdue())
                                <div class="text-red-600 text-[10px] font-semibold">Overdue</div>
                            @elseif($task->days_until_due <= 7 && $task->days_until_due >= 0)
                                <div class="text-amber-600 text-[10px] font-semibold">H-{{ $task->days_until_due }}</div>
                            @endif
                        @else
                            <span class="text-slate-500">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 min-w-[130px] text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="flex-1 max-w-[80px] bg-slate-100 rounded-full h-1.5">
                                @php $p = $task->progress; @endphp
                                <div class="h-1.5 rounded-full" style="width:{{ $p }}%; background: {{ $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                            </div>
                            <span class="text-xs text-slate-500 w-8 shrink-0">{{ $p }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @php
                            $ts = match($task->status) {
                                'Berjalan'    => 'bg-blue-50 text-blue-600 border-blue-100',
                                'Selesai'     => 'bg-green-50 text-green-600 border-green-100',
                                'Belum Mulai' => 'bg-slate-50 text-slate-500 border-slate-200',
                                'Overdue'     => 'bg-red-50 text-red-600 border-red-100',
                                default       => 'bg-slate-50 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold border {{ $ts }}">{{ $task->status }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('tasks.show', $task->id) }}"
                               class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-100 rounded-xl transition-all shadow-sm" title="Detail">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @can('update', $task)
                            <a href="{{ route('tasks.edit', $task->id) }}"
                               class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-100 rounded-xl transition-all shadow-sm" title="Edit">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>   
                            </a>
                            @endcan
                            @can('delete', $task)
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}"
                                  onsubmit="return confirm('Hapus task ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 rounded-xl transition-all shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                        <div class="text-4xl mb-3">✅</div>
                        <div class="font-semibold text-slate-800 mb-1">Belum ada task</div>
                        <p class="text-sm">Tambahkan task pertama Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tasks->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">{{ $tasks->links() }}</div>
    @endif
</div>
@endsection
