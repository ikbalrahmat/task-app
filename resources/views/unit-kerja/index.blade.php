@extends('layouts.app')
@section('title', 'Manajemen Unit Kerja')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Unit Kerja</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola semua unit kerja yang terdaftar dalam sistem</p>
        </div>
        <a href="{{ route('unit-kerja.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Unit Kerja
        </a>
    </div>

    @include('partials.flash')

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <div class="text-sm text-slate-500 mb-1">Total Unit Kerja</div>
            <div class="text-3xl font-bold text-blue-600">{{ $unitKerjas->total() }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <div class="text-sm text-slate-500 mb-1">Unit Aktif</div>
            <div class="text-3xl font-bold text-emerald-600">{{ $unitKerjas->where('is_active', true)->count() }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <div class="text-sm text-slate-500 mb-1">Unit Nonaktif</div>
            <div class="text-3xl font-bold text-slate-400">{{ $unitKerjas->where('is_active', false)->count() }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500">Unit Kerja</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500">Kode</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500">User</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500">Project</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($unitKerjas as $uk)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-4">
                        <div class="font-semibold text-slate-800">{{ $uk->name }}</div>
                        @if($uk->description)
                        <div class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">{{ $uk->description }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-mono font-bold rounded-lg">{{ $uk->code }}</span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="font-semibold text-slate-700">{{ $uk->users_count }}</span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="font-semibold text-slate-700">{{ $uk->projects_count }}</span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($uk->is_active)
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full">Aktif</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-xs font-semibold rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('unit-kerja.edit', $uk) }}"
                               class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('unit-kerja.destroy', $uk) }}" method="POST" onsubmit="return confirm('Hapus unit kerja {{ $uk->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                        <div class="text-4xl mb-3">🏢</div>
                        <div class="font-medium">Belum ada unit kerja</div>
                        <div class="text-xs mt-1">Klik "Tambah Unit Kerja" untuk memulai</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($unitKerjas->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $unitKerjas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
