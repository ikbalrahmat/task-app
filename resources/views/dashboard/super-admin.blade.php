@extends('layouts.app')
@section('title', 'Super Admin — Overview')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full mb-2">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                ⚡ Super Admin Mode
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Overview Semua Unit Kerja</h1>
            <p class="text-sm text-slate-500 mt-1">Rekap data dari seluruh unit kerja yang terdaftar</p>
        </div>
        <a href="{{ route('unit-kerja.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Unit Kerja
        </a>
    </div>

    @include('partials.flash')

    {{-- Global Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-200">
            <div class="text-xs font-semibold uppercase tracking-wider text-blue-200 mb-2">Total Unit Kerja</div>
            <div class="text-4xl font-bold">{{ $totalUnitKerja }}</div>
        </div>
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg shadow-indigo-200">
            <div class="text-xs font-semibold uppercase tracking-wider text-indigo-200 mb-2">Total User</div>
            <div class="text-4xl font-bold">{{ $totalUsers }}</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl p-5 text-white shadow-lg shadow-emerald-200">
            <div class="text-xs font-semibold uppercase tracking-wider text-emerald-200 mb-2">Total Program</div>
            <div class="text-4xl font-bold">{{ $totalProjects }}</div>
        </div>
        <div class="bg-gradient-to-br from-violet-600 to-violet-700 rounded-2xl p-5 text-white shadow-lg shadow-violet-200">
            <div class="text-xs font-semibold uppercase tracking-wider text-violet-200 mb-2">Total Tugas</div>
            <div class="text-4xl font-bold">{{ $totalTasks }}</div>
        </div>
    </div>

    {{-- Unit Kerja Cards --}}
    <div>
        <h2 class="text-base font-semibold text-slate-700 mb-4">Detail Per Unit Kerja</h2>
        @if($unitKerjas->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center">
                <div class="text-5xl mb-4">🏢</div>
                <div class="text-slate-500 font-medium">Belum ada unit kerja yang aktif</div>
                <a href="{{ route('unit-kerja.create') }}" class="inline-block mt-4 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                    Buat Unit Kerja Pertama
                </a>
            </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($unitKerjas as $uk)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-blue-100 transition-all duration-200 p-5 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                            {{ strtoupper(substr($uk->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 text-sm leading-tight">{{ $uk->name }}</div>
                            <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $uk->code }}</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $uk->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                        {{ $uk->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                @if($uk->description)
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">{{ Str::limit($uk->description, 80) }}</p>
                @endif

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $uk->users_count }}</div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">User</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-indigo-600">{{ $uk->projects_count }}</div>
                        <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Program</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                    <a href="{{ route('unit-kerja.edit', $uk) }}"
                       class="flex-1 text-center px-3 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        Edit
                    </a>
                    <a href="{{ route('users.index') }}?unit_kerja={{ $uk->id }}"
                       class="flex-1 text-center px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
                        Lihat User
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Quick Links --}}
    <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-6 text-white">
        <h3 class="font-bold mb-4">Aksi Cepat Super Admin</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('unit-kerja.create') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-semibold transition-colors">
                🏢 Tambah Unit Kerja
            </a>
            <a href="{{ route('users.create') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-semibold transition-colors">
                👤 Tambah User
            </a>
            <a href="{{ route('activity-log.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-semibold transition-colors">
                📋 Activity Log
            </a>
            <a href="{{ route('unit-kerja.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-semibold transition-colors">
                ⚙ Kelola Unit Kerja
            </a>
        </div>
    </div>

</div>
@endsection
