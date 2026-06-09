@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')

<div class="form-header">
    <h2>Reset Password</h2>
    <p>Masukkan email Anda dan kami akan mengirim link untuk mereset password.</p>
</div>

@if(session('status'))
    <div class="alert alert-success">
        <div class="alert-icon">✓</div>
        <div>
            <strong>Email Terkirim!</strong><br>
            Silakan cek email Anda untuk link reset password. Link berlaku selama 60 menit.
        </div>
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

<form method="POST" action="{{ route('password.email') }}">
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

    {{-- Submit Button --}}
    <button type="submit" class="submit-btn">Kirim Link Reset</button>

</form>

{{-- Back to Login --}}
<div class="back-link">
    <a href="{{ route('login') }}">
        <span>←</span> Kembali ke Halaman Login
    </a>
</div>

@endsection
