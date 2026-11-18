@extends('layouts.app')

@section('title', 'Pendaftaran Siswa Baru - INVENTRA SMK Negeri 5 Surakarta')

@section('styles')
<style>
  .auth-card {
    max-width: 520px;
    margin: 24px auto;
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 34px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.05);
  }

  .auth-header {
    text-align: center;
    margin-bottom: 22px;
  }

  .auth-badge {
    display: inline-block;
    background: #ECFDF5;
    color: var(--primary);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    text-transform: uppercase;
    border: 1px solid #A7F3D0;
  }

  .auth-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--secondary);
  }

  .auth-subtitle {
    font-size: 13px;
    color: var(--muted);
    margin-top: 4px;
  }

  .info-callout {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: 8px;
    padding: 11px 14px;
    font-size: 12px;
    color: #166534;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
</style>
@endsection

@section('content')
<div class="auth-card">
  <div class="auth-header">
    <div class="auth-badge">Registrasi Akun Siswa</div>
    <h2 class="auth-title">DAFTAR AKUN INVENTRA</h2>
    <p class="auth-subtitle">Sistem Peminjaman Inventaris Praktikum SMK Negeri 5 Surakarta</p>
  </div>

  <div class="info-callout">
    <div>Akun baru otomatis didaftarkan sebagai peran <strong>Siswa</strong> untuk keperluan peminjaman alat praktik sekolah.</div>
  </div>

  <form action="{{ route('register.process') }}" method="POST">
    @csrf

    <div class="form-group">
      <label class="form-label">Nama Lengkap Siswa <span style="color:red">*</span></label>
      <input type="text" name="name" value="{{ old('name') }}" required autofocus class="form-control" placeholder="Contoh: Muhammad Fikri">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Nomor Induk Siswa (NIS) <span style="color:red">*</span></label>
        <input type="text" name="nis" value="{{ old('nis') }}" required class="form-control" placeholder="Contoh: 2024005">
      </div>

      <div class="form-group">
        <label class="form-label">Email (Opsional)</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@email.com">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Kata Sandi <span style="color:red">*</span></label>
        <input type="password" name="password" required class="form-control" placeholder="Minimal 6 karakter">
      </div>

      <div class="form-group">
        <label class="form-label">Konfirmasi Sandi <span style="color:red">*</span></label>
        <input type="password" name="password_confirmation" required class="form-control" placeholder="Ulangi kata sandi">
      </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 14px; margin-top: 6px;">
      Daftar Sebagai Siswa
    </button>

    <div style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--muted);">
      Sudah memiliki akun? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Masuk di sini</a>
    </div>
  </form>
</div>
@endsection
