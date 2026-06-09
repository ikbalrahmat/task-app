@extends('layouts.app')
@section('title', $project->name)
@section('heading', $project->name)
@section('subheading', 'Detail project & daftar task')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <a href="{{ route('projects.index') }}" class="text-slate-500 hover:text-slate-950 text-sm font-semibold transition-colors">← Kembali</a>
    @can('update', $project)
    <a href="{{ route('projects.edit', $project->id) }}" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">✏️ Edit</a>
    @endcan
    @can('delete', $project)
    <form method="POST" action="{{ route('projects.destroy', $project->id) }}"
          onsubmit="return confirm('Hapus project ini? Semua task terkait akan ikut dihapus.')">
        @csrf @method('DELETE')
        <button type="submit" class="bg-red-700 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">🗑 Hapus</button>
    </form>
    @endcan
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Info Card --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 mb-4">Informasi Project</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center"><span class="text-slate-500">Status</span>
                    @php
                        $sc = match($project->status) {
                            'Berjalan'   => 'bg-blue-50 text-blue-600 border-blue-100',
                            'Selesai'    => 'bg-green-50 text-green-600 border-green-100',
                            'Perencanaan'=> 'bg-slate-50 text-slate-500 border-slate-200',
                            'Ditunda'    => 'bg-red-50 text-red-600 border-red-100',
                            default      => 'bg-slate-50 text-slate-500 border-slate-200',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $sc }}">{{ $project->status }}</span>
                </div>
                <div class="flex justify-between"><span class="text-slate-500">Tahun</span><span class="text-slate-800 font-semibold">{{ $project->year }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Rencana Mulai</span><span class="text-slate-800 font-medium">{{ $project->start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Rencana Selesai</span><span class="text-slate-800 font-medium">{{ $project->end_date?->format('d M Y') ?? '-' }}</span></div>
                
                <div class="flex justify-between border-t border-slate-100 pt-3 mt-1"><span class="text-slate-500">Realisasi Mulai</span><span class="text-blue-600 font-semibold">{{ $project->actual_start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between items-start"><span class="text-slate-500 mt-0.5">Realisasi Selesai</span>
                    <div class="text-right">
                        <span class="text-blue-600 font-semibold block">{{ $project->actual_end_date?->format('d M Y') ?? '-' }}</span>
                        @if($project->delay_days > 0)
                            <span class="text-[10px] text-red-700 font-bold bg-red-50 border border-red-100 px-2 py-0.5 rounded-md mt-1 inline-block">Telat {{ $project->delay_days }} Hari</span>
                        @elseif($project->delay_days < 0)
                            <span class="text-[10px] text-green-700 font-bold bg-green-50 border border-green-100 px-2 py-0.5 rounded-md mt-1 inline-block">Maju {{ abs($project->delay_days) }} Hari</span>
                        @elseif($project->actual_end_date)
                            <span class="text-[10px] text-blue-700 font-bold bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-md mt-1 inline-block">Sesuai Target</span>
                        @endif
                    </div>
                </div>
                
                <div class="flex justify-between border-t border-slate-100 pt-3 mt-1"><span class="text-slate-500">Dibuat oleh</span><span class="text-slate-800 font-medium">{{ $project->creator->name ?? '-' }}</span></div>
            </div>
            @if($project->description)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">Deskripsi</p>
                <p class="text-sm text-slate-700">{{ $project->description }}</p>
            </div>
            @endif
        </div>

        {{-- Progress --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-bold text-slate-900">Progress</h2>
                <span class="text-2xl font-bold text-blue-600">{{ $project->progress }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 mb-4">
                <div class="h-3 rounded-full" style="width:{{ $project->progress }}%; background: linear-gradient(90deg,#4f80ff,#a78bfa)"></div>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="bg-green-50 border border-green-100 rounded-xl p-3">
                    <div class="text-lg font-bold text-green-600">{{ $project->tasks->where('status','Selesai')->count() }}</div>
                    <div class="text-[10px] text-slate-600 font-semibold">Selesai</div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3">
                    <div class="text-lg font-bold text-blue-600">{{ $project->tasks->where('status','Berjalan')->count() }}</div>
                    <div class="text-[10px] text-slate-600 font-semibold">Berjalan</div>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl p-3">
                    <div class="text-lg font-bold text-red-600">{{ $project->overdue_tasks_count }}</div>
                    <div class="text-[10px] text-slate-600 font-semibold">Overdue</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tasks & Subprojects --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-slate-900 text-base">Daftar Task & Sub-Project</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola pembagian tugas dan sub-project organisasi</p>
            </div>
            <div class="flex items-center gap-2">
                @if(auth()->user()->isAdminOrManager())
                <a href="{{ route('subprojects.create', ['project_id' => $project->id]) }}"
                   class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold px-4 py-2 rounded-xl text-sm transition-all shadow-sm flex items-center gap-1.5">+ Sub-Project</a>
                @endif
                @can('create', \App\Models\Task::class)
                <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all shadow-sm flex items-center gap-1.5">+ Task</a>
                @endcan
            </div>
        </div>

        {{-- Subprojects List --}}
        @if($project->subprojects->isNotEmpty())
            <div class="space-y-4">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">Sub-Project ({{ $project->subprojects->count() }})</p>
                @foreach($project->subprojects as $subproject)
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                        <!-- Subproject Header -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-800 text-sm flex items-center flex-wrap gap-2">
                                    <span>📂 {{ $subproject->name }}</span>
                                    @php
                                        $stc = match($subproject->status) {
                                            'Berjalan'   => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'Selesai'    => 'bg-green-50 text-green-600 border-green-100',
                                            'Perencanaan'=> 'bg-slate-50 text-slate-500 border-slate-200',
                                            'Ditunda'    => 'bg-red-50 text-red-600 border-red-100',
                                            default      => 'bg-slate-50 text-slate-500 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $stc }}">{{ $subproject->status }}</span>
                                </h3>
                                @if($subproject->description)
                                    <p class="text-xs text-slate-500 mt-1">{{ $subproject->description }}</p>
                                @endif
                            </div>
                            
                            <!-- Subproject Actions -->
                            @if(auth()->user()->isAdminOrManager())
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('subprojects.edit', $subproject->id) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-550 hover:bg-amber-50 rounded-lg transition-colors text-xs" title="Edit Sub-Project">✏️</a>
                                <form method="POST" action="{{ route('subprojects.destroy', $subproject->id) }}"
                                      onsubmit="return confirm('Hapus sub-project {{ $subproject->name }}? Task di dalamnya tidak akan terhapus tapi dipindahkan ke tugas langsung.')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-550 hover:bg-red-50 rounded-lg transition-colors text-xs cursor-pointer" title="Hapus Sub-Project">🗑</button>
                                </form>
                            </div>
                            @endif
                        </div>

                        <!-- Subproject Progress -->
                        <div>
                            <div class="flex justify-between items-center text-xs mb-1.5">
                                <span class="text-slate-500 font-medium">Progress Sub-Project</span>
                                <span class="font-bold text-blue-600">{{ $subproject->progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full bg-blue-600" style="width: {{ $subproject->progress }}%"></div>
                            </div>
                        </div>

                        <!-- Subproject Tasks -->
                        <div class="divide-y divide-slate-100 border-t border-slate-100 pt-1 mt-2">
                            @forelse($subproject->tasks as $task)
                                <a href="{{ route('tasks.show', $task->id) }}" class="flex items-center gap-4 py-3 hover:bg-slate-50 transition-colors group rounded-xl px-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-slate-800 group-hover:text-blue-600 transition-colors text-xs truncate">{{ $task->name }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">PIC: {{ $task->pics->pluck('name')->join(', ') ?: 'Tanpa PIC' }} • Due: {{ $task->due_date?->format('d M Y') ?? '-' }}</div>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <div class="flex-1 bg-slate-100 rounded-full h-1">
                                                @php $p = $task->progress; @endphp
                                                <div class="h-1 rounded-full" style="width:{{ $p }}%; background: {{ $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                                            </div>
                                            <span class="text-[9px] text-slate-500 w-8 shrink-0">{{ $p }}%</span>
                                        </div>
                                    </div>
                                    @php
                                        $ts = match($task->status) {
                                            'Berjalan'    => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'Selesai'     => 'bg-green-50 text-green-600 border-green-100',
                                            'Belum Mulai' => 'bg-slate-50 text-slate-500 border-slate-200',
                                            'Overdue'     => 'bg-red-50 text-red-600 border-red-100',
                                            default       => 'bg-slate-50 text-slate-500 border-slate-200',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border shrink-0 {{ $ts }}">{{ $task->status }}</span>
                                </a>
                            @empty
                                <p class="text-[11px] text-slate-400 py-2.5 text-center">Belum ada task di sub-project ini.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Direct Tasks Card --}}
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 text-xs">📋 Tugas Langsung (Direct Tasks)</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($project->tasks->whereNull('subproject_id') as $task)
                    <a href="{{ route('tasks.show', $task->id) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors group">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-slate-800 group-hover:text-blue-600 transition-colors text-sm truncate">{{ $task->name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">PIC: {{ $task->pics->pluck('name')->join(', ') ?: 'Tanpa PIC' }} • Due: {{ $task->due_date?->format('d M Y') ?? '-' }}</div>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                                    @php $p = $task->progress; @endphp
                                    <div class="h-1.5 rounded-full" style="width:{{ $p }}%; background: {{ $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                                </div>
                                <span class="text-[10px] text-slate-500 w-8 shrink-0">{{ $p }}%</span>
                            </div>
                        </div>
                        @php
                            $ts = match($task->status) {
                                'Berjalan'    => 'bg-blue-50 text-blue-600 border-blue-100',
                                'Selesai'     => 'bg-green-50 text-green-600 border-green-100',
                                'Belum Mulai' => 'bg-slate-50 text-slate-500 border-slate-200',
                                'Overdue'     => 'bg-red-50 text-red-600 border-red-100',
                                default       => 'bg-slate-50 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border shrink-0 {{ $ts }}">{{ $task->status }}</span>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center text-slate-500">
                        <div class="text-3xl mb-2">📋</div>
                        <p class="text-xs text-slate-500">Belum ada tugas langsung untuk project ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
