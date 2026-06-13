@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('heading', 'Pengguna')
@section('subheading', 'Kelola akun dan hak akses pengguna')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex items-center gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau email..."
               class="bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 min-w-[220px]">
        <select name="role" class="bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">Semua Role</option>
            @foreach(['Admin','Pengendali Teknis','Ketua Tim','Anggota Tim'] as $r)
                <option value="{{ $r }}" {{ ($filters['role'] ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-slate-100 border border-slate-200 text-slate-800 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-200 transition-colors">Filter</button>
    </form>
    <div class="flex gap-2">

        <a href="{{ route('users.create') }}"
           class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 shrink-0 shadow-sm hover:shadow-md">
            + Tambah Pengguna
        </a>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200 divide-x divide-slate-200">
                    <th class="px-6 py-4 text-center font-semibold">Pengguna</th>
                    <th class="px-6 py-4 text-center font-semibold">Email</th>
                    <th class="px-6 py-4 text-center font-semibold">Role</th>
                    <th class="px-6 py-4 text-center font-semibold">Status</th>
                    <th class="px-6 py-4 text-center font-semibold">Departemen</th>
                    <th class="px-6 py-4 text-center font-semibold">Bergabung</th>
                    <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition-colors divide-x divide-slate-100">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shrink-0
                                {{ $user->role === 'Admin' ? 'bg-purple-50 text-purple-600 border border-purple-100' :
                                   ($user->role === 'Pengendali Teknis' ? 'bg-blue-50 text-blue-600 border border-blue-100' :
                                   ($user->role === 'Ketua Tim' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-slate-50 border border-slate-200 text-slate-600')) }}">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <span class="text-[10px] text-blue-600 font-semibold">(Anda)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $rc = match($user->role) {
                                'Admin'             => 'bg-purple-50 text-purple-600 border-purple-100',
                                'Pengendali Teknis' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'Ketua Tim'         => 'bg-green-50 text-green-600 border-green-100',
                                'Anggota Tim'       => 'bg-slate-50 text-slate-500 border-slate-200',
                                default   => 'bg-slate-50 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold border {{ $rc }}">{{ $user->role }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->is_locked)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border bg-red-50 text-red-600 border-red-100">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Terkunci
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border bg-emerald-50 text-emerald-600 border-emerald-100">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-slate-500 text-sm">{{ $user->department ?? '-' }}</td>
                    <td class="px-6 py-4 text-center text-slate-500 text-xs">{{ $user->created_at?->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            @if($user->is_locked)
                                <form method="POST" action="{{ route('users.unlock', $user->id) }}"
                                      onsubmit="return confirm('Buka kunci akun {{ $user->name }}?')">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-100 rounded-xl transition-all shadow-sm" title="Buka Kunci Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-100 rounded-xl transition-all shadow-sm" title="Edit">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>   
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                  onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-100 text-slate-500 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 rounded-xl transition-all shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-slate-500">
                        <div class="text-4xl mb-3">👥</div>
                        <p class="font-semibold text-slate-800">Belum ada pengguna.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">{{ $users->links() }}</div>
    @endif
</div>
@endsection
