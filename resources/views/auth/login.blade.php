@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<h2 class="text-xl font-bold text-white mb-2">Selamat Datang 👋</h2>
<p class="text-slate-400 text-sm mb-8">Masuk ke akun TaskFlow Anda</p>

@if(session('success'))
    <div class="bg-green-950 border border-green-700 text-green-400 rounded-xl px-4 py-3 mb-6 text-sm">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               placeholder="nama@perusahaan.com"
               class="w-full bg-[#222535] border @error('email') border-red-500 @else border-[#333650] @enderror text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors">
        @error('email')
            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-slate-300">Password</label>
            <a href="{{ route('password.request') }}" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">Lupa password?</a>
        </div>
        <input type="password" name="password" required
               placeholder="Masukkan password"
               class="w-full bg-[#222535] border border-[#333650] text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-colors">
        @error('password')
            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-blue-500">
        <label for="remember" class="text-sm text-slate-400">Ingat saya</label>
    </div>

    <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-900/50">
        Masuk
    </button>
</form>

<p class="text-center text-xs text-slate-400 mt-8">
    TaskFlow &copy; {{ date('Y') }} — Sistem Manajemen Project
</p>
@endsection
