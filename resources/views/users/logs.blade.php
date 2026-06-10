@extends('layouts.app')
@section('title', 'Log Aktivitas Audit')
@section('heading', 'Log Aktivitas Keamanan')
@section('subheading', 'Rekaman audit akses dan perubahan sistem (Read-only)')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex items-center gap-3 w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari event, deskripsi, user, IP..."
               class="bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 min-w-[280px] w-full sm:w-auto">
        <button type="submit" class="bg-slate-100 border border-slate-200 text-slate-800 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-200 transition-colors">Cari</button>
        @if(request('search'))
            <a href="{{ route('activity-log.index') }}" class="text-xs text-blue-600 hover:underline">Reset</a>
        @endif
    </form>
    <a href="{{ route('users.index') }}"
       class="bg-slate-100 border border-slate-200 text-slate-800 px-5 py-2.5 rounded-xl text-sm hover:bg-slate-200 transition-colors shrink-0 shadow-sm">
        ← Kembali ke Pengguna
    </a>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                    <th class="px-6 py-4 text-left font-semibold">Tanggal & Waktu</th>
                    <th class="px-6 py-4 text-left font-semibold">Pengguna</th>
                    <th class="px-6 py-4 text-left font-semibold">Tipe Event</th>
                    <th class="px-6 py-4 text-left font-semibold">Deskripsi</th>
                    <th class="px-6 py-4 text-left font-semibold">IP Address</th>
                    <th class="px-6 py-4 text-left font-semibold">Metode & URL</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-slate-600 text-xs whitespace-nowrap">
                        {{ $log->created_at?->timezone('Asia/Jakarta')->format('d M Y, H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($log->user)
                            <span class="font-semibold text-slate-800">{{ $log->user->name }}</span>
                            <span class="text-xs text-slate-400 block">{{ $log->user->email }}</span>
                        @else
                            <span class="text-slate-400 font-medium italic">Sistem / Tamu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $colorClass = match(true) {
                                str_contains($log->event_type, 'success') => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                str_contains($log->event_type, 'failed') || str_contains($log->event_type, 'lockout') => 'bg-red-50 text-red-600 border-red-100',
                                str_contains($log->event_type, 'created') => 'bg-blue-50 text-blue-600 border-blue-100',
                                str_contains($log->event_type, 'deleted') => 'bg-rose-50 text-rose-600 border-rose-100',
                                default => 'bg-slate-50 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-semibold border {{ $colorClass }}">
                            {{ $log->event_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-700 text-sm max-w-xs break-words">
                        {{ $log->description }}
                    </td>
                    <td class="px-6 py-4 text-slate-500 text-xs whitespace-nowrap">
                        {{ $log->ip_address }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] uppercase font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ $log->method }}</span>
                        <span class="text-slate-600 text-xs truncate block mt-1 max-w-[200px]" title="{{ $log->url }}">{{ $log->url }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-slate-500">
                        <div class="text-4xl mb-3">📋</div>
                        <p class="font-semibold text-slate-800">Belum ada rekaman audit log.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
