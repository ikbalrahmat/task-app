@extends('layouts.app')
@section('title', 'Manajemen Hari Libur')
@section('heading', 'Hari Libur Nasional')
@section('subheading', 'Kelola data hari libur untuk kalender SENTIMEN')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Toolbar --}}
    <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl px-5 py-3.5 shadow-sm flex flex-wrap items-center gap-3">
        {{-- Pilih Tahun --}}
        <form method="GET" class="flex items-center gap-2">
            <label class="text-xs font-semibold text-slate-500">Tahun:</label>
            <select name="tahun" onchange="this.form.submit()"
                    class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>

        <div class="flex-1"></div>

        {{-- Tombol Sync --}}
        <form method="POST" action="{{ route('hari-libur.sync', $tahun) }}">
            @csrf
            <button type="submit"
                    onclick="return confirm('Sync hari libur {{ $tahun }} dari API date.nager.at?')"
                    class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Sync dari API ({{ $tahun }})
            </button>
        </form>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
        @foreach($errors->all() as $e)<div>⚠️ {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Tambah Manual --}}
        <div class="lg:col-span-1">
            <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl p-5 shadow-sm">
                <h3 class="font-bold text-[#1e3a8a] mb-4 text-sm">Tambah Manual</h3>
                <form method="POST" action="{{ route('hari-libur.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                               class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Libur <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_libur" value="{{ old('nama_libur') }}" required
                               class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                               placeholder="Contoh: Hari Kemerdekaan RI">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sumber <span class="text-red-500">*</span></label>
                        <select name="sumber" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="nasional">Nasional</option>
                            <option value="internal">Internal</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] text-white font-bold py-2.5 rounded-xl text-sm hover:shadow-md transition-all">
                        Tambah Hari Libur
                    </button>
                </form>
            </div>
        </div>

        {{-- Tabel Daftar --}}
        <div class="lg:col-span-2">
            <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-[#1e3a8a] text-sm">Hari Libur {{ $tahun }}</h3>
                    <span class="text-xs text-slate-400">{{ $hariLibur->total() }} data</span>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($hariLibur as $h)
                    <div class="flex items-center px-5 py-3 hover:bg-slate-50/50 group">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($h->tanggal)->isoFormat('D MMMM YYYY') }}
                                </span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium
                                             {{ $h->sumber === 'nasional' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600' }}">
                                    {{ ucfirst($h->sumber) }}
                                </span>
                                @if(\Carbon\Carbon::parse($h->tanggal)->dayOfWeek === 0)
                                <span class="text-[10px] text-slate-400">(Minggu)</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $h->nama_libur }}</div>
                        </div>
                        <form method="POST" action="{{ route('hari-libur.destroy', $h->id) }}"
                              onsubmit="return confirm('Hapus hari libur ini?')" class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="text-center py-12 text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm">Belum ada data hari libur {{ $tahun }}</p>
                        <p class="text-xs mt-1">Klik "Sync dari API" untuk import otomatis</p>
                    </div>
                    @endforelse
                </div>
                @if($hariLibur->hasPages())
                <div class="px-5 py-3 border-t border-slate-100">{{ $hariLibur->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kembali ke Agenda --}}
    <div class="text-center">
        <a href="{{ route('agenda.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
            ← Kembali ke Agenda Tahunan
        </a>
    </div>
</div>
@endsection
