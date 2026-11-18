@extends('layouts.app')
@section('title', 'Rekap Peminjaman - INVENTRA Admin')

@section('styles')
<style>
  /* Print Styles khusus halaman rekap */
  @media print {
    @page { size: A4 landscape; margin: 12mm 10mm; }
    body { font-size: 10px !important; }
    .table th, .table td { padding: 5px 7px !important; white-space: normal !important; }
    .print-title { font-size: 14px !important; }
    .stat-grid { grid-template-columns: repeat(4, 1fr) !important; gap: 8px !important; }
    .stat-card { padding: 10px 12px !important; }
    .stat-num  { font-size: 18px !important; }
  }
</style>
@endsection

@section('content')
{{-- Header Rekap --}}
<div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 class="print-title" style="font-size:20px; font-weight:800; color:#334155;">REKAP PEMINJAMAN ALAT INVENTARIS</h1>
    <p style="font-size:12px; color:#64748B;">SMK Negeri 5 Surakarta • Dicetak: {{ $printDate }}</p>
  </div>
  <div class="no-print" style="display:flex; gap:8px;">
    <a href="{{ route('admin.rekap') }}" class="btn btn-outline btn-sm">Reset Filter</a>
    <a href="{{ route('admin.rekap.export', request()->query()) }}" class="btn btn-primary btn-sm">
      📊 Cetak CSV / Excel
    </a>
    <button type="button" onclick="window.print()" class="btn btn-outline btn-sm" title="Cetak langsung lewat printer">
      🖨️ Print
    </button>
  </div>
</div>

