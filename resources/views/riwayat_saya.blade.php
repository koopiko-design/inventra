@extends('layouts.app')

@section('title', 'Riwayat Peminjaman Saya - INVENTRA SMK Negeri 5 Surakarta')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
  <div>
    <h1 style="font-size:22px; font-weight:800; color:var(--secondary); line-height:1.2;">
      RIWAYAT PEMINJAMAN SAYA
    </h1>
    <p style="font-size:12.5px; color:var(--muted); margin-top:3px;">
      Pantau status persetujuan, batas waktu kembali, dan catatan penolakan alat praktikum Anda secara real-time.
    </p>
  </div>
  <a href="{{ route('ajukan') }}" class="btn btn-primary">
    ➕ Ajukan Pinjam Baru
  </a>
</div>

<div class="card">
  <div class="card-header">
    <span class="card-title">Daftar Pengajuan Peminjaman Alat</span>
    <span style="font-size:11px; color:var(--muted); font-weight:600;">{{ $borrowings->count() }} pengajuan</span>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th style="width:30px;">No</th>
          <th>Alat Inventaris</th>
          <th>Jml</th>
          <th>Tgl Pinjam</th>
          <th>Batas Kembali</th>
          <th>Tujuan Penggunaan</th>
          <th>Status Pengajuan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($borrowings as $index => $b)
          <tr>
            <td style="color:var(--muted); font-weight:600;">{{ $index + 1 }}</td>
            <td>
              <div style="font-weight:700;">{{ $b->tool->nama_alat ?? 'Alat Inventaris' }}</div>
              <div style="font-size:11px; color:var(--primary); font-weight:600;">
                Kode: {{ $b->tool->kode_alat ?? '-' }}
              </div>
            </td>
            <td><strong>{{ $b->jumlah_unit }}</strong> Unit</td>
            <td>{{ date('d M Y', strtotime($b->tgl_pinjam)) }}</td>
            <td>
              <strong>{{ date('d M Y', strtotime($b->tgl_kembali)) }}</strong>
              @if($b->tgl_dikembalikan)
                <div style="font-size:11px; color:#059669; font-weight:600; margin-top:2px;">
                  ✓ Dikembalikan: {{ date('d M Y', strtotime($b->tgl_dikembalikan)) }}
                </div>
              @endif
            </td>
            <td style="max-width:260px; white-space:normal; font-size:12px; color:#334155;">
              {{ $b->alasan_pinjam }}
            </td>
            <td style="white-space:normal; min-width:160px;">
              @if($b->status === 'pending')
                <span class="badge badge-pending">Menunggu Verifikasi</span>
                <div style="font-size:11px; color:#D97706; margin-top:3px;">
                </div>
              @elseif($b->status === 'disetujui')
                <span class="badge badge-disetujui">Disetujui (Dipinjam)</span>
                <div style="font-size:11px; color:#059669; margin-top:3px;">
                  Harap dikembalikan tepat waktu.
                </div>
              @elseif($b->status === 'ditolak')
                <span class="badge badge-ditolak">Ditolak</span>
                {{-- KETERANGAN ALASAN PENOLAKAN DI BAWAH BADGE STATUS --}}
                @php
                  $reason = $b->rejection_reason ?? $b->alasan_penolakan;
                @endphp
                @if($reason)
                  <div style="margin-top:5px; background:#FEF2F2; border:1px solid #FECACA; padding:5px 8px; border-radius:6px; font-size:11.5px; color:#991B1B; font-weight:600; line-height:1.3;">
                    ✕ Alasan: {{ $reason }}
                  </div>
                @else
                  <div style="font-size:11px; color:#991B1B; margin-top:3px;">
                    Pengajuan belum memenuhi syarat.
                  </div>
                @endif
              @elseif($b->status === 'dikembalikan')
                <span class="badge badge-dikembalikan">Sudah Dikembalikan</span>
                @if($b->kondisi_kembali)
                  <div style="font-size:11px; color:#1E40AF; margin-top:3px;">
                    Kondisi: {{ $b->kondisi_kembali }}
                  </div>
                @endif
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 48px 16px; color: var(--muted);">
              <div style="font-size: 36px; margin-bottom: 8px;">📋</div>
              <p style="font-size: 14px; font-weight: 700; margin-bottom: 4px; color:var(--secondary);">Belum ada riwayat peminjaman.</p>
              <p style="font-size: 12px; margin-bottom: 16px;">Silakan ajukan peminjaman alat inventaris untuk praktikum laboratorium sekolah.</p>
              <a href="{{ route('ajukan') }}" class="btn btn-sm btn-primary">Mulai Pengajuan Baru</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
