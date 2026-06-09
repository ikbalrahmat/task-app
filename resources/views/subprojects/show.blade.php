@extends('layouts.app')
@section('title', $subproject->name)
@section('heading', $subproject->name)
@section('subheading', 'Detail sub-project & daftar task')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <a href="{{ route('subprojects.index') }}" class="text-slate-500 hover:text-slate-950 text-sm font-semibold transition-colors">← Kembali</a>
    @if(auth()->user()->isAdminOrManager())
    <a href="{{ route('subprojects.edit', $subproject->id) }}" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">✏️ Edit</a>
    <form method="POST" action="{{ route('subprojects.destroy', $subproject->id) }}"
          onsubmit="return confirm('Hapus sub-project ini? Task di dalamnya tidak akan terhapus tapi dipindahkan ke tugas langsung.')">
        @csrf @method('DELETE')
        <button type="submit" class="bg-red-700 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">🗑 Hapus</button>
    </form>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Left: Info Card --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h2 class="font-bold text-slate-900 mb-4">Informasi Sub-Project</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">Status</span>
                    @php
                        $sc = match($subproject->status) {
                            'Berjalan'   => 'bg-blue-50 text-blue-600 border-blue-100',
                            'Selesai'    => 'bg-green-50 text-green-600 border-green-100',
                            'Belum Mulai', 'Perencanaan'=> 'bg-slate-50 text-slate-500 border-slate-200',
                            'Ditunda'    => 'bg-red-50 text-red-600 border-red-100',
                            default      => 'bg-slate-50 text-slate-500 border-slate-200',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $sc }}">{{ $subproject->status }}</span>
                </div>
                <div class="flex justify-between items-start">
                    <span class="text-slate-500">Project Induk</span>
                    <a href="{{ route('projects.show', $subproject->project_id) }}" class="text-blue-600 hover:text-blue-700 text-right font-medium max-w-[150px] truncate">
                        {{ $subproject->project->name ?? '-' }}
                    </a>
                </div>
                <div class="flex justify-between"><span class="text-slate-500">Rencana Mulai</span><span class="text-slate-800 font-medium">{{ $subproject->start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Rencana Selesai</span><span class="text-slate-800 font-medium">{{ $subproject->end_date?->format('d M Y') ?? '-' }}</span></div>
                
                <div class="flex justify-between border-t border-slate-100 pt-3 mt-1">
                    <span class="text-slate-500">Realisasi Mulai</span>
                    <span class="text-blue-600 font-semibold">{{ $subproject->actual_start_date?->format('d M Y') ?? '-' }}</span>
                </div>
                @if($subproject->actual_start_remarks)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-2 text-xs text-amber-800 mt-1">
                        <span class="font-semibold">Alasan deviasi:</span> {{ $subproject->actual_start_remarks }}
                    </div>
                @endif

                <div class="flex justify-between items-start pt-2">
                    <span class="text-slate-500 mt-0.5">Realisasi Selesai</span>
                    <span class="text-blue-600 font-semibold text-right">{{ $subproject->actual_end_date?->format('d M Y') ?? '-' }}</span>
                </div>
                @if($subproject->actual_end_remarks)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-2 text-xs text-amber-800 mt-1">
                        <span class="font-semibold">Alasan deviasi:</span> {{ $subproject->actual_end_remarks }}
                    </div>
                @endif
                
                <div class="flex justify-between border-t border-slate-100 pt-3 mt-1"><span class="text-slate-500">Dibuat oleh</span><span class="text-slate-800 font-medium">{{ $subproject->creator->name ?? '-' }}</span></div>
            </div>
            @if($subproject->description)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">Deskripsi</p>
                <p class="text-sm text-slate-700">{{ $subproject->description }}</p>
            </div>
            @endif
        </div>

        {{-- Progress --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-bold text-slate-900">Progress</h2>
                <span class="text-2xl font-bold text-blue-600">{{ $subproject->progress }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 mb-4">
                <div class="h-3 rounded-full" style="width:{{ $subproject->progress }}%; background: linear-gradient(90deg,#4f80ff,#a78bfa)"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="bg-green-50 border border-green-100 rounded-xl p-3">
                    <div class="text-lg font-bold text-green-600">{{ $subproject->tasks->where('status','Selesai')->count() }}</div>
                    <div class="text-[10px] text-slate-600 font-semibold">Selesai</div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3">
                    <div class="text-lg font-bold text-blue-600">{{ $subproject->tasks->where('status','Berjalan')->count() }}</div>
                    <div class="text-[10px] text-slate-600 font-semibold">Berjalan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Tasks List --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-slate-900 text-base">Daftar Task Sub-Project</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola pembagian tugas sub-project</p>
            </div>
            @can('create', \App\Models\Task::class)
            <a href="{{ route('tasks.create', ['project_id' => $subproject->project_id, 'subproject_id' => $subproject->id]) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all shadow-sm flex items-center gap-1.5 shrink-0">
                + Tambah Task
            </a>
            @endcan
        </div>

        {{-- Tasks Table --}}
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl overflow-hidden">
            <div class="divide-y divide-slate-100">
                @forelse($subproject->tasks as $task)
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
                    <div class="px-6 py-16 text-center text-slate-500">
                        <div class="text-4xl mb-3">📋</div>
                        <p class="font-semibold text-slate-850 mb-1">Belum ada tugas</p>
                        <p class="text-xs text-slate-500">Tambahkan tugas pertama Anda untuk sub-project ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
