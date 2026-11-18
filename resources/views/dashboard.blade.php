@extends('layouts.app')

@section('title', 'Dashboard Admin Sarpras - INVENTRA')

@section('content')
<!-- Header Dashboard -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:22px; font-weight:800; color:var(--secondary); line-height:1.2;">
      DASHBOARD MANAJEMEN SARPRAS
    </h1>
    <p style="font-size:12.5px; color:var(--muted); margin-top:3px;">
      Verifikasi persetujuan peminjaman alat, pemotongan stok otomatis, &amp; rekapitulasi data.
    </p>
  </div>
  <div style="display:flex; gap:8px; flex-wrap:wrap;">
    <a href="{{ route('admin.tools.index') }}" class="btn btn-sm btn-outline-green">
      📦 Kelola Barang
    </a>
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline">
      👥 Kelola User
    </a>
    <a href="{{ route('admin.rekap') }}" class="btn btn-sm btn-primary">
      🖨️ Rekap &amp; Cetak
    </a>
  </div>
</div>

<!-- Grid Metrik Statistik -->
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon" style="background:#ECFDF5; color:#059669;">📦</div>
    <div>
      <div class="stat-num" style="color:#059669;">{{ $totalAlat }}</div>
      <div class="stat-lbl">Total Jenis Alat</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon" style="background:#FFFBEB; color:#D97706;">⏳</div>
    <div>
      <div class="stat-num" style="color:#D97706;">{{ $pendingCount }}</div>
      <div class="stat-lbl">Menunggu Verifikasi</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon" style="background:#ECFDF5; color:#047857;">✓</div>
    <div>
      <div class="stat-num" style="color:#047857;">{{ $approvedCount }}</div>
      <div class="stat-lbl">Sedang Dipinjam</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon" style="background:#EFF6FF; color:#2563EB;">↩</div>
    <div>
      <div class="stat-num" style="color:#2563EB;">{{ $returnedCount }}</div>
      <div class="stat-lbl">Telah Dikembalikan</div>
    </div>
  </div>
</div>

<!-- Filter Tabs Transaksi -->
<div class="filter-tabs">
  <a href="{{ route('dashboard') }}" class="filter-tab {{ !$statusFilter ? 'active' : '' }}">
    Semua Transaksi ({{ $borrowings->count() }})
  </a>
  <a href="{{ route('dashboard', ['status' => 'pending']) }}" class="filter-tab {{ $statusFilter === 'pending' ? 'active' : '' }}">
    ⏳ Menunggu Persetujuan ({{ $pendingCount }})
  </a>
  <a href="{{ route('dashboard', ['status' => 'disetujui']) }}" class="filter-tab {{ $statusFilter === 'disetujui' ? 'active' : '' }}">
    ⚡ Disetujui / Dipinjam ({{ $approvedCount }})
  </a>
  <a href="{{ route('dashboard', ['status' => 'dikembalikan']) }}" class="filter-tab {{ $statusFilter === 'dikembalikan' ? 'active' : '' }}">
    ✔️ Sudah Dikembalikan ({{ $returnedCount }})
  </a>
  <a href="{{ route('dashboard', ['status' => 'ditolak']) }}" class="filter-tab {{ $statusFilter === 'ditolak' ? 'active' : '' }}">
    ❌ Ditolak
  </a>
</div>

