@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')

<div class="form-header">
    <h2>Reset Password</h2>
    <p>Informasi mengenai pengaturan ulang kata sandi.</p>
</div>

<!-- ============================================================
     FITUR RESET PASSWORD SEMENTARA DINONAKTIFKAN
     Untuk mengaktifkan kembali:
     1. Hapus blok ANNOUNCEMENT di bawah ini
     2. Hapus baris @php /* dan */ @endphp yang mengapit FORM ORIGINAL
     ============================================================ -->

{{-- ANNOUNCEMENT (aktif) --}}
<div style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #93c5fd; border-radius: 14px; padding: 24px 20px; text-align: center; margin-bottom: 20px;">
    <div style="font-size: 2rem; margin-bottom: 10px;">🔒</div>
    <div style="font-weight: 700; color: #1e3a8a; font-size: 15px; margin-bottom: 8px;">
        Fitur Reset Password Sementara Tidak Tersedia
    </div>
    <div style="color: #3b82f6; font-size: 13px; line-height: 1.6;">
        Untuk mengatur ulang password Anda,<br>
        silakan <strong>hubungi Administrator Sistem</strong>.
    </div>
</div>
{{-- END ANNOUNCEMENT --}}


{{-- FORM ORIGINAL (dinonaktifkan sementara) --}}
@php /* @endphp

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
    <button type="submit" class="submit-btn">Kirim Link Reset</button>
</form>

@php */ @endphp
{{-- END FORM ORIGINAL --}}


{{-- Back to Login --}}
<div class="back-link">
    <a href="{{ route('login') }}">
        <span>←</span> Kembali ke Halaman Login
    </a>
</div>

@endsection
