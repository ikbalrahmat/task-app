@extends('layouts.guest')
@section('title', 'Lupa Password')

@section('content')
<h2 class="text-xl font-bold text-slate-800 mb-2">Lupa Password? 🔑</h2>
<p class="text-slate-500 text-sm mb-8">Masukkan email Anda dan kami akan mengirim link reset password.</p>

@if(session('info'))
    <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded-xl px-4 py-3 mb-6 text-sm">
        {{ session('info') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               placeholder="nama@perusahaan.com"
               class="w-full bg-white border @error('email') border-red-500 @else border-slate-200 @enderror text-slate-900 placeholder-slate-400 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
        @error('email')
            <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-all duration-200">
        Kirim Link Reset
    </button>
</form>

<div class="text-center mt-6">
    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-700 transition-colors">← Kembali ke halaman login</a>
</div>
@endsection