<!-- Tabel Utama Rekapitulasi & Verifikasi Peminjaman -->
<div class="card" style="margin-bottom:28px;">
  <div class="card-header">
    <div>
      <span class="card-title">Daftar Transaksi Peminjaman Siswa</span>
      <div style="font-size:11px; color:var(--muted); margin-top:2px;">
        Klik "Setujui" untuk memotong stok, atau "Tolak" untuk memasukkan alasan penolakan.
      </div>
    </div>
    <span style="font-size:12px; color:var(--muted); font-weight:600;">
      {{ $borrowings->count() }} data
    </span>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Peminjam (Siswa)</th>
          <th>Barang / Alat Praktik</th>
          <th>Jml</th>
          <th>Jadwal Peminjaman</th>
          <th>Tujuan Penggunaan</th>
          <th>Status</th>
          <th style="min-width: 200px;">Aksi Verifikasi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($borrowings as $b)
          <tr>
            <td>
              <div style="font-weight:700; color:var(--secondary);">{{ $b->nama_peminjam }}</div>
              <div style="font-size:11px; color:var(--muted); font-weight:500;">
                {{ $b->kelas_nis }}
              </div>
            </td>

            <td>
              <div style="font-weight:700;">{{ $b->tool->nama_alat ?? 'Alat Inventaris' }}</div>
              <div style="font-size:11px; color:var(--primary); font-weight:600;">
                Kode: {{ $b->tool->kode_alat ?? '-' }} • Sisa Stok: {{ $b->tool->stok ?? 0 }} Unit
              </div>
            </td>

            <td>
              <strong style="color:var(--primary);">{{ $b->jumlah_unit }}</strong> Unit
            </td>

            <td>
              <div style="font-size:12px;">Pinjam: <strong>{{ date('d/m/Y', strtotime($b->tgl_pinjam)) }}</strong></div>
              <div style="font-size:11px; color:var(--muted);">Kembali: <strong>{{ date('d/m/Y', strtotime($b->tgl_kembali)) }}</strong></div>
              @if($b->tgl_dikembalikan)
                <div style="font-size:10.5px; color:#059669; font-weight:700; margin-top:2px;">
                  ✓ Kembali: {{ date('d/m/Y', strtotime($b->tgl_dikembalikan)) }}
                </div>
              @endif
            </td>

            <td style="max-width:220px; white-space:normal; font-size:12px;">
              <div style="color:#334155;">{{ $b->alasan_pinjam }}</div>
              @if($b->kondisi_kembali)
                <div style="color:#059669; font-size:11px; margin-top:3px; font-weight:600;">
                  Kondisi: {{ $b->kondisi_kembali }}
                </div>
              @endif
            </td>

            {{-- Kolom Status: Tampilkan Alasan Penolakan di bawah badge jika status Ditolak --}}
            <td style="white-space:normal; min-width:140px;">
              @if($b->status === 'pending')
                <span class="badge badge-pending">Pending</span>
              @elseif($b->status === 'disetujui')
                <span class="badge badge-disetujui">Disetujui</span>
              @elseif($b->status === 'ditolak')
                <span class="badge badge-ditolak">Ditolak</span>
                @php
                  $reason = $b->rejection_reason ?? $b->alasan_penolakan;
                @endphp
                @if($reason)
                  <div style="font-size:11px; color:#DC2626; margin-top:5px; line-height:1.3; font-weight:600; background:#FEF2F2; padding:4px 8px; border-radius:6px; border:1px solid #FECACA;">
                    ✕ {{ $reason }}
                  </div>
                @endif
              @elseif($b->status === 'dikembalikan')
                <span class="badge badge-dikembalikan">Dikembalikan</span>
              @endif
            </td>

            <td>
              <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                @if($b->status === 'pending')
                  {{-- Setujui (verifyApproval) --}}
                  <form action="{{ route('borrowing.verify', $b->id) }}" method="POST" style="display:inline;"
                        data-confirm="Setujui pengajuan peminjaman ini? Stok barang akan otomatis berkurang."
                        data-confirm-title="Setujui Peminjaman?"
                        data-confirm-type="success"
                        data-confirm-btn="✓ Ya, Setujui">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn btn-xs btn-success">
                      ✓ Setujui
                    </button>
                  </form>

                  {{-- Tombol Tolak: Buka Modal Isi Alasan --}}
                  <button type="button" 
                          onclick="bukaModalTolak({{ $b->id }}, '{{ addslashes($b->nama_peminjam) }}', '{{ addslashes($b->tool->nama_alat ?? 'Alat') }}')" 
                          class="btn btn-xs btn-danger">
                    ✕ Tolak
                  </button>

                @elseif($b->status === 'disetujui')
                  {{-- Konfirmasi Pengembalian: processReturn --}}
                  <form action="{{ route('borrowing.return', $b->id) }}" method="POST" style="display:inline;"
                        data-confirm="Konfirmasi pengembalian barang ini? Stok barang akan bertambah kembali ke inventaris."
                        data-confirm-title="Konfirmasi Pengembalian?"
                        data-confirm-type="info"
                        data-confirm-btn="↩ Ya, Konfirmasi">
                    @csrf
                    <button type="submit" class="btn btn-xs btn-primary">
                      ↩ Konfirmasi Kembali
                    </button>
                  </form>

                @else
                  <span style="font-size:11px; color:var(--muted); font-weight:600;">Selesai</span>
                @endif

                {{-- Tombol Hapus (destroyBorrowing) --}}
                <form action="{{ route('borrowing.destroy', $b->id) }}" method="POST" style="display:inline;"
                      data-confirm="Hapus data transaksi ini secara permanen? Data yang telah dihapus tidak dapat dipulihkan."
                      data-confirm-title="Hapus Transaksi?"
                      data-confirm-type="danger"
                      data-confirm-btn="🗑️ Ya, Hapus">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-outline" style="color:#DC2626; border-color:#FECACA;" title="Hapus Data">
                    🗑️
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 40px; color: var(--muted);">
              <div style="font-size: 28px; margin-bottom: 6px;">📂</div>
              Belum ada transaksi peminjaman untuk filter status ini.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Ringkasan Stok Inventaris Barang -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Ringkasan Inventaris Barang Sarpras</span>
    <a href="{{ route('admin.tools.index') }}" class="btn btn-xs btn-outline-green">
      Kelola Semua Barang &rarr;
    </a>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Kode Alat</th>
          <th>Nama Barang / Alat</th>
          <th>Kategori</th>
          <th>Stok Tersedia</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tools as $t)
          <tr>
            <td><code style="background:#F1F5F9; padding:2px 6px; border-radius:4px; font-weight:700; color:var(--primary);">{{ $t->kode_alat }}</code></td>
            <td><strong>{{ $t->nama_alat }}</strong></td>
            <td>{{ $t->category->nama_kategori ?? 'Umum' }}</td>
            <td><strong>{{ $t->stok }}</strong> Unit</td>
            <td>
              @if($t->stok > 0)
                <span class="badge badge-disetujui">Tersedia</span>
              @else
                <span class="badge badge-ditolak">Habis</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- MODAL FORM PENOLAKAN PEMINJAMAN (REQUIREMENT 4) --}}
