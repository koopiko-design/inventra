@extends('layouts.app')
@section('title', 'Edit Barang - INVENTRA Admin')

@section('content')
<div style="max-width:640px; margin:0 auto;">
  <div style="margin-bottom:20px;">
    <a href="{{ route('admin.tools.index') }}" style="font-size:12px; color:var(--primary); text-decoration:none;">← Kembali ke Kelola Barang</a>
    <h1 style="font-size:20px; font-weight:800; color:#334155; margin-top:6px;">EDIT BARANG INVENTARIS</h1>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">{{ $tool->nama_alat }}</span>
      <code style="font-size:11px; background:#F1F5F9; padding:3px 8px; border-radius:5px; color:var(--primary);">{{ $tool->kode_alat }}</code>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.tools.update', $tool) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kode Alat <span style="color:red">*</span></label>
            <input type="text" name="kode_alat" class="form-control" value="{{ old('kode_alat', $tool->kode_alat) }}" required style="text-transform:uppercase;">
          </div>
          <div class="form-group">
            <label class="form-label">Kategori <span style="color:red">*</span></label>
            <select name="category_id" class="form-control" required>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $tool->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Nama Barang / Alat <span style="color:red">*</span></label>
          <input type="text" name="nama_alat" class="form-control" value="{{ old('nama_alat', $tool->nama_alat) }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi Barang</label>
          <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $tool->deskripsi) }}</textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Foto / Gambar Barang</label>
          @if($tool->gambar_url)
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; padding:12px; background:#F8FAFC; border:1px solid var(--border); border-radius:10px;">
              <img src="{{ $tool->gambar_url }}" alt="{{ $tool->nama_alat }}" style="height:70px; width:70px; object-fit:cover; border-radius:8px; border:1px solid #CBD5E1;">
              <div>
                <div style="font-size:12.5px; font-weight:600; color:#334155;">Foto Saat Ini</div>
                <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#EF4444; margin-top:4px; cursor:pointer;">
                  <input type="checkbox" name="hapus_gambar" value="1">
                  Hapus foto ini (gunakan placeholder default)
                </label>
              </div>
            </div>
          @endif
          <input type="file" name="gambar" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" onchange="previewEditGambar(this)">
          <div class="form-hint">Pilih file baru jika ingin mengganti foto (Maks. 2MB). Foto akan tampil di halaman katalog.</div>
          <div id="preview-edit-wrap" style="display:none; margin-top:10px;">
            <div style="font-size:12px; font-weight:600; color:#059669; margin-bottom:4px;">Preview Foto Baru:</div>
            <img id="preview-edit-img" src="" alt="Preview Gambar Baru" style="max-height:140px; border-radius:8px; border:1px solid var(--border); object-fit:contain; background:#F8FAFC; padding:4px;">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Jumlah Stok <span style="color:red">*</span></label>
          <input type="number" name="stok" class="form-control" min="0" value="{{ old('stok', $tool->stok) }}" required>
          <div class="form-hint">Status barang otomatis dihitung dari jumlah stok.</div>
        </div>

        <div style="display:flex; gap:12px; margin-top:8px;">
          <button type="submit" class="btn btn-primary" style="flex:1;">Simpan Perubahan</button>
          <a href="{{ route('admin.tools.index') }}" class="btn btn-outline" style="flex:1; text-align:center;">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function previewEditGambar(input) {
    const wrap = document.getElementById('preview-edit-wrap');
    const img = document.getElementById('preview-edit-img');
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
@endsection
