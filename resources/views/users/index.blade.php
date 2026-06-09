@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('heading', 'Pengguna')
@section('subheading', 'Kelola akun dan hak akses pengguna')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex items-center gap-3">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau email..."
               class="bg-[#1a1d27] border border-[#333650] text-white placeholder-slate-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 min-w-[220px]">
        <select name="role" class="bg-[#1a1d27] border border-[#333650] text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500">
            <option value="">Semua Role</option>
            @foreach(['Admin','Pengendali Teknis','Ketua Tim','Anggota Tim'] as $r)
                <option value="{{ $r }}" {{ ($filters['role'] ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-[#222535] border border-[#333650] text-white px-4 py-2.5 rounded-xl text-sm hover:bg-[#2a2e42] transition-colors">Filter</button>
    </form>
    <a href="{{ route('users.create') }}"
       class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 shrink-0">
        + Tambah Pengguna
    </a>
</div>

<div class="bg-[#1a1d27] border border-[#333650] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#222535] text-slate-400 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 text-left font-semibold">Pengguna</th>
                    <th class="px-6 py-4 text-left font-semibold">Email</th>
                    <th class="px-6 py-4 text-left font-semibold">Role</th>
                    <th class="px-6 py-4 text-left font-semibold">Departemen</th>
                    <th class="px-6 py-4 text-left font-semibold">Bergabung</th>
                    <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#333650]">
                @forelse($users as $user)
                <tr class="hover:bg-[#222535] transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shrink-0
                                {{ $user->role === 'Admin' ? 'bg-purple-950 text-purple-400' :
                                   ($user->role === 'Pengendali Teknis' ? 'bg-blue-950 text-blue-400' :
                                   ($user->role === 'Ketua Tim' ? 'bg-green-950 text-green-400' : 'bg-[#222535] text-slate-400')) }}">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-white">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <span class="text-[10px] text-blue-400">(Anda)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-400">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @php
                            $rc = match($user->role) {
                                'Admin'             => 'bg-purple-950 text-purple-400 border-purple-900/50',
                                'Pengendali Teknis' => 'bg-blue-950 text-blue-400 border-blue-900/50',
                                'Ketua Tim'         => 'bg-green-950 text-green-400 border-green-900/50',
                                'Anggota Tim'       => 'bg-[#222535] text-slate-400 border-[#333650]',
                                default   => 'bg-[#222535] text-slate-400',
                            };
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold border {{ $rc }}">{{ $user->role }}</span>
                    </td>
                    <td class="px-6 py-4 text-slate-400 text-sm">{{ $user->department ?? '-' }}</td>
                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $user->created_at?->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="p-2 text-slate-400 hover:text-amber-400 hover:bg-amber-950/30 rounded-lg transition-colors" title="Edit">✏️</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                                  onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-950/30 rounded-lg transition-colors" title="Hapus">🗑</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-slate-400">
                        <div class="text-4xl mb-3">👥</div>
                        <p class="font-semibold text-white">Belum ada pengguna.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-[#333650]">{{ $users->links() }}</div>
    @endif
</div>
@endsection
