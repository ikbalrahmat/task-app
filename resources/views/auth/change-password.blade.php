@extends('layouts.app')
@section('title', 'Ganti Password')
@section('heading', 'Ganti Password')
@section('subheading', 'Perbarui password akun Anda')

@section('content')

{{-- Warning banner jika diredirect paksa oleh middleware --}}
@if(session('warning'))
    <div class="mb-6 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl px-5 py-4">
        <span class="text-xl mt-0.5">⚠️</span>
        <div>
            <div class="font-semibold text-sm mb-0.5">Perhatian</div>
            <p class="text-sm">{{ session('warning') }}</p>
        </div>
    </div>
@endif

<div class="max-w-lg">

    {{-- Error alerts --}}
    @if($errors->any())
        <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4">
            <span class="text-xl mt-0.5">❌</span>
            <div class="text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">

        <form method="POST" action="{{ route('change-password.post') }}" class="space-y-6">
            @csrf

            {{-- Current Password --}}
            <div>
                <label for="current_password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">
                    Password Saat Ini
                </label>
                <input type="password" id="current_password" name="current_password" required
                       placeholder="Masukkan password saat ini"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm
                              {{ $errors->has('current_password') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-slate-50' }}
                              focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
                @error('current_password')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">⚠ {{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">
                    Password Baru
                </label>
                <input type="password" id="password" name="password" required
                       placeholder="Masukkan password baru"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm
                              {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-slate-200 bg-slate-50' }}
                              focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
                <p class="mt-2 text-xs text-slate-400 leading-relaxed">
                    Minimal 8 karakter — harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus (@#$!% dll).
                </p>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">⚠ {{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm New Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">
                    Konfirmasi Password Baru
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       placeholder="Ulangi password baru"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm
                              focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-all hover:-translate-y-0.5 shadow-sm hover:shadow-md">
                    💾 Simpan Password Baru
                </button>
                <a href="{{ route('dashboard') }}"
                   class="text-slate-500 hover:text-slate-800 text-sm font-medium px-4 py-2.5 rounded-xl hover:bg-slate-100 transition-colors">
                    ← Kembali
                </a>
            </div>

        </form>

    </div>

    {{-- Password requirements info card --}}
    <div class="mt-5 bg-blue-50 border border-blue-100 rounded-2xl p-5">
        <p class="text-xs font-semibold text-blue-700 mb-3 uppercase tracking-wide">Kriteria Password Kuat</p>
        <ul class="space-y-1.5 text-xs text-blue-600">
            <li class="flex items-center gap-2"><span>✓</span> Minimal 8 karakter</li>
            <li class="flex items-center gap-2"><span>✓</span> Mengandung huruf BESAR (A-Z)</li>
            <li class="flex items-center gap-2"><span>✓</span> Mengandung huruf kecil (a-z)</li>
            <li class="flex items-center gap-2"><span>✓</span> Mengandung angka (0-9)</li>
            <li class="flex items-center gap-2"><span>✓</span> Mengandung karakter khusus (!@#$%^&*)</li>
            <li class="flex items-center gap-2"><span>✓</span> Tidak boleh sama dengan 3 password terakhir</li>
        </ul>
    </div>

</div>

@endsection
