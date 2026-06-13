@extends('layouts.app')
@section('title', 'Daftar Program')
@section('heading', 'Program')
@section('subheading', 'Kelola seluruh program organisasi')

@section('content')
{{-- Header Actions --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <form method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        <div class="relative flex-1 sm:flex-none">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari program..."
                   class="w-full sm:w-auto bg-white/80 backdrop-blur-md border border-white/60 text-slate-900 placeholder-slate-400 rounded-2xl pl-10 pr-4 py-2.5 text-sm shadow-sm shadow-blue-900/5 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all min-w-[240px]">
        </div>
        
        <div class="relative">
            <select name="year" class="appearance-none bg-white/80 backdrop-blur-md border border-white/60 text-slate-700 font-medium rounded-2xl pl-4 pr-10 py-2.5 text-sm shadow-sm shadow-blue-900/5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition-all">
                <option value="">Semua Tahun</option>
                @php
                    $availableYears = \App\Models\Project::select('year')->distinct()->pluck('year')->toArray();
                    if (!in_array(date('Y'), $availableYears)) $availableYears[] = date('Y');
                    if (!empty($filters['year']) && !in_array($filters['year'], $availableYears)) $availableYears[] = $filters['year'];
                    rsort($availableYears);
                @endphp
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ ($filters['year'] ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
        </div>

        <div class="relative">
            <select name="status" class="appearance-none bg-white/80 backdrop-blur-md border border-white/60 text-slate-700 font-medium rounded-2xl pl-4 pr-10 py-2.5 text-sm shadow-sm shadow-blue-900/5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition-all">
                <option value="">Semua Status</option>
                @foreach(\App\Models\Project::STATUSES as $s)
                    <option value="{{ $s }}" {{ ($filters['status'] ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
        </div>

        <button type="submit" class="bg-white/80 backdrop-blur-md border border-white/60 text-blue-700 font-bold px-5 py-2.5 rounded-2xl text-sm shadow-sm shadow-blue-900/5 hover:bg-blue-50 transition-all">Filter</button>
        
        @if(!empty(array_filter($filters)))
            <a href="{{ route('projects.index') }}" class="text-slate-400 hover:text-rose-500 text-sm font-semibold transition-colors px-2">Reset</a>
        @endif
    </form>

    @can('create', \App\Models\Project::class)
    <a href="{{ route('projects.create') }}"
       class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-6 py-2.5 rounded-2xl text-sm shadow-lg shadow-blue-900/20 transition-all hover:-translate-y-0.5 flex items-center gap-2 shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Program
    </a>
    @endcan
</div>

{{-- Table --}}
<div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-3xl overflow-hidden shadow-xl shadow-blue-900/5">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200 divide-x divide-slate-200">
                    <th class="px-6 py-5 text-center font-bold">Nama Program</th>
                    <th class="px-6 py-5 text-center font-bold">Tahun</th>
                    <th class="px-6 py-5 text-center font-bold">Status</th>
                    <th class="px-6 py-5 text-center font-bold">Periode</th>
                    <th class="px-6 py-5 text-center font-bold">Progress</th>
                    <th class="px-6 py-5 text-center font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($projects as $project)
                <tr class="hover:bg-slate-50 transition-colors group divide-x divide-slate-100">
                    <td class="px-6 py-5">
                        <a href="{{ route('projects.show', $project->id) }}" class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors text-base block mb-0.5">
                            {{ $project->name }}
                        </a>
                        <div class="flex items-center gap-3 mt-1">
                            @if($project->description)
                                <p class="text-[11px] text-slate-500 truncate max-w-[250px] font-medium">{{ $project->description }}</p>
                            @endif
                            <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md font-semibold shrink-0">{{ $project->subprojects->count() }} Sub</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="font-bold text-slate-700">{{ $project->year }}</span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        @php
                            $sc = match($project->status) {
                                'Berjalan'   => 'bg-blue-100/50 text-blue-700 border-blue-200/50',
                                'Selesai'    => 'bg-emerald-100/50 text-emerald-700 border-emerald-200/50',
                                'Belum Mulai', 'Perencanaan' => 'bg-slate-100/50 text-slate-600 border-slate-200/50',
                                default      => 'bg-slate-100/50 text-slate-600 border-slate-200/50',
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-[11px] font-bold tracking-wide uppercase border {{ $sc }}">
                            {{ $project->status }}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-center text-slate-500 text-xs font-medium">
                        <div class="flex flex-col items-center gap-1">
                            <span class="flex items-center gap-1"><svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> {{ $project->start_date?->format('d M Y') ?? '-' }}</span>
                            <span class="flex items-center gap-1 text-slate-400"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg> {{ $project->end_date?->format('d M Y') ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 min-w-[140px] text-center">
                        <div class="flex items-center justify-center gap-3">
                            <div class="flex-1 bg-slate-100 rounded-full h-2 shadow-inner">
                                @php 
                                    $prog = $project->progress;
                                    $colorClass = $prog >= 75 ? 'from-emerald-400 to-emerald-500' : ($prog >= 40 ? 'from-blue-400 to-blue-500' : 'from-amber-400 to-amber-500');
                                @endphp
                                <div class="h-full rounded-full bg-gradient-to-r {{ $colorClass }} shadow-sm" style="width:{{ $prog }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-600 w-8 shrink-0 text-right">{{ $prog }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('projects.show', $project->id) }}"
                               class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-100 rounded-xl transition-all shadow-sm" title="Detail">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @can('update', $project)
                            <a href="{{ route('projects.edit', $project->id) }}"
                               class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-100 rounded-xl transition-all shadow-sm" title="Edit">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>   
                            </a>
                            @endcan
                            @can('delete', $project)
                            <form method="POST" action="{{ route('projects.destroy', $project->id) }}"
                                  onsubmit="return confirm('Hapus program {{ $project->name }}? Semua task terkait juga akan dihapus.')">
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
                    <td colspan="6" class="px-6 py-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 border border-slate-100 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-4 shadow-inner">📁</div>
                        <div class="font-bold text-slate-800 text-lg mb-1">Belum ada program</div>
                        <div class="text-sm text-slate-400">Tambahkan program pertama Anda untuk memulai kolaborasi.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
    <div class="px-6 py-5 border-t border-slate-100/80 bg-slate-50/30">
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection
