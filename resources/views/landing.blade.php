@extends('layouts.app')

@section('title', 'INVENTRA - Sistem Peminjaman Inventaris SMK Negeri 5 Surakarta')

@section('styles')
<style>
  /* Hero Section */
  .hero-container {
    display: grid;
    grid-template-columns: 1.25fr 1fr;
    gap: 32px;
    align-items: center;
    margin-bottom: 36px;
    background: white;
    padding: 36px;
    border-radius: 14px;
    border: 1px solid var(--border);
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  }

  .school-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ECFDF5;
    color: var(--primary);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 12px;
    border: 1px solid #A7F3D0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .hero-title {
    font-size: 30px;
    font-weight: 800;
    color: var(--secondary);
    line-height: 1.2;
    margin-bottom: 12px;
    letter-spacing: -0.5px;
  }

  .hero-title span {
    color: var(--primary);
  }

  .hero-desc {
    font-size: 13.5px;
    color: var(--muted);
    line-height: 1.6;
    margin-bottom: 24px;
  }

  .hero-actions {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
  }

  /* Status Panel Card */
  .status-panel-card {
    background: linear-gradient(135deg, #064E3B, #047857);
    border-radius: 12px;
    padding: 24px;
    color: white;
    box-shadow: 0 8px 20px rgba(5, 150, 105, 0.25);
  }

  .status-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 12px;
    margin-bottom: 18px;
  }

  .status-panel-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #A7F3D0;
  }

  .live-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    color: #A7F3D0;
    font-weight: 600;
  }

  .live-dot {
    width: 8px;
    height: 8px;
    background-color: #34D399;
    border-radius: 50%;
    box-shadow: 0 0 8px #34D399;
  }

  .stats-grid-hero {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
  }

  .stat-card-hero {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 16px 10px;
    text-align: center;
  }

  .stat-num-hero {
    font-size: 28px;
    font-weight: 800;
    color: white;
    line-height: 1;
    margin-bottom: 4px;
  }

  .stat-lbl-hero {
    font-size: 10.5px;
    color: #D1FAE5;
    text-transform: uppercase;
    font-weight: 600;
  }

  /* Workflow 4 Steps */
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 18px;
  }

  .section-title {
    font-size: 17px;
    font-weight: 800;
    color: var(--secondary);
  }

  .section-subtitle {
    font-size: 12px;
    color: var(--muted);
  }

  .workflow-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 40px;
  }

  .workflow-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 20px;
    transition: all 0.2s ease;
  }

  .workflow-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border-color: var(--primary);
  }

  .step-badge {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #ECFDF5;
    color: var(--primary);
    font-weight: 800;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    border: 1px solid #A7F3D0;
  }

  .step-title {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--secondary);
    margin-bottom: 4px;
  }

  .step-desc {
    font-size: 12px;
    color: var(--muted);
    line-height: 1.5;
  }

  .recent-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
  }

  .tool-mini-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s;
  }

  .tool-mini-card:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.08);
  }

  @media (max-width: 900px) {
    .hero-container { grid-template-columns: 1fr; padding: 22px; }
    .workflow-grid { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 500px) {
    .workflow-grid { grid-template-columns: 1fr; }
  }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="hero-container">
  <div>
    <div class="school-tag">
      <span>🏫</span> SMK Negeri 5 Surakarta • Sarana &amp; Prasarana
    </div>
    <h1 class="hero-title">Sistem Peminjaman<br><span>Inventaris Sekolah</span> Praktikum</h1>
    <p class="hero-desc">
      <strong>INVENTRA</strong> memudahkan seluruh siswa SMK Negeri 5 Surakarta dalam mencari alat, mengajukan peminjaman secara digital, serta memantau status persetujuan secara real-time dan terstruktur.
    </p>

    <div class="hero-actions">
      @auth
        @if(Auth::user()->role === 'admin')
          <a href="{{ route('dashboard') }}" class="btn btn-primary">
             Dashboard Admin Sarpras
          </a>
          <a href="{{ route('admin.tools.index') }}" class="btn btn-outline">
             Kelola Barang
          </a>
        @else
          <a href="{{ route('ajukan') }}" class="btn btn-primary">
             Ajukan Peminjaman
          </a>
          <a href="{{ route('riwayat.saya') }}" class="btn btn-outline">
             Peminjaman Saya
          </a>
        @endif
      @else
        <a href="{{ route('ajukan') }}" class="btn btn-primary">
           Ajukan Peminjaman Barang
        </a>
        <a href="{{ route('katalog') }}" class="btn btn-outline">
           Lihat Katalog Barang
        </a>
      @endauth
    </div>
  </div>

  <!-- Panel Status Inventaris -->
  <div class="status-panel-card">
    <div class="status-panel-header">
      <div class="status-panel-title">Panel Status Inventaris</div>
      <div class="live-indicator">
        <span class="live-dot"></span> Sistem Aktif
      </div>
    </div>

    <div class="stats-grid-hero">
      <div class="stat-card-hero">
        <div class="stat-num-hero">{{ str_pad($availableTools ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-lbl-hero">Alat Tersedia</div>
      </div>

      <div class="stat-card-hero">
        <div class="stat-num-hero">{{ str_pad($borrowedTools ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-lbl-hero">Sedang Dipinjam</div>
      </div>

      <div class="stat-card-hero">
        <div class="stat-num-hero">{{ str_pad($totalTransactions ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="stat-lbl-hero">Total Transaksi</div>
      </div>
    </div>

    <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.15); display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: #A7F3D0;">
      <span>SMK Negeri 5 Surakarta</span>
      <a href="{{ route('katalog') }}" style="color: white; text-decoration: underline; font-weight: 600;">Lihat semua alat &rarr;</a>
    </div>
  </div>
</div>

<!-- Workflow Section -->
<div class="section-header">
  <div>
    <h2 class="section-title">Alur Kerja Peminjaman — 4 Langkah Mudah</h2>
    <p class="section-subtitle">Proses peminjaman alat praktikum laboratorium yang terstandar di sekolah.</p>
  </div>
</div>

<div class="workflow-grid">
  <div class="workflow-card">
    <div class="step-badge">1</div>
    <div class="step-title">Pilih Alat Praktik</div>
    <div class="step-desc">Buka katalog inventaris, temukan alat praktikum yang Anda butuhkan sesuai jurusan Anda.</div>
  </div>

  <div class="workflow-card">
    <div class="step-badge">2</div>
    <div class="step-title">Isi Form Pengajuan</div>
    <div class="step-desc">Isi form peminjaman (1 barang per pengajuan) dengan durasi otomatis 3 hari kalender.</div>
  </div>

  <div class="workflow-card">
    <div class="step-badge">3</div>
    <div class="step-title">Verifikasi Admin</div>
    <div class="step-desc">Petugas sarana dan prasarana akan memvalidasi pengajuan dan menyetujui ketersediaan stok.</div>
  </div>

  <div class="workflow-card">
    <div class="step-badge">4</div>
    <div class="step-title">Ambil &amp; Kembalikan</div>
    <div class="step-desc">Ambil alat di ruang inventaris sarpras dan kembalikan tepat waktu dalam kondisi prima.</div>
  </div>
</div>

<!-- Recent Tools Showcase -->
<div class="section-header">
  <div>
    <h2 class="section-title">Alat Inventaris Terpopuler</h2>
    <p class="section-subtitle">Daftar perlengkapan praktikum yang sering digunakan di laboratorium.</p>
  </div>
  <a href="{{ route('katalog') }}" class="btn btn-xs btn-outline">Buka Katalog Lengkap &rarr;</a>
</div>

<div class="recent-grid">
  @forelse($recentTools ?? [] as $tool)
    <div class="tool-mini-card">
      <div>
        <div style="font-size: 10px; font-weight: 700; color: var(--primary); text-transform: uppercase; margin-bottom: 4px;">
          {{ $tool->category->nama_kategori ?? 'UMUM' }} • {{ $tool->kode_alat }}
        </div>
        <div style="font-size: 14px; font-weight: 700; color: var(--secondary); margin-bottom: 6px;">
          {{ $tool->nama_alat }}
        </div>
        <p style="font-size: 12px; color: var(--muted); line-height: 1.4; margin-bottom: 14px;">
          {{ Str::limit($tool->deskripsi, 80) }}
        </p>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid var(--border);">
        @if($tool->stok > 0)
          <span class="badge badge-disetujui">Stok: {{ $tool->stok }}</span>
          <a href="{{ route('ajukan', ['nama' => $tool->nama_alat, 'tool_id' => $tool->id]) }}" class="btn btn-xs btn-primary">
            Pinjam Alat
          </a>
        @else
          <span class="badge badge-ditolak">Habis (0)</span>
          <button disabled class="btn btn-xs btn-outline" style="opacity: 0.5; cursor: not-allowed;">
            Habis
          </button>
        @endif
      </div>
    </div>
  @empty
    <div style="grid-column: 1 / -1; background: white; padding: 24px; border-radius: 10px; text-align: center; color: var(--muted);">
      Belum ada data inventaris.
    </div>
  @endforelse
</div>
@endsection
