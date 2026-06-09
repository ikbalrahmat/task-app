@extends('layouts.app')
@section('title', 'Daftar Project')
@section('heading', 'Project')
@section('subheading', 'Kelola seluruh project organisasi')

@section('content')
{{-- Header Actions --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari project..."
               class="bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 min-w-[220px]">
        <select name="year" class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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
        <select name="status" class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Project::STATUSES as $s)
                <option value="{{ $s }}" {{ ($filters['status'] ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-slate-100 border border-slate-200 text-slate-800 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-200 transition-colors">Filter</button>
        @if(!empty(array_filter($filters)))
            <a href="{{ route('projects.index') }}" class="text-slate-500 hover:text-slate-800 text-sm transition-colors">Reset</a>
        @endif
    </form>
    @can('create', \App\Models\Project::class)
    <a href="{{ route('projects.create') }}"
       class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 flex items-center gap-2 shrink-0">
        + Tambah Project
    </a>
    @endcan
</div>

{{-- Table --}}
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                    <th class="px-6 py-4 text-left font-semibold">Nama Project</th>
                    <th class="px-6 py-4 text-left font-semibold">Tahun</th>
                    <th class="px-6 py-4 text-left font-semibold">Status</th>
                    <th class="px-6 py-4 text-left font-semibold">Periode</th>
                    <th class="px-6 py-4 text-left font-semibold">Progress</th>
                    <th class="px-6 py-4 text-left font-semibold">Sub-Project</th>
                    <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($projects as $project)
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="px-6 py-4">
                        <a href="{{ route('projects.show', $project->id) }}" class="font-semibold text-slate-800 hover:text-blue-600 transition-colors">
                            {{ $project->name }}
                        </a>
                        @if($project->description)
                            <p class="text-xs text-slate-500 mt-0.5 truncate max-w-[200px]">{{ $project->description }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $project->year }}</td>
                    <td class="px-6 py-4">
                        @php
                            $sc = match($project->status) {
                                'Berjalan'   => 'bg-blue-50 text-blue-600 border-blue-100',
                                'Selesai'    => 'bg-green-50 text-green-600 border-green-100',
                                'Belum Mulai', 'Perencanaan' => 'bg-slate-50 text-slate-500 border-slate-200',
                                default      => 'bg-slate-50 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $sc }}">
                            {{ $project->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500 text-xs">
                        {{ $project->start_date?->format('d M Y') ?? '-' }}<br>
                        <span class="text-slate-600 font-medium">s/d {{ $project->end_date?->format('d M Y') ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 min-w-[120px]">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                                @php $prog = $project->progress; @endphp
                                <div class="h-1.5 rounded-full" style="width:{{ $prog }}%; background: {{ $prog >= 75 ? '#22c55e' : ($prog >= 40 ? '#4f80ff' : '#f59e0b') }}"></div>
                            </div>
                            <span class="text-xs text-slate-500 w-8 shrink-0">{{ $prog }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs text-slate-600">{{ $project->subprojects->count() }} sub-project</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('projects.show', $project->id) }}"
                               class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-550 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">👁</a>
                            @can('update', $project)
                            <a href="{{ route('projects.edit', $project->id) }}"
                               class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">✏️</a>
                            @endcan
                            @can('delete', $project)
                            <form method="POST" action="{{ route('projects.destroy', $project->id) }}"
                                  onsubmit="return confirm('Hapus project {{ $project->name }}? Semua task terkait juga akan dihapus.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">🗑</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                        <div class="text-4xl mb-3">📁</div>
                        <div class="font-semibold text-slate-800 mb-1">Belum ada project</div>
                        <div class="text-sm">Tambahkan project pertama Anda.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection
