@extends('layouts.app')

@section('title', 'Katalog Barang Inventaris - INVENTRA SMK Negeri 5 Surakarta')

@section('styles')
<style>
  .katalog-header {
    margin-bottom: 20px;
    background: white;
    padding: 22px 26px;
    border-radius: 12px;
    border: 1px solid var(--border);
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }

  .katalog-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--secondary);
    margin-bottom: 4px;
  }

  .katalog-subtitle {
    font-size: 12.5px;
    color: var(--muted);
  }

  .filter-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 24px;
    background: white;
    padding: 14px 18px;
    border-radius: 10px;
    border: 1px solid var(--border);
    flex-wrap: wrap;
    align-items: center;
  }

  .catalog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 18px;
  }

  .catalog-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
  }

  .catalog-card:hover {
    transform: translateY(-3px);
    border-color: var(--primary);
    box-shadow: 0 8px 18px rgba(5, 150, 105, 0.1);
  }

  .tool-img-wrapper {
    width: 100%;
    height: 165px;
    background: #F8FAFC;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
    margin-bottom: 14px;
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .tool-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .catalog-card:hover .tool-img-wrapper img {
    transform: scale(1.05);
  }

  .tool-img-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #F8FAFC, #F1F5F9);
  }

  .tool-icon {
    font-size: 38px;
    margin-bottom: 3px;
  }

  .tool-code-badge {
    position: absolute;
    bottom: 8px;
    left: 8px;
    font-size: 11px;
    font-weight: 700;
    color: var(--primary);
    background: rgba(255, 255, 255, 0.95);
    padding: 3px 9px;
    border-radius: 6px;
    border: 1px solid var(--border);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    backdrop-filter: blur(4px);
    letter-spacing: 0.3px;
  }

  .tool-category {
    font-size: 10.5px;
    font-weight: 700;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }

  .tool-name {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--secondary);
    margin-bottom: 6px;
    line-height: 1.3;
  }

  .tool-description {
    font-size: 12px;
    color: var(--muted);
    line-height: 1.5;
    margin-bottom: 16px;
  }

  .catalog-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid var(--border);
  }
</style>
@endsection

@section('content')
<div class="katalog-header">
  <h1 class="katalog-title">KATALOG BARANG &amp; ALAT PRAKTIKUM</h1>
  <p class="katalog-subtitle">Daftar sarana laboratorium dan perlengkapan praktikum SMK Negeri 5 Surakarta yang siap dipinjam.</p>
</div>

<!-- Form Filter & Pencarian -->
<form action="{{ route('katalog') }}" method="GET" class="filter-bar">
  <div style="flex: 2; min-width: 200px;">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama alat, kode, atau kata kunci...">
  </div>

  <div style="flex: 1; min-width: 180px;">
    <select name="category_id" class="form-control">
      <option value="">Semua Kategori Alat</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
          {{ $category->nama_kategori }}
        </option>
      @endforeach
    </select>
  </div>

  <div style="display: flex; gap: 8px;">
    <button type="submit" class="btn btn-primary btn-sm">
      🔍 Cari
    </button>
    @if(request('search') || request('category_id'))
      <a href="{{ route('katalog') }}" class="btn btn-outline btn-sm">
        Reset
      </a>
    @endif
  </div>
</form>

<!-- Grid Katalog -->
<div class="catalog-grid">
  @forelse($tools as $tool)
    @php
      $catName = strtolower($tool->category->nama_kategori ?? '');
      $icon = '📦';
      if (str_contains($catName, 'foto') || str_contains($catName, 'video')) {
          $icon = '📷';
      } elseif (str_contains($catName, 'komputer') || str_contains($catName, 'jaringan')) {
          $icon = '💻';
      } elseif (str_contains($catName, 'elektronik')) {
          $icon = '⚡';
      }
    @endphp

    <div class="catalog-card">
      <div>
        <div class="tool-img-wrapper">
          @if($tool->gambar_url)
            <img src="{{ $tool->gambar_url }}" alt="{{ $tool->nama_alat }}" loading="lazy">
          @else
            <div class="tool-img-fallback">
              <div class="tool-icon">{{ $icon }}</div>
            </div>
          @endif
          <span class="tool-code-badge">{{ $tool->kode_alat }}</span>
        </div>

        <div class="tool-category">{{ $tool->category->nama_kategori ?? 'Umum' }}</div>
        <h3 class="tool-name">{{ $tool->nama_alat }}</h3>
        <p class="tool-description">{{ $tool->deskripsi }}</p>
      </div>

      <div class="catalog-card-footer">
        @if($tool->stok > 0)
          <div>
            <span class="badge badge-disetujui">Stok: {{ $tool->stok }} Unit</span>
          </div>
          <a href="{{ route('ajukan', ['nama' => $tool->nama_alat, 'tool_id' => $tool->id]) }}" class="btn btn-sm btn-primary">
            Pinjam Alat
          </a>
        @else
          <div>
            <span class="badge badge-ditolak">Habis (0)</span>
          </div>
          <button disabled class="btn btn-sm btn-outline" style="opacity: 0.5; cursor: not-allowed;">
            Tidak Tersedia
          </button>
        @endif
      </div>
    </div>
  @empty
    <div style="grid-column: 1 / -1; background: white; padding: 48px 24px; border-radius: 12px; text-align: center; border: 1px dashed var(--border);">
      <div style="font-size: 36px; margin-bottom: 10px;">🔍</div>
      <h3 style="font-size: 15px; font-weight: 700; color: var(--secondary); margin-bottom: 4px;">Barang Tidak Ditemukan</h3>
      <p style="font-size: 12.5px; color: var(--muted); margin-bottom: 14px;">
        Tidak ada alat inventaris yang cocok dengan pencarian Anda.
      </p>
      <a href="{{ route('katalog') }}" class="btn btn-sm btn-outline">Lihat Semua Alat</a>
    </div>
  @endforelse
</div>
@endsection
