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
        <a href="{{ route('users.logs') }}"
           class="bg-slate-700 hover:bg-slate-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 shrink-0 shadow-sm hover:shadow-md">
            📋 Log Aktivitas Audit
        </a>
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
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                    <th class="px-6 py-4 text-left font-semibold">Pengguna</th>
                    <th class="px-6 py-4 text-left font-semibold">Email</th>
                    <th class="px-6 py-4 text-left font-semibold">Role</th>
                    <th class="px-6 py-4 text-left font-semibold">Status</th>
                    <th class="px-6 py-4 text-left font-semibold">Departemen</th>
                    <th class="px-6 py-4 text-left font-semibold">Bergabung</th>
                    <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition-colors">
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
                    <td class="px-6 py-4">
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
                    <td class="px-6 py-4">
                        @if($user->is_locked)
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold border bg-red-50 text-red-600 border-red-100">🔒 Terkunci</span>
                        @else
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold border bg-emerald-50 text-emerald-600 border-emerald-100">🔓 Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-500 text-sm">{{ $user->department ?? '-' }}</td>
                    <td class="px-6 py-4 text-slate-500 text-xs">{{ $user->created_at?->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1">
                            @if($user->is_locked)
                                <form method="POST" action="{{ route('users.unlock', $user->id) }}"
                                      onsubmit="return confirm('Buka kunci akun {{ $user->name }}?')">
                                    @csrf
                                    <button type="submit" class="p-2 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors" title="Buka Kunci Akun">🔓 Buka Kunci</button>
                                </form>
                            @endif
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">✏️</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                  onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">🗑</button>
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