<div class="modal-overlay" id="modal-tolak">
  <div class="modal-box">
    <h2 class="modal-title" style="color:#DC2626;">✕ Tolak Pengajuan Peminjaman</h2>
    <p style="font-size:12.5px; color:#475569; margin-bottom:14px;" id="modal-tolak-info">
      Pengajuan peminjaman akan ditolak. Berikan keterangan alasan penolakan agar siswa memahami alasannya.
    </p>

    <form id="form-tolak" method="POST" action="">
      @csrf
      <input type="hidden" name="action" value="reject">

      <div class="form-group">
        <label class="form-label">Alasan Penolakan <span style="color:red">*</span></label>
        <textarea name="alasan_penolakan" class="form-control" rows="3" required placeholder="Contoh: Jadwal penggunaan bentrok dengan ujian UKK / Alat sedang dalam perawatan berkala..."></textarea>
        <div class="form-hint">Keterangan ini akan langsung tampil pada riwayat peminjaman siswa di bawah badge status Ditolak.</div>
      </div>

      <div class="modal-actions">
        <button type="button" onclick="closeModal('modal-tolak')" class="btn btn-outline">Batal</button>
        <button type="submit" class="btn btn-danger">Konfirmasi Tolak Pengajuan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function bukaModalTolak(id, namaPeminjam, namaAlat) {
    const form = document.getElementById('form-tolak');
    form.action = `/borrowing/verify/${id}`;
    document.getElementById('modal-tolak-info').innerHTML = 
      `Anda akan menolak pengajuan <strong>${namaAlat}</strong> atas nama <strong>${namaPeminjam}</strong>. Silakan isi alasan penolakan:`;
    openModal('modal-tolak');
  }
</script>
@endsection
