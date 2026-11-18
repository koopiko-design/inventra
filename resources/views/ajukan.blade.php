@extends('layouts.app')

@section('title', 'Form Pengajuan Peminjaman Alat - INVENTRA SMK Negeri 5 Surakarta')

@section('styles')
<style>
  .form-card {
    max-width: 660px;
    margin: 10px auto 30px auto;
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 32px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.05);
  }

  .form-header {
    text-align: center;
    margin-bottom: 24px;
  }

  .form-badge {
    display: inline-block;
    background: #ECFDF5;
    color: var(--primary);
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 8px;
    text-transform: uppercase;
    border: 1px solid #A7F3D0;
  }

  .form-title {
    font-size: 21px;
    font-weight: 800;
    color: var(--secondary);
  }

  .form-subtitle {
    font-size: 13px;
    color: var(--muted);
    margin-top: 4px;
  }

  .selected-tool-panel {
    background: #F0FDF4;
    border: 1.5px solid #BBF7D0;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .tool-tag {
    font-size: 10px;
    font-weight: 700;
    color: #166534;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .tool-name-highlight {
    font-size: 14px;
    font-weight: 700;
    color: #14532D;
    margin-top: 2px;
  }

  .rule-notice {
    background: #F8FAFC;
    border-left: 4px solid var(--primary);
    padding: 10px 14px;
    border-radius: 0 8px 8px 0;
    font-size: 12px;
    color: #475569;
    margin-bottom: 20px;
  }
</style>
@endsection

