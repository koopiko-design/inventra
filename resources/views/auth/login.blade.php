@extends('layouts.app')

@section('title', 'Masuk Akun - INVENTRA SMK Negeri 5 Surakarta')

@section('styles')
<style>
  .auth-card {
    max-width: 480px;
    margin: 30px auto;
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 34px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.05);
  }

  .auth-header {
    text-align: center;
    margin-bottom: 24px;
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
    letter-spacing: -0.5px;
  }

  .auth-subtitle {
    font-size: 13px;
    color: var(--muted);
    margin-top: 4px;
  }

  .role-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 18px;
  }

  .role-option {
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #F8FAFC;
  }

  .role-option:hover {
    border-color: var(--primary);
    background: #ECFDF5;
  }

  .role-option.active {
    border-color: var(--primary);
    background: #ECFDF5;
    color: var(--primary);
    font-weight: 700;
    box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.2);
  }

  .role-option input {
    display: none;
  }

  .demo-hint-box {
    margin-top: 24px;
    background: #F8FAFC;
    border: 1px dashed var(--border);
    border-radius: 10px;
    padding: 14px;
    font-size: 11px;
    color: #475569;
  }

  .demo-hint-title {
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 6px;
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 0.5px;
  }

  .demo-account-item {
    display: flex;
    justify-content: space-between;
    padding: 4px 0;
    border-bottom: 1px solid var(--border);
  }
  .demo-account-item:last-child { border-bottom: none; }
</style>
@endsection

@section('content')
<div class="auth-card">
  <div class="auth-header">
    <div class="auth-badge">Autentikasi Pengguna</div>
    <h2 class="auth-title">MASUK KE INVENTRA</h2>
    <p class="auth-subtitle">Sistem Peminjaman Inventaris SMK Negeri 5 Surakarta</p>
  </div>

  <form action="{{ route('login.process') }}" method="POST">
    @csrf

    <!-- Pilihan Role (Admin / Siswa) -->
    <div class="form-group">
      <label class="form-label">Masuk Sebagai (Role)</label>
      <div class="role-selector">
        <label class="role-option" id="role-siswa-label" onclick="selectRole('siswa')">
          <input type="radio" name="role" value="siswa" id="role-siswa" {{ old('role', 'siswa') === 'siswa' ? 'checked' : '' }}>
          <div style="font-size: 13px;">Siswa / Peminjam</div>
        </label>

        <label class="role-option" id="role-admin-label" onclick="selectRole('admin')">
          <input type="radio" name="role" value="admin" id="role-admin" {{ old('role') === 'admin' ? 'checked' : '' }}>
          <div style="font-size: 13px;">Admin / Petugas</div>
        </label>
      </div>
    </div>

    <!-- Input Username / NIS -->
    <div class="form-group">
      <label class="form-label" id="username-label">NIS / Username / Email</label>
      <input type="text" name="username" value="{{ old('username') }}" required autofocus class="form-control" placeholder="Contoh: 2024001 atau admin05">
    </div>

    <!-- Input Password -->
    <div class="form-group">
      <label class="form-label">Kata Sandi</label>
      <input type="password" name="password" required class="form-control" placeholder="Masukkan kata sandi">
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 12px;">
      <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: var(--muted);">
        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Ingat sesi saya
      </label>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 14px;">
      Masuk ke Aplikasi
    </button>

    <div style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--muted);">
      Belum memiliki akun siswa? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">Daftar Akun Baru</a>
    </div>
  </form>

  
@endsection

@section('scripts')
<script>
  function selectRole(role) {
    const siswaLabel = document.getElementById('role-siswa-label');
    const adminLabel = document.getElementById('role-admin-label');
    const siswaRadio = document.getElementById('role-siswa');
    const adminRadio = document.getElementById('role-admin');
    const userLabel = document.getElementById('username-label');

    if (role === 'admin') {
      adminRadio.checked = true;
      adminLabel.classList.add('active');
      siswaLabel.classList.remove('active');
      userLabel.innerText = "Username Admin / Email (admin05)";
    } else {
      siswaRadio.checked = true;
      siswaLabel.classList.add('active');
      adminLabel.classList.remove('active');
      userLabel.innerText = "Nomor Induk Siswa (NIS) / Email";
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    const isRoleAdmin = document.getElementById('role-admin').checked;
    selectRole(isRoleAdmin ? 'admin' : 'siswa');
  });
</script>
@endsection
