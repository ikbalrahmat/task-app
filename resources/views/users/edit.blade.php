@extends('layouts.app')
@section('title', 'Edit Pengguna')
@section('heading', 'Edit Pengguna')
@section('subheading', 'Perbarui informasi pengguna')

@section('content')
<div class="max-w-lg">
    <div class="bg-[#1a1d27] border border-[#333650] rounded-2xl p-8">
        <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full bg-[#222535] border @error('name') border-red-500 @else border-[#333650] @enderror text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                @error('name')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email <span class="text-red-400">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full bg-[#222535] border @error('email') border-red-500 @else border-[#333650] @enderror text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                @error('email')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Role <span class="text-red-400">*</span></label>
                    <select name="role" required class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                        @foreach(['Admin','Pengendali Teknis','Ketua Tim','Anggota Tim'] as $r)
                            <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Departemen</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}"
                           class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="bg-amber-950/20 border border-amber-900/30 rounded-xl p-4 text-sm text-amber-400">
                💡 Kosongkan password jika tidak ingin mengubahnya.
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Password Baru</label>
                <input type="password" name="password"
                       class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
                       placeholder="Biarkan kosong jika tidak diubah">
                @error('password')<p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full bg-[#222535] border border-[#333650] text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-all hover:-translate-y-0.5">
                    Perbarui Pengguna
                </button>
                <a href="{{ route('users.index') }}" class="bg-[#222535] border border-[#333650] text-slate-300 hover:text-white px-6 py-3 rounded-xl text-sm transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