{{-- Filter (tidak dicetak) --}}
<div class="card no-print" style="margin-bottom:20px;">
  <div class="card-body" style="padding:14px 18px;">
    <form method="GET" action="{{ route('admin.rekap') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <select name="status" class="form-control" style="max-width:180px;">
        <option value="">Semua Status</option>
        <option value="pending"      {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="disetujui"    {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
        <option value="ditolak"      {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
        <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
      </select>
      <div style="display:flex; gap:6px; align-items:center; font-size:12px; color:#64748B;">
        <span>Tanggal Pinjam:</span>
        <input type="date" name="dari" value="{{ request('dari') }}" class="form-control" style="max-width:150px;">
        <span>s/d</span>
        <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control" style="max-width:150px;">
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Terapkan Filter</button>
    </form>
  </div>
</div>

{{-- Summary Stats --}}
<div class="stat-grid" style="margin-bottom:24px;">
  <div class="stat-card">
    <div class="stat-icon" style="background:#EFF6FF;">📊</div>
    <div><div class="stat-num">{{ $stats['total'] }}</div><div class="stat-lbl">Total Transaksi</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#FEF3C7;">⏳</div>
    <div><div class="stat-num" style="color:#D97706;">{{ $stats['pending'] }}</div><div class="stat-lbl">Pending</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#D1FAE5;">✓</div>
    <div><div class="stat-num" style="color:#059669;">{{ $stats['disetujui'] }}</div><div class="stat-lbl">Disetujui</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#DBEAFE;">↩</div>
    <div><div class="stat-num" style="color:#2563EB;">{{ $stats['dikembalikan'] }}</div><div class="stat-lbl">Dikembalikan</div></div>
  </div>
</div>

{{-- Header cetak (hanya tampil saat print) --}}
<div style="display:none;" class="print-header">
  <div style="display:flex; align-items:center; justify-content:center; gap:16px; margin-bottom:14px; border-bottom:2px solid #059669; padding-bottom:10px;">
    @if(file_exists(public_path('images/logo.png')))
      <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:55px; width:auto;">
    @endif
    <div style="text-align:center;">
      <div style="font-size:18px; font-weight:800; color:#059669;">INVENTRA — SMK NEGERI 5 SURAKARTA</div>
      <div style="font-size:11px; color:#666;">REKAP DATA PEMINJAMAN ALAT INVENTARIS • {{ $printDate }}</div>
      <div style="font-size:10px; color:#888; margin-top:2px;">Jumlah Data: {{ $stats['total'] }} transaksi</div>
    </div>
  </div>
</div>

{{-- Tabel Rekap --}}
<div class="card">
  <div class="card-header no-print">
    <span class="card-title">Daftar Transaksi Peminjaman ({{ $borrowings->count() }} data)</span>
  </div>
  <div class="table-responsive">
    <table class="table" style="white-space:normal;">
      <thead>
        <tr>
          <th style="width:28px;">No</th>
          <th>Peminjam</th>
          <th>Alat / Barang</th>
          <th>Kategori</th>
          <th>Jml</th>
          <th>Tgl Pinjam</th>
          <th>Rencana Kembali</th>
          <th>Tgl Dikembalikan</th>
          <th>Tujuan Peminjaman</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($borrowings as $i => $b)
        <tr>
          <td style="color:#94A3B8; font-weight:600;">{{ $i + 1 }}</td>
          <td>
            <div style="font-weight:600; white-space:nowrap;">{{ $b->nama_peminjam }}</div>
            <div style="font-size:11px; color:#64748B;">{{ $b->kelas_nis }}</div>
          </td>
          <td>
            <div style="font-weight:600; white-space:nowrap;">{{ $b->tool->nama_alat ?? '—' }}</div>
            <div style="font-size:10px; color:var(--primary);">{{ $b->tool->kode_alat ?? '' }}</div>
          </td>
          <td style="font-size:12px; white-space:nowrap;">{{ $b->tool->category->nama_kategori ?? '—' }}</td>
          <td style="font-weight:700; text-align:center;">{{ $b->jumlah_unit }}</td>
          <td style="white-space:nowrap;">{{ date('d/m/Y', strtotime($b->tgl_pinjam)) }}</td>
          <td style="white-space:nowrap; font-weight:600;">{{ date('d/m/Y', strtotime($b->tgl_kembali)) }}</td>
          <td style="white-space:nowrap; color:#059669;">
            {{ $b->tgl_dikembalikan ? date('d/m/Y', strtotime($b->tgl_dikembalikan)) : '—' }}
          </td>
          <td style="max-width:200px; white-space:normal; font-size:12px; color:#475569;">{{ Str::limit($b->alasan_pinjam, 60) }}</td>
          <td>
            @if($b->status === 'pending')
              <span class="badge badge-pending">Pending</span>
            @elseif($b->status === 'disetujui')
              <span class="badge badge-disetujui">Disetujui</span>
            @elseif($b->status === 'ditolak')
              <span class="badge badge-ditolak">Ditolak</span>
              @if($b->alasan_penolakan)
                <div style="font-size:10px; color:#991B1B; white-space:normal; max-width:160px; margin-top:2px;">{{ $b->alasan_penolakan }}</div>
              @endif
            @elseif($b->status === 'dikembalikan')
              <span class="badge badge-dikembalikan">Kembali</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" style="text-align:center; padding:40px; color:#94A3B8;">Tidak ada data transaksi peminjaman.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:12px 18px; border-top:1px solid var(--border); font-size:11px; color:#94A3B8; display:flex; justify-content:space-between;" class="no-print">
    <span>Total: {{ $borrowings->count() }} data ditampilkan</span>
    <span>Dicetak: {{ $printDate }}</span>
  </div>
</div>


@endsection

@section('scripts')
<script>
  // Tampilkan print-header saat print
  window.addEventListener('beforeprint', () => {
    document.querySelectorAll('.print-header, .print-footer').forEach(el => el.style.display = 'block');
    document.querySelectorAll('.print-header').forEach(el => el.style.display = 'block');
    document.querySelectorAll('.print-footer').forEach(el => el.style.display = 'grid');
  });
  window.addEventListener('afterprint', () => {
    document.querySelectorAll('.print-header, .print-footer').forEach(el => el.style.display = 'none');
  });
</script>
@endsection
