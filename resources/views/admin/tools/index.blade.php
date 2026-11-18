@extends('layouts.app')
@section('title', 'Kelola Katalog Barang - INVENTRA Admin')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:20px; font-weight:800; color:#334155;">KELOLA KATALOG BARANG INVENTARIS</h1>
    <p style="font-size:12px; color:#64748B;">Tambah, ubah, dan hapus data barang inventaris praktikum sekolah.</p>
  </div>
  <button onclick="openModal('modal-tambah')" class="btn btn-primary">
    ＋ Tambah Barang Baru
  </button>
</div>

{{-- Filter Pencarian --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-body" style="padding:14px 18px;">
    <form method="GET" action="{{ route('admin.tools.index') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="max-width:320px;" placeholder="Cari nama atau kode barang...">
      <button type="submit" class="btn btn-secondary btn-sm">🔍 Cari</button>
      @if(request('search'))
        <a href="{{ route('admin.tools.index') }}" class="btn btn-outline btn-sm">Reset</a>
      @endif
    </form>
  </div>
</div>

{{-- Tabel Katalog Barang --}}
<div class="card">
  <div class="card-header">
    <span class="card-title">Daftar Barang Inventaris ({{ $tools->total() }} item)</span>
    <span style="font-size:11px; color:#64748B;">Halaman {{ $tools->currentPage() }} dari {{ $tools->lastPage() }}</span>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Kode Alat</th>
          <th>Nama Barang / Alat</th>
          <th>Kategori</th>
          <th>Stok</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tools as $tool)
        <tr>
          <td>
            <code style="background:#F1F5F9; padding:2px 7px; border-radius:5px; font-size:11px; font-weight:700; color:var(--primary);">
              {{ $tool->kode_alat }}
            </code>
          </td>
          <td>
            <div style="display:flex; align-items:center; gap:10px;">
              @if($tool->gambar_url)
                <img src="{{ $tool->gambar_url }}" alt="{{ $tool->nama_alat }}" style="width:42px; height:42px; object-fit:cover; border-radius:8px; border:1px solid #E2E8F0; flex-shrink:0;">
              @else
                <div style="width:42px; height:42px; border-radius:8px; background:#F1F5F9; border:1px solid #E2E8F0; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">📦</div>
              @endif
              <div>
                <div style="font-weight:600;">{{ $tool->nama_alat }}</div>
                @if($tool->deskripsi)
                  <div style="font-size:11px; color:#64748B; white-space:normal; max-width:280px;">{{ Str::limit($tool->deskripsi, 55) }}</div>
                @endif
              </div>
            </div>
          </td>
          <td style="white-space:normal;">{{ $tool->category->nama_kategori ?? '—' }}</td>
          <td>
            <strong style="font-size:15px; color:{{ $tool->stok > 0 ? '#059669' : '#EF4444' }};">{{ $tool->stok }}</strong>
            <span style="font-size:11px; color:#94A3B8;"> unit</span>
          </td>
          <td>
            @if($tool->stok > 0)
              <span class="badge badge-disetujui">Tersedia</span>
            @else
              <span class="badge badge-ditolak">Habis</span>
            @endif
          </td>
          <td>
            <div style="display:flex; gap:6px;">
              {{-- Edit --}}
              <a href="{{ route('admin.tools.edit', $tool) }}" class="btn btn-xs btn-secondary">✏️ Edit</a>
              {{-- Hapus --}}
              <form method="POST" action="{{ route('admin.tools.destroy', $tool) }}"
                    data-confirm="Hapus barang '{{ $tool->nama_alat }}'? Pastikan tidak ada transaksi peminjaman aktif untuk barang ini."
                    data-confirm-title="Hapus Barang Inventaris?"
                    data-confirm-type="danger"
                    data-confirm-btn="🗑️ Ya, Hapus">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-xs btn-danger">🗑️ Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center; padding:40px; color:#94A3B8;">
            Belum ada barang inventaris. Tambahkan barang pertama.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($tools->hasPages())
    <div style="padding:14px 18px; border-top:1px solid var(--border);">
      {{ $tools->links() }}
    </div>
  @endif
</div>

{{-- ====== MODAL TAMBAH BARANG ====== --}}
<div class="modal-overlay" id="modal-tambah">
  <div class="modal-box">
    <h2 class="modal-title">＋ Tambah Katalog Barang Baru</h2>
    <form method="POST" action="{{ route('admin.tools.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kode Alat <span style="color:red">*</span></label>
          <input type="text" name="kode_alat" class="form-control" placeholder="Contoh: DSLR-02" required value="{{ old('kode_alat') }}" style="text-transform:uppercase;">
        </div>
        <div class="form-group">
          <label class="form-label">Kategori <span style="color:red">*</span></label>
          <select name="category_id" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Nama Barang / Alat <span style="color:red">*</span></label>
        <input type="text" name="nama_alat" class="form-control" placeholder="Contoh: Kamera Mirrorless Sony A6400" required value="{{ old('nama_alat') }}">
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi Barang</label>
        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Spesifikasi, kelengkapan, kondisi barang...">{{ old('deskripsi') }}</textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Foto / Gambar Barang</label>
        <input type="file" name="gambar" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" onchange="previewTambahGambar(this)">
        <div class="form-hint">Format: JPG, JPEG, PNG, WEBP, GIF (Maks. 2MB). Foto akan tampil di katalog siswa.</div>
        <div id="preview-tambah-wrap" style="display:none; margin-top:8px;">
          <img id="preview-tambah-img" src="" alt="Preview Gambar" style="max-height:120px; border-radius:8px; border:1px solid var(--border); object-fit:contain; background:#F8FAFC; padding:4px;">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Jumlah Stok Awal <span style="color:red">*</span></label>
        <input type="number" name="stok" class="form-control" min="0" value="{{ old('stok', 1) }}" required>
        <div class="form-hint">Status barang otomatis: Tersedia (stok > 0) atau Habis (stok = 0)</div>
      </div>

      <div class="modal-actions">
        <button type="button" onclick="closeModal('modal-tambah')" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Barang</button>
      </div>
    </form>
  </div>
</div>

<script>
  function previewTambahGambar(input) {
    const wrap = document.getElementById('preview-tambah-wrap');
    const img = document.getElementById('preview-tambah-img');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        img.src = e.target.result;
        wrap.style.display = 'block';
      }
      reader.readAsDataURL(input.files[0]);
    } else {
      wrap.style.display = 'none';
    }
  }
</script>

{{-- Auto-open modal jika ada error validasi --}}
@if(isset($errors) && $errors->any())
<script>openModal('modal-tambah');</script>
@endif
@endsection
