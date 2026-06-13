@extends('layouts.app')
@section('title', 'Daftar List')
@section('heading', 'List')
@section('subheading', 'Kelola seluruh list di bawah program utama')

@section('content')
{{-- Header Actions --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari list..."
               class="bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 min-w-[220px]">
        
        <select name="project_id" class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">Semua Program</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="bg-slate-100 border border-slate-200 text-slate-800 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-200 transition-colors">Filter</button>
        @if(request()->filled('search') || request()->filled('project_id'))
            <a href="{{ route('subprojects.index') }}" class="text-slate-500 hover:text-slate-800 text-sm transition-colors">Reset</a>
        @endif
    </form>
    
    @if(auth()->user()->isAdminOrManager())
    <a href="{{ route('subprojects.create') }}"
       class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 flex items-center gap-2 shrink-0">
        + Tambah List
    </a>
    @endif
</div>

{{-- Table --}}
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200 divide-x divide-slate-200">
                    <th class="px-6 py-4 text-center font-semibold">Nama List</th>
                    <th class="px-6 py-4 text-center font-semibold">Program Utama</th>
                    <th class="px-6 py-4 text-center font-semibold">Status</th>
                    <th class="px-6 py-4 text-center font-semibold">Periode</th>
                    <th class="px-6 py-4 text-center font-semibold">Progress</th>
                    <th class="px-6 py-4 text-center font-semibold">Task</th>
                    <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($subprojects as $subproject)
                <tr class="hover:bg-slate-50 transition-colors divide-x divide-slate-100 group">
                    <td class="px-6 py-4">
                        <span class="font-semibold text-slate-800">
                            {{ $subproject->name }}
                        </span>
                        @if($subproject->description)
                            <p class="text-xs text-slate-500 mt-0.5 truncate max-w-[200px]">{{ $subproject->description }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('projects.show', $subproject->project_id) }}" class="text-blue-600 hover:text-blue-700 font-medium">
                            {{ $subproject->project->name ?? '-' }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $sc = match($subproject->status) {
                                'Berjalan'   => 'bg-blue-50 text-blue-600 border-blue-100',
                                'Selesai'    => 'bg-green-50 text-green-600 border-green-100',
                                'Belum Mulai', 'Perencanaan'=> 'bg-slate-50 text-slate-500 border-slate-200',
                                'Ditunda'    => 'bg-red-50 text-red-600 border-red-100',
                                default      => 'bg-slate-50 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $sc }}">
                            {{ $subproject->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-slate-500 text-xs">
                        {{ $subproject->start_date?->format('d M Y') ?? '-' }}<br>
                        <span class="text-slate-600 font-medium">s/d {{ $subproject->end_date?->format('d M Y') ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 min-w-[120px] text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="flex-1 max-w-[80px] bg-slate-100 rounded-full h-1.5">
                                @php $prog = $subproject->progress; @endphp
                                <div class="h-1.5 rounded-full" style="width:{{ $prog }}%; background: {{ $prog >= 75 ? '#22c55e' : ($prog >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                            </div>
                            <span class="text-xs text-slate-500 w-8 shrink-0">{{ $prog }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs text-slate-600">{{ $subproject->tasks->count() }} task</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('subprojects.show', $subproject->id) }}"
                               class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-100 rounded-xl transition-all shadow-sm" title="Detail">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @can('update', $subproject)
                            <a href="{{ route('subprojects.edit', $subproject->id) }}"
                               class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-100 rounded-xl transition-all shadow-sm" title="Edit">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>   
                            </a>
                            @endcan
                            @can('delete', $subproject)
                            <form method="POST" action="{{ route('subprojects.destroy', $subproject->id) }}"
                                  onsubmit="return confirm('Hapus list ini?')">
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
                        <div class="text-4xl mb-3">📁</div>
                        <div class="font-semibold text-slate-800 mb-1">Belum ada list</div>
                        <div class="text-sm">Tambahkan list pertama Anda.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subprojects->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $subprojects->links() }}
    </div>
    @endif
</div>
@endsection