@section('content')
<div class="form-card">
  <div class="form-header">
    <div class="form-badge">Peminjaman Siswa</div>
    <h1 class="form-title">FORMULIR PENGAJUAN PINJAM ALAT</h1>
    <p class="form-subtitle">SMK Negeri 5 Surakarta • Sarana &amp; Prasarana Praktikum</p>
  </div>

  {{-- Aturan Peminjaman --}}
  <div class="rule-notice">
    <strong>Ketentuan Peminjaman Inventaris:</strong>
    <ul style="margin:4px 0 0 16px; font-size:11.5px;">
      <li>Siswa <strong>hanya dapat meminjam 1 unit</strong> barang dalam setiap pengajuan.</li>
      <li>Silakan <strong>pilih tanggal peminjaman</strong> sesuai jadwal praktik Anda.</li>
      <li>Batas tanggal pengembalian <strong>otomatis dihitung 3 hari kalender</strong> setelah tanggal peminjaman yang Anda pilih.</li>
    </ul>
  </div>

  @guest
    <div class="alert alert-info" style="margin-bottom: 20px;">
      <span>💡</span>
      <div>
        Anda belum login. <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: underline;">Masuk ke Akun Anda</a> agar nama &amp; NIS terisi otomatis.
      </div>
    </div>
  @endguest

  <form action="{{ route('ajukan.store') }}" method="POST">
    @csrf

    <!-- Pemilihan Alat Inventaris -->
    <div class="form-group">
      <label class="form-label">Pilih Alat Inventaris Praktikum <span style="color:red">*</span></label>
      <select name="tool_id" id="tool_id" class="form-control" required onchange="updateSelectedToolInfo()">
        <option value="" disabled {{ !$toolId ? 'selected' : '' }}>-- Pilih alat dari katalog --</option>
        @foreach($tools as $t)
          <option value="{{ $t->id }}" 
                  data-nama="{{ $t->nama_alat }}" 
                  data-kode="{{ $t->kode_alat }}" 
                  data-stok="{{ $t->stok }}"
                  {{ (old('tool_id', $toolId) == $t->id) ? 'selected' : '' }}>
            {{ $t->nama_alat }} (Kode: {{ $t->kode_alat }} | Stok Tersedia: {{ $t->stok }})
          </option>
        @endforeach
      </select>
    </div>

    <!-- Kotak Preview Barang Dipilih -->
    <div class="selected-tool-panel" id="preview-panel">
      <div>
        <div class="tool-tag" id="preview-tag">Barang Terpilih:</div>
        <div class="tool-name-highlight" id="preview-title">
          {{ $selectedTool ? $selectedTool->nama_alat . ' (Stok: ' . $selectedTool->stok . ')' : ($namaAlat ?: 'Silakan tentukan alat pada pilihan di atas') }}
        </div>
      </div>
      <a href="{{ route('katalog') }}" class="btn btn-xs btn-outline">
        🔍 Katalog
      </a>
    </div>

    <!-- Identitas Peminjam -->
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Nama Lengkap Siswa <span style="color:red">*</span></label>
        <input type="text" name="nama_peminjam" 
               value="{{ old('nama_peminjam', Auth::check() ? Auth::user()->name : '') }}" 
               required class="form-control" placeholder="Contoh: Budi Pratama">
      </div>

      <div class="form-group">
        <label class="form-label">Kelas &amp; NIS <span style="color:red">*</span></label>
        <input type="text" name="kelas_nis" 
               value="{{ old('kelas_nis', Auth::check() && Auth::user()->nis ? (Auth::user()->nis) : '') }}" 
               required class="form-control" placeholder="Contoh: XII RPL 1 / 2024001">
      </div>
    </div>

    <!-- Jumlah Unit (Dikunci = 1) & Tanggal Pinjam (Bisa Dipilih) -->
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Jumlah Barang </label>
        <div style="position:relative;">
          <input type="number" class="form-control" value="1" readonly style="background:#F8FAFC; color:#334155; font-weight:700; cursor:not-allowed;">
          <input type="hidden" name="jumlah_unit" value="1">
          <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:11px; color:#059669; font-weight:700;">
            Maks. 1 Unit
          </span>
        </div>
        <div class="form-hint">Ketentuan sistem: Siswa dibatasi 1 barang per pengajuan.</div>
      </div>

      <div class="form-group">
        <label class="form-label">Tanggal Peminjaman <span style="color:red">*</span></label>
        <input type="date" name="tgl_pinjam" id="tgl_pinjam" class="form-control" 
               value="{{ old('tgl_pinjam', $tglPinjam ?? date('Y-m-d')) }}" 
               min="{{ date('Y-m-d') }}"
               required onchange="hitungTanggalKembali()">
        <div class="form-hint">Pilih tanggal rencana pengambilan barang praktikum.</div>
      </div>
    </div>

    <!-- Tanggal Pengembalian Otomatis 3 Hari Setelah Tanggal Pinjam -->
    <div class="form-group">
      <label class="form-label">Rencana Tanggal Pengembalian </label>
      <div style="position:relative;">
        <input type="text" id="tgl_kembali_preview" class="form-control" 
               value="{{ date('d M Y', strtotime($tglKembali ?? now()->addDays(3))) }} (Durasi: 3 Hari)" 
               readonly style="background:#ECFDF5; border-color:#A7F3D0; color:#065F46; font-weight:700; cursor:not-allowed;">
      </div>
      <div class="form-hint" id="keterangan_kembali">
        Otomatis dihitung 3 hari kalender setelah tanggal peminjaman yang Anda pilih.
      </div>
    </div>

    <!-- Alasan / Tujuan Peminjaman -->
    <div class="form-group">
      <label class="form-label">Tujuan &amp; Keperluan Praktikum <span style="color:red">*</span></label>
      <textarea name="alasan_pinjam" rows="3" required class="form-control" 
                placeholder="Contoh: Praktikum Pemrograman Web / Ujian Praktik Kejuruan (UPK) di Lab Komputer 2">{{ old('alasan_pinjam') }}</textarea>
    </div>

    <div style="display: flex; gap: 12px; margin-top: 24px;">
      <button type="submit" class="btn btn-primary" style="flex: 2; padding: 12px; font-size: 14px;">
         Kirim Pengajuan Peminjaman
      </button>
      <a href="{{ route('katalog') }}" class="btn btn-outline" style="flex: 1; padding: 12px; font-size: 14px; text-align:center;">
        Batal
      </a>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
  function updateSelectedToolInfo() {
    const select = document.getElementById('tool_id');
    const selectedOption = select.options[select.selectedIndex];
    const previewTitle = document.getElementById('preview-title');
    
    if (selectedOption && selectedOption.value) {
      const nama = selectedOption.getAttribute('data-nama');
      const stok = selectedOption.getAttribute('data-stok');
      const kode = selectedOption.getAttribute('data-kode');
      previewTitle.innerHTML = `<strong>${nama}</strong> <span style="font-size:12px; font-weight:normal; color:#059669;">(${kode} • Sisa Stok: ${stok} Unit)</span>`;
    }
  }

  function hitungTanggalKembali() {
    const inputPinjam = document.getElementById('tgl_pinjam');
    const previewKembali = document.getElementById('tgl_kembali_preview');
    const keteranganKembali = document.getElementById('keterangan_kembali');

    if (inputPinjam.value) {
      const parts = inputPinjam.value.split('-');
      // Buat date object
      const tgl = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
      
      // Tambah 3 hari persis seperti Carbon::addDays(3)
      tgl.setDate(tgl.getDate() + 3);

      const bulanIndo = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      const hari = String(tgl.getDate()).padStart(2, '0');
      const bulan = bulanIndo[tgl.getMonth()];
      const tahun = tgl.getFullYear();

      previewKembali.value = `${hari} ${bulan} ${tahun} `;
      keteranganKembali.innerText = `Batas pengembalian otomatis: ${hari} ${bulan} ${tahun} (3 hari setelah ${parts[2]}/${parts[1]}/${parts[0]}).`;
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    updateSelectedToolInfo();
    hitungTanggalKembali();
  });
</script>
@endsection
