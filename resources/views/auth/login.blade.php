@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')

<div class="form-header">
    <h2>Masuk</h2>
    <p>Selamat datang kembali. Masukkan kredensial Anda.</p>
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
               placeholder="admin@example.com"
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
