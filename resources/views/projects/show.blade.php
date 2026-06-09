@extends('layouts.app')
@section('title', $project->name)
@section('heading', $project->name)
@section('subheading', 'Detail project & daftar task')

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-6">
    <a href="{{ route('projects.index') }}" class="text-slate-400 hover:text-white text-sm transition-colors">← Kembali</a>
    @can('update', $project)
    <a href="{{ route('projects.edit', $project->id) }}" class="bg-amber-600 hover:bg-amber-9500 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">✏️ Edit</a>
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
        <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6">
            <h2 class="font-bold text-white mb-4">Informasi Project</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-slate-400">Status</span>
                    @php
                        $sc = match($project->status) {
                            'Berjalan'   => 'bg-blue-950 text-blue-400',
                            'Selesai'    => 'bg-green-950 text-green-400',
                            'Perencanaan'=> 'bg-[#222535] text-slate-300',
                            'Ditunda'    => 'bg-red-950 text-red-400',
                            default      => 'bg-[#222535] text-slate-300',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $sc }}">{{ $project->status }}</span>
                </div>
                <div class="flex justify-between"><span class="text-slate-400">Tahun</span><span class="text-white font-medium">{{ $project->year }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Rencana Mulai</span><span class="text-white">{{ $project->start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-400">Rencana Selesai</span><span class="text-white">{{ $project->end_date?->format('d M Y') ?? '-' }}</span></div>
                
                <div class="flex justify-between border-t border-[#333650] pt-3 mt-1"><span class="text-slate-400">Realisasi Mulai</span><span class="text-blue-300 font-semibold">{{ $project->actual_start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between items-start"><span class="text-slate-400 mt-0.5">Realisasi Selesai</span>
                    <div class="text-right">
                        <span class="text-blue-300 font-semibold block">{{ $project->actual_end_date?->format('d M Y') ?? '-' }}</span>
                        @if($project->delay_days > 0)
                            <span class="text-[10px] text-red-400 font-bold bg-red-950/50 px-2 py-0.5 rounded-md mt-1 inline-block">Telat {{ $project->delay_days }} Hari</span>
                        @elseif($project->delay_days < 0)
                            <span class="text-[10px] text-green-400 font-bold bg-green-950/50 px-2 py-0.5 rounded-md mt-1 inline-block">Maju {{ abs($project->delay_days) }} Hari</span>
                        @elseif($project->actual_end_date)
                            <span class="text-[10px] text-blue-400 font-bold bg-blue-950/50 px-2 py-0.5 rounded-md mt-1 inline-block">Sesuai Target</span>
                        @endif
                    </div>
                </div>
                
                <div class="flex justify-between border-t border-[#333650] pt-3 mt-1"><span class="text-slate-400">Dibuat oleh</span><span class="text-white">{{ $project->creator->name ?? '-' }}</span></div>
            </div>
            @if($project->description)
            <div class="mt-4 pt-4 border-t border-[#333650]">
                <p class="text-xs text-slate-400 mb-2">Deskripsi</p>
                <p class="text-sm text-slate-300">{{ $project->description }}</p>
            </div>
            @endif
        </div>

        {{-- Progress --}}
        <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-6">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-bold text-white">Progress</h2>
                <span class="text-2xl font-bold text-blue-400">{{ $project->progress }}%</span>
            </div>
            <div class="w-full bg-[#222535] rounded-full h-3 mb-4">
                <div class="h-3 rounded-full" style="width:{{ $project->progress }}%; background: linear-gradient(90deg,#4f80ff,#a78bfa)"></div>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="bg-green-950/40 rounded-xl p-3">
                    <div class="text-lg font-bold text-green-400">{{ $project->tasks->where('status','Selesai')->count() }}</div>
                    <div class="text-[10px] text-slate-400">Selesai</div>
                </div>
                <div class="bg-blue-950/40 rounded-xl p-3">
                    <div class="text-lg font-bold text-blue-400">{{ $project->tasks->where('status','Berjalan')->count() }}</div>
                    <div class="text-[10px] text-slate-400">Berjalan</div>
                </div>
                <div class="bg-red-950/40 rounded-xl p-3">
                    <div class="text-lg font-bold text-red-400">{{ $project->overdue_tasks_count }}</div>
                    <div class="text-[10px] text-slate-400">Overdue</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tasks --}}
    <div class="lg:col-span-2">
        <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-[#333650]">
                <h2 class="font-bold text-white">Daftar Task ({{ $project->tasks->count() }})</h2>
                @can('create', \App\Models\Task::class)
                <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}"
                   class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">+ Task</a>
                @endcan
            </div>
            <div class="divide-y divide-[#333650]">
                @forelse($project->tasks as $task)
                <a href="{{ route('tasks.show', $task->id) }}"
                   class="flex items-center gap-4 px-6 py-4 hover:bg-[#222535] transition-colors group">
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-white group-hover:text-blue-400 transition-colors text-sm truncate">{{ $task->name }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">PIC: {{ $task->pic->name ?? '-' }} • Due: {{ $task->due_date?->format('d M Y') ?? '-' }}</div>
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex-1 bg-[#222535] rounded-full h-1.5">
                                @php $p = $task->progress; @endphp
                                <div class="h-1.5 rounded-full" style="width:{{ $p }}%; background: {{ $p >= 75 ? '#22c55e' : ($p >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                            </div>
                            <span class="text-[10px] text-slate-400 w-8 shrink-0">{{ $p }}%</span>
                        </div>
                    </div>
                    @php
                        $ts = match($task->status) {
                            'Berjalan'    => 'bg-blue-950 text-blue-400',
                            'Selesai'     => 'bg-green-950 text-green-400',
                            'Belum Mulai' => 'bg-[#222535] text-slate-400',
                            'Overdue'     => 'bg-red-950 text-red-400',
                            default       => 'bg-[#222535] text-slate-400',
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold shrink-0 {{ $ts }}">{{ $task->status }}</span>
                </a>
                @empty
                <div class="px-6 py-12 text-center text-slate-400">
                    <div class="text-3xl mb-2">📋</div>
                    <p class="text-sm">Belum ada task untuk project ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
