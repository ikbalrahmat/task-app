@extends('layouts.guest')
@section('title', 'Perbarui Password')

@section('content')

<div class="form-header">
    <h2>Perbarui Password</h2>
    <p>Silakan ganti password Anda untuk dapat terus menggunakan sistem.</p>
</div>

@if(session('warning'))
    <div class="alert alert-error" style="background: #fff9db; border: 1px solid #ffe066; color: #f59f00; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">
        <div class="alert-icon" style="font-weight: bold; margin-bottom: 2px;">⚠ PERINGATAN:</div>
        {{ session('warning') }}
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

<form method="POST" action="{{ route('change-password.post') }}">
    @csrf

    {{-- Current Password --}}
    <div class="form-group">
        <label for="current_password">Password Saat Ini</label>
        <input type="password" id="current_password" name="current_password" required
               placeholder="Masukkan password saat ini"
               class="@error('current_password') error @enderror">
        @error('current_password')
            <div class="error-message">
                <span>⚠</span> {{ $message }}
            </div>
        @enderror
    </div>

    {{-- New Password --}}
    <div class="form-group">
        <label for="password">Password Baru</label>
        <input type="password" id="password" name="password" required
               placeholder="Masukkan password baru"
               class="@error('password') error @enderror">
        <p style="font-size: 11px; color: #64748b; margin-top: 4px; line-height: 1.4;">
            Kriteria: Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.
        </p>
        @error('password')
            <div class="error-message">
                <span>⚠</span> {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Confirm New Password --}}
    <div class="form-group">
        <label for="password_confirmation">Konfirmasi Password Baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
               placeholder="Ulangi password baru">
    </div>

    {{-- Submit Button --}}
    <button type="submit" class="submit-btn">Perbarui Password</button>

</form>

<form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
    @csrf
</form>

<div class="back-link">
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        ← Kembali / Keluar
    </a>
</div>

@endsection
