@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')

<div class="form-header">
    <h2>Masuk Portal</h2>
    <p>Masukkan kredensial Anda untuk melanjutkan.</p>
</div>

{{-- Security Warning Banner (Requirement 5) --}}
<div class="alert alert-error" style="background: #fffbeb; border: 1px solid #fef3c7; color: #b45309; padding: 14px; border-radius: 10px; font-size: 12px; margin-bottom: 24px; line-height: 1.6; display: block;">
    <div style="font-weight: 700; margin-bottom: 4px; font-size: 13px;">⚠️ PERINGATAN AKSES TERBATAS:</div>
    Hanya pengguna yang berwenang yang dapat mengakses sistem ini. Semua aktivitas masuk dan operasional sistem dicatat dan dipantau untuk audit keamanan.
</div>

@if(session('success'))
    <div class="alert alert-success">
        <div class="alert-icon">✓</div>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        <div class="alert-icon">!</div>
        <div>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Email --}}
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
               placeholder="name@example.com"
               class="@error('email') error @enderror">
        @error('email')
            <div class="error-message">
                <span>⚠</span> {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Password --}}
    <div class="form-group">
        <label for="password">Password</label>
        <div x-data="{ show: false }" class="relative">
            <input :type="show ? 'text' : 'password'" id="password" name="password" required 
                   class="@error('password') error @enderror w-full pr-12" 
                   placeholder="Masukkan password">
            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
        </div>
        @error('password')
            <div class="error-message">
                <span>⚠</span> {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Google reCAPTCHA v2 (Requirement 3) --}}
    <div class="form-group">
        <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
        @error('g-recaptcha-response')
            <div class="error-message">
                <span>⚠</span> {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Remember Me & Forgot Password --}}
    <div class="form-footer">
        <div class="checkbox-group">
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Ingat saya</label>
        </div>
        <a href="{{ route('password.request') }}" class="forgot-password">Lupa password?</a>
    </div>

    {{-- Submit Button --}}
    <button type="submit" class="submit-btn">Masuk</button>

</form>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

@endsection
