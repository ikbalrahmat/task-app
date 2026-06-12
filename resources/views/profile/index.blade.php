@extends('layouts.app')
@section('title', 'Profil Saya')
@section('heading', 'Profil Saya')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- User Profile Data (Left Column) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden sticky top-24">
                <div class="p-8 text-center border-b border-slate-100">
                    <div class="w-24 h-24 mx-auto rounded-3xl bg-gradient-to-br from-blue-900 to-blue-600 flex items-center justify-center text-4xl font-bold text-white shadow-lg shadow-blue-900/20 mb-4">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $user->email }}</p>
                    <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        {{ $user->role }}
                    </div>
                </div>
                <div class="p-6 bg-slate-50/50">
                    <p class="text-xs text-slate-400 text-center">Bergabung sejak {{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Edit Profile & Change Password (Right Column) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Edit Profile Form --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Ubah Profil
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Perbarui informasi profil dasar Anda di sini.</p>
                </div>
                <div class="p-8">
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6 max-w-md">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400">
                            @error('name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400">
                            @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Departemen</label>
                            <input type="text" name="department" value="{{ old('department', $user->department) }}" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400"
                                   placeholder="Contoh: IT, Keuangan, dll">
                            @error('department') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-8 py-3 rounded-xl transition-all shadow-md shadow-blue-600/20 hover:shadow-lg hover:-translate-y-0.5">
                                Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Ganti Password
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Pastikan akun Anda menggunakan password yang kuat dan unik.</p>
                </div>
                <div class="p-8">
                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-6 max-w-md">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Password Saat Ini</label>
                            <input type="password" name="current_password" required 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400"
                                   placeholder="Masukkan password lama">
                            @error('current_password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Password Baru</label>
                            <input type="password" name="password" required 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400"
                                   placeholder="Minimal 8 karakter unik">
                            @error('password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 mt-3">
                                <ul class="text-[10px] text-slate-600 space-y-1.5 list-disc list-inside font-medium">
                                    <li>Minimal 8 karakter</li>
                                    <li>Mengandung huruf besar & kecil</li>
                                    <li>Mengandung angka & simbol khusus</li>
                                    <li>Tidak boleh sama dengan 3 password terakhir</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" required 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400"
                                   placeholder="Ulangi password baru">
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-8 py-3 rounded-xl transition-all shadow-md shadow-blue-600/20 hover:shadow-lg hover:-translate-y-0.5">
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
