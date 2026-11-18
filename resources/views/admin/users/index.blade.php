@extends('layouts.app')
@section('title', 'Manajemen User - INVENTRA Admin')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:20px; font-weight:800; color:#334155;">MANAJEMEN PENGGUNA SISTEM</h1>
    <p style="font-size:12px; color:#64748B;">Kelola akun Admin dan Siswa yang terdaftar di sistem INVENTRA.</p>
  </div>
  <button onclick="openModal('modal-tambah-user')" class="btn btn-primary">
    ＋ Tambah Pengguna
  </button>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:14px 18px;">
    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="max-width:280px;" placeholder="Cari nama, NIS, atau email...">
      <select name="role" class="form-control" style="max-width:160px;">
        <option value="">Semua Role</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
        <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">🔍 Filter</button>
      @if(request('search') || request('role'))
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">Reset</a>
      @endif
    </form>
  </div>
</div>

{{-- Tabel User --}}
<div class="card">
  <div class="card-header">
    <span class="card-title">Daftar Pengguna ({{ $users->total() }} akun)</span>
    <span style="font-size:11px; color:#64748B;">Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</span>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Nama Lengkap</th>
          <th>NIS / Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Terdaftar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width:32px;height:32px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
              <div>
                <div style="font-weight:600;">{{ $user->name }}</div>
                @if($user->id === Auth::id())
                  <span style="font-size:10px; color:var(--primary); font-weight:700;">(Akun Anda)</span>
                @endif
              </div>
            </div>
          </td>
          <td><code style="font-size:12px; background:#F1F5F9; padding:2px 7px; border-radius:5px;">{{ $user->nis ?? '—' }}</code></td>
          <td style="white-space:normal; max-width:200px;">{{ $user->email }}</td>
          <td>
            @if($user->role === 'admin')
              <span class="badge" style="background:#FDE047; color:#713F12;">ADMIN</span>
            @elseif($user->role === 'petugas')
              <span class="badge" style="background:#E0E7FF; color:#3730A3;">PETUGAS</span>
            @else
              <span class="badge badge-green">SISWA</span>
            @endif
          </td>
          <td style="font-size:12px; color:#64748B;">{{ $user->created_at->format('d/m/Y') }}</td>
          <td>
            <div style="display:flex; gap:6px;">
              <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-xs btn-secondary">✏️ Edit</a>
              @if($user->id !== Auth::id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      data-confirm="Hapus akun '{{ $user->name }}'? Akun yang dihapus tidak dapat dipulihkan kembali."
                      data-confirm-title="Hapus Akun Pengguna?"
                      data-confirm-type="danger"
                      data-confirm-btn="🗑️ Ya, Hapus">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-danger">🗑️ Hapus</button>
                </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center; padding:40px; color:#94A3B8;">Tidak ada pengguna ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
    <div style="padding:14px 18px; border-top:1px solid var(--border);">{{ $users->links() }}</div>
  @endif
</div>

{{-- MODAL TAMBAH USER --}}
<div class="modal-overlay" id="modal-tambah-user">
  <div class="modal-box">
    <h2 class="modal-title">＋ Tambah Pengguna Baru</h2>
    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
        <input type="text" name="name" class="form-control" placeholder="Nama lengkap pengguna" required value="{{ old('name') }}">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">NIS / Username</label>
          <input type="text" name="nis" class="form-control" placeholder="Contoh: 2024009" value="{{ old('nis') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Role <span style="color:red">*</span></label>
          <select name="role" class="form-control" required>
            <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
            <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Email <span style="color:red">*</span></label>
        <input type="email" name="email" class="form-control" placeholder="email@example.com" required value="{{ old('email') }}">
      </div>

      <div class="form-group">
        <label class="form-label">Kata Sandi <span style="color:red">*</span></label>
        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
      </div>

      <div class="modal-actions">
        <button type="button" onclick="closeModal('modal-tambah-user')" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
      </div>
    </form>
  </div>
</div>

@if(isset($errors) && $errors->any())
<script>openModal('modal-tambah-user');</script>
@endif
@endsection
