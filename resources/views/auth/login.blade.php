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
        <input type="password" id="password" name="password" required
               placeholder="Masukkan password Anda"
               class="@error('password') error @enderror">
        @error('password')
            <div class="error-message">
                <span>⚠</span> {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Math CAPTCHA (Requirement 3) --}}
    <div class="form-group">
        <label for="captcha">Pertanyaan Keamanan: {{ $num1 }} + {{ $num2 }} = ?</label>
        <input type="text" id="captcha" name="captcha" required autocomplete="off"
               placeholder="Masukkan jawaban angka"
               class="@error('captcha') error @enderror">
        @error('captcha')
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

@endsection
