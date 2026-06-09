@extends('layouts.guest')
@section('title', 'Verifikasi Dua Faktor')

@section('content')

<div class="form-header">
    <h2>Verifikasi Dua Faktor (MFA)</h2>
    <p>Demi keamanan akun Anda, masukkan 6 digit kode OTP yang dikirimkan.</p>
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

<form method="POST" action="{{ route('mfa.verify.post') }}">
    @csrf

    {{-- OTP Code --}}
    <div class="form-group">
        <label for="code">Kode Verifikasi (OTP)</label>
        <input type="text" id="code" name="code" required autofocus autocomplete="off"
               placeholder="Contoh: 123456" pattern="[0-9]{6}" maxlength="6"
               style="text-align: center; font-size: 24px; letter-spacing: 8px;"
               class="@error('code') error @enderror">
        <p style="font-size: 11px; color: #64748b; margin-top: 8px; text-align: center; line-height: 1.4;">
            OTP dikirimkan secara internal. Untuk keperluan pengujian/lokal, silakan cek file log aplikasi Anda di <code>storage/logs/laravel.log</code>.
        </p>
        @error('code')
            <div class="error-message">
                <span>⚠</span> {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Submit Button --}}
    <button type="submit" class="submit-btn">Verifikasi & Masuk</button>

</form>

<form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
    @csrf
</form>

<div class="back-link">
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        ← Batal / Keluar
    </a>
</div>

@endsection
