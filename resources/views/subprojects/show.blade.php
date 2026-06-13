@extends('layouts.app')
@section('title', $subproject->name)
@section('heading', $subproject->name)
@section('subheading', 'Detail list & daftar task')

@section('content')
<div class="flex flex-wrap items-center gap-4 mb-8">
    <a href="{{ route('subprojects.index') }}" class="flex items-center gap-2 bg-white/70 hover:bg-white border border-slate-200 text-slate-600 hover:text-slate-900 shadow-sm hover:shadow font-bold px-4 py-2.5 rounded-xl text-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>

    @if(auth()->user()->isAdminOrManager())
    <a href="{{ route('subprojects.edit', $subproject->id) }}" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-amber-600/20 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit
    </a>
    
    <form method="POST" action="{{ route('subprojects.destroy', $subproject->id) }}" onsubmit="return confirm('Hapus list ini? Task di dalamnya tidak akan terhapus tapi dipindahkan ke program induk.')" class="ml-auto">
        @csrf @method('DELETE')
        <button type="submit" class="bg-rose-100/50 hover:bg-rose-100 text-rose-700 border border-rose-200/60 font-bold px-5 py-2.5 rounded-xl text-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Hapus
        </button>
    </form>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
    {{-- Background decorations --}}
    <div class="absolute -left-20 -top-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute right-0 bottom-0 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Left: Info Card & Progress --}}
    <div class="lg:col-span-1 space-y-6 relative z-10">
        {{-- Info Card --}}
        <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-7 shadow-xl shadow-blue-900/5 relative overflow-hidden">
            <h2 class="font-black text-slate-800 mb-6 text-lg flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-blue-100 text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                Informasi List
            </h2>
            
            <div class="space-y-4 text-sm font-medium">
                <div class="flex justify-between items-center py-2 border-b border-slate-100/80">
                    <span class="text-slate-500">Program Induk</span>
                    <a href="{{ route('projects.show', $subproject->project_id) }}" class="text-blue-600 hover:text-blue-700 font-bold max-w-[150px] truncate bg-blue-50 px-2.5 py-1 rounded-lg">
                        {{ $subproject->project->name ?? '-' }}
                    </a>
                </div>
                
                <div class="flex justify-between items-center py-2"><span class="text-slate-500">Rencana Mulai</span><span class="text-slate-800 font-bold">{{ $subproject->start_date?->format('d M Y') ?? '-' }}</span></div>
                <div class="flex justify-between items-center py-2"><span class="text-slate-500">Rencana Selesai</span><span class="text-slate-800 font-bold">{{ $subproject->end_date?->format('d M Y') ?? '-' }}</span></div>
                
                <div class="flex justify-between items-center pt-4 mt-2 border-t border-slate-200/60">
                    <span class="text-slate-500">Realisasi Mulai</span>
                    <div class="text-right">
                        <span class="text-blue-600 font-black block">{{ $subproject->actual_start_date?->format('d M Y') ?? '-' }}</span>
                        @if($subproject->start_delay_days > 0)
                            <span class="text-[10px] text-rose-700 font-black bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Telat Mulai {{ $subproject->start_delay_days }} Hari</span>
                        @elseif($subproject->start_delay_days < 0)
                            <span class="text-[10px] text-emerald-700 font-black bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Mulai Lebih Cepat {{ abs($subproject->start_delay_days) }} Hari</span>
                        @elseif($subproject->actual_start_date)
                            <span class="text-[10px] text-blue-700 font-black bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Tepat Waktu</span>
                        @endif
                    </div>
                </div>
                @if($subproject->actual_start_remarks)
                    <div class="bg-amber-50/80 border border-amber-200/50 rounded-xl p-3 text-xs text-amber-800 mt-2 shadow-sm">
                        <span class="font-bold block mb-0.5">Catatan Deviasi Mulai:</span> {{ $subproject->actual_start_remarks }}
                    </div>
                @endif

                <div class="flex justify-between items-center pt-3">
                    <span class="text-slate-500">Realisasi Selesai</span>
                    <div class="text-right">
                        <span class="text-blue-600 font-black block">{{ $subproject->actual_end_date?->format('d M Y') ?? '-' }}</span>
                        @if($subproject->delay_days > 0)
                            <span class="text-[10px] text-rose-700 font-black bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Telat Selesai {{ $subproject->delay_days }} Hari</span>
                        @elseif($subproject->delay_days < 0)
                            <span class="text-[10px] text-emerald-700 font-black bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Selesai Lebih Cepat {{ abs($subproject->delay_days) }} Hari</span>
                        @elseif($subproject->actual_end_date)
                            <span class="text-[10px] text-blue-700 font-black bg-blue-100 border border-blue-200 px-2.5 py-1 rounded-md mt-1.5 inline-block shadow-sm">Tepat Waktu</span>
                        @endif
                    </div>
                </div>
                @if($subproject->actual_end_remarks)
                    <div class="bg-amber-50/80 border border-amber-200/50 rounded-xl p-3 text-xs text-amber-800 mt-2 shadow-sm">
                        <span class="font-bold block mb-0.5">Catatan Deviasi Selesai:</span> {{ $subproject->actual_end_remarks }}
                    </div>
                @endif
                
                <div class="flex justify-between items-center pt-4 mt-2 border-t border-slate-200/60">
                    <span class="text-slate-500">Status</span>
                    @php
                        $sc = match($subproject->status) {
                            'Berjalan'    => 'bg-blue-100 text-blue-700 border-blue-200 shadow-sm shadow-blue-500/10',
                            'Selesai'     => 'bg-emerald-100 text-emerald-700 border-emerald-200 shadow-sm shadow-emerald-500/10',
                            'Belum Mulai' => 'bg-slate-100 text-slate-600 border-slate-200 shadow-sm',
                            'Ditunda'     => 'bg-red-100 text-red-700 border-red-200 shadow-sm shadow-red-500/10',
                            default       => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                    @endphp
                    <span class="px-3 py-1.5 rounded-xl text-xs font-black border {{ $sc }}">{{ $subproject->status }}</span>
                </div>
                
                <div class="flex justify-between items-center pt-2"><span class="text-slate-500">Dibuat oleh</span><span class="text-slate-800 font-bold">{{ $subproject->creator->name ?? '-' }}</span></div>
            </div>
            
            @if($subproject->description)
            <div class="mt-6 pt-5 border-t border-slate-200/60">
                <p class="text-sm font-bold text-slate-700 mb-2">Deskripsi</p>
                <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100 text-sm text-slate-600 leading-relaxed">
                    {{ $subproject->description }}
                </div>
            </div>
            @endif
        </div>

        {{-- Progress Card --}}
        <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl p-7 shadow-xl shadow-blue-900/5 relative overflow-hidden">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-black text-slate-800 text-lg flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></span>
                    Progress
                </h2>
                <span class="text-2xl font-black text-blue-600">{{ $subproject->progress }}%</span>
            </div>
            
            <div class="w-full bg-slate-100/80 rounded-full h-3 shadow-inner overflow-hidden mb-6">
                @php $p = $subproject->progress; @endphp
                <div class="h-full rounded-full transition-all duration-1000 ease-out" 
                     style="width:{{ $p }}%; background: {{ $p >= 75 ? 'linear-gradient(to right, #10b981, #059669)' : ($p >= 40 ? 'linear-gradient(to right, #3b82f6, #2563eb)' : 'linear-gradient(to right, #f59e0b, #d97706)') }}">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 shadow-sm">
                    <div class="text-2xl font-black text-emerald-600 mb-1">{{ $subproject->tasks->where('status','Selesai')->count() }}</div>
                    <div class="text-xs text-emerald-700 font-bold uppercase tracking-wider">Selesai</div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 shadow-sm">
                    <div class="text-2xl font-black text-blue-600 mb-1">{{ $subproject->tasks->where('status','Berjalan')->count() }}</div>
                    <div class="text-xs text-blue-700 font-bold uppercase tracking-wider">Berjalan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Tasks List --}}
    <div class="lg:col-span-2 relative z-10">
        <div class="bg-white/80 backdrop-blur-md border border-white/60 shadow-xl shadow-blue-900/5 rounded-3xl overflow-hidden flex flex-col h-full">
            <div class="p-6 md:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100/80 bg-white/50">
                <div>
                    <h2 class="font-black text-slate-800 text-lg flex items-center gap-2 mb-1">
                        <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></span>
                        Daftar Task List
                    </h2>
                    <p class="text-sm text-slate-500 font-medium">Kelola dan pantau seluruh task di list ini</p>
                </div>
                @can('create', \App\Models\Task::class)
                <a href="{{ route('tasks.create', ['project_id' => $subproject->project_id, 'subproject_id' => $subproject->id]) }}"
                   class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition-all shadow-md shadow-blue-600/20 flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Task
                </a>
                @endcan
            </div>

            {{-- Tasks Table --}}
            <div class="p-6 md:p-8 flex-1">
                <div class="space-y-4">
                    @forelse($subproject->tasks as $task)
                        <a href="{{ route('tasks.show', $task->id) }}" class="flex flex-col sm:flex-row sm:items-center gap-5 p-5 bg-white border border-slate-100 hover:border-blue-200 rounded-2xl hover:shadow-md hover:shadow-blue-900/5 transition-all group relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $task->status === 'Selesai' ? 'bg-emerald-500' : ($task->status === 'Overdue' ? 'bg-rose-500' : 'bg-blue-500') }} opacity-80"></div>
                            
                            <div class="flex-1 min-w-0 pl-2">
                                <div class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors text-base truncate mb-1">{{ $task->name }}</div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-medium text-slate-500 mb-3">
                                    <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                        <span class="text-slate-400">👤</span>
                                        {{ $task->pics->pluck('name')->join(', ') ?: 'Belum ada PIC' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 {{ $task->isOverdue() ? 'text-rose-600 bg-rose-50 border-rose-100' : 'bg-slate-50 border-slate-100' }} px-2 py-1 rounded-lg border">
                                        <span class="{{ $task->isOverdue() ? 'text-rose-500' : 'text-slate-400' }}">⏰</span>
                                        Due: {{ $task->due_date?->format('d M Y') ?? '-' }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-slate-100 rounded-full h-2">
                                        @php $tp = $task->progress; @endphp
                                        <div class="h-2 rounded-full transition-all" style="width:{{ $tp }}%; background: {{ $tp >= 75 ? '#10b981' : ($tp >= 40 ? '#3b82f6' : '#f59e0b') }}"></div>
                                    </div>
                                    <span class="text-xs font-black text-slate-400 w-8 shrink-0 text-right">{{ $tp }}%</span>
                                </div>
                            </div>
                            
                            <div class="shrink-0 flex items-center">
                                @php
                                    $ts = match($task->status) {
                                        'Berjalan'    => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'Selesai'     => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'Belum Mulai' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        'Overdue'     => 'bg-rose-100 text-rose-700 border-rose-200',
                                        default       => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="px-3 py-1.5 rounded-xl text-xs font-black border {{ $ts }}">{{ $task->status }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-16 text-center bg-slate-50/50 rounded-2xl border border-slate-200 border-dashed">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl mx-auto mb-4 shadow-sm border border-slate-100">📝</div>
                            <p class="font-bold text-slate-700 mb-1 text-base">Belum ada task sama sekali</p>
                            <p class="text-sm font-medium text-slate-500">Klik tombol "Tambah Task" untuk memecah list ini menjadi langkah-langkah kecil.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
