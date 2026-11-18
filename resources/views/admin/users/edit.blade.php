@extends('layouts.app')
@section('title', 'Edit Pengguna - INVENTRA Admin')

@section('content')
<div style="max-width:580px; margin:0 auto;">
  <div style="margin-bottom:20px;">
    <a href="{{ route('admin.users.index') }}" style="font-size:12px; color:var(--primary); text-decoration:none;">← Kembali ke Manajemen User</a>
    <h1 style="font-size:20px; font-weight:800; color:#334155; margin-top:6px;">EDIT PENGGUNA</h1>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">{{ $user->name }}</span>
      <span class="badge {{ $user->role === 'admin' ? '' : 'badge-green' }}" style="{{ $user->role === 'admin' ? 'background:#FDE047;color:#713F12;' : '' }}">{{ strtoupper($user->role) }}</span>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')

        <div class="form-group">
          <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">NIS / Username</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis', $user->nis) }}" placeholder="Kosongkan jika tidak ada">
          </div>
          <div class="form-group">
            <label class="form-label">Role <span style="color:red">*</span></label>
            <select name="role" class="form-control" required {{ $user->id === Auth::id() ? 'disabled' : '' }}>
              <option value="siswa" {{ old('role', $user->role) === 'siswa' ? 'selected' : '' }}>Siswa</option>
              <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas</option>
              <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @if($user->id === Auth::id())
              <input type="hidden" name="role" value="{{ $user->role }}">
              <div class="form-hint">Role akun Anda sendiri tidak dapat diubah.</div>
            @endif
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email <span style="color:red">*</span></label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Kata Sandi Baru <span style="font-weight:normal; text-transform:none; font-size:11px;">(kosongkan jika tidak diubah)</span></label>
          <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter...">
        </div>

        <div style="display:flex; gap:12px; margin-top:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;">Simpan Perubahan</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-outline" style="flex:1; text-align:center;">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
