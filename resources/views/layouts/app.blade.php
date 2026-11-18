<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'INVENTRA - Sistem Inventaris SMK Negeri 5 Surakarta')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ===== DESIGN SYSTEM: EMERALD GREEN + SLATE GRAY ===== */
    :root {
      --primary:       #059669;   /* Emerald Green — navbar, tombol utama, header */
      --primary-dark:  #047857;   /* Hover & active primary */
      --primary-light: #ECFDF5;   /* Background chip/badge */
      --secondary:     #334155;   /* Slate Gray — teks penting, secondary btn */
      --accent:        #10B981;   /* Teal aksen */
      --danger:        #EF4444;
      --warning:       #F59E0B;
      --info:          #3B82F6;
      --bg:            #F1F5F9;   /* Background halaman */
      --surface:       #FFFFFF;
      --border:        #E2E8F0;
      --text:          #0F172A;
      --muted:         #64748B;
    }

    /* ===== RESET & BASE ===== */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      font-size: 14px;
      line-height: 1.5;
    }

    /* ===== TOP INFO BAR ===== */
    .top-bar {
      background: #064E3B; /* Emerald-900 */
      color: #6EE7B7;
      font-size: 11px;
      padding: 5px 28px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      letter-spacing: 0.3px;
    }

    /* ===== NAVBAR ===== */
    .navbar {
      background: var(--primary);
      padding: 12px 28px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
      position: sticky;
      top: 0;
      z-index: 999;
      flex-wrap: wrap;
      gap: 10px;
    }

    .brand-wrap {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      flex-shrink: 0;
    }

    .brand-logo-img {
      height: 42px;
      width: auto;
      max-width: 48px;
      object-fit: contain;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
      transition: transform 0.2s ease;
    }

    .brand-wrap:hover .brand-logo-img {
      transform: scale(1.05);
    }

    .brand-logo-fallback {
      width: 40px; height: 40px;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: 16px; color: white;
      letter-spacing: -1px;
    }

    .brand-name  { font-size: 19px; font-weight: 800; color: white; letter-spacing: 1px; line-height: 1; }
    .brand-sub   { font-size: 9px; color: #A7F3D0; font-weight: 500; letter-spacing: 0.3px; }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 4px;
      flex-wrap: wrap;
    }

    .nav-links a {
      color: #D1FAE5;
      text-decoration: none;
      font-size: 13px;
      font-weight: 500;
      padding: 6px 12px;
      border-radius: 6px;
      transition: all 0.18s;
      white-space: nowrap;
    }

    .nav-links a:hover, .nav-links a.active {
      background: rgba(255,255,255,0.15);
      color: white;
    }

    .nav-user {
      display: flex;
      align-items: center;
      gap: 8px;
      background: rgba(0,0,0,0.12);
      border: 1px solid rgba(255,255,255,0.2);
      padding: 4px 12px 4px 5px;
      border-radius: 24px;
    }

    .avatar {
      width: 30px; height: 30px;
      background: rgba(255,255,255,0.25);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 12px; color: white;
    }

    .user-name  { font-size: 12px; color: white; font-weight: 600; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .role-chip  { font-size: 9px; padding: 1px 7px; border-radius: 10px; font-weight: 700; text-transform: uppercase; }
    .chip-admin { background: #FDE047; color: #713F12; }
    .chip-siswa { background: #A7F3D0; color: #064E3B; }

    /* ===== WRAPPER / CONTAINER ===== */
    .page-wrap {
      flex: 1;
      max-width: 1280px;
      width: 100%;
      margin: 28px auto;
      padding: 0 20px;
    }

    /* ===== CARDS ===== */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .card-header {
      padding: 18px 22px;
      border-bottom: 1px solid var(--border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }

    .card-title { font-size: 15px; font-weight: 700; color: var(--secondary); }
    .card-body  { padding: 22px; }

    /* ===== BUTTONS ===== */
    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 6px;
      padding: 9px 18px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      text-decoration: none;
      transition: all 0.18s;
      white-space: nowrap;
    }
    .btn:hover { transform: translateY(-1px); filter: brightness(1.07); }
    .btn-sm    { padding: 6px 12px; font-size: 12px; }
    .btn-xs    { padding: 4px 9px; font-size: 11px; }

    .btn-primary   { background: var(--primary); color: white; }
    .btn-secondary { background: var(--secondary); color: white; }
    .btn-success   { background: #10B981; color: white; }
    .btn-danger    { background: var(--danger); color: white; }
    .btn-warning   { background: var(--warning); color: white; }
    .btn-info      { background: var(--info); color: white; }
    .btn-outline   { background: white; color: var(--secondary); border: 1.5px solid var(--border); }
    .btn-outline:hover { background: var(--bg); }
    .btn-outline-green { background: white; color: var(--primary); border: 1.5px solid var(--primary); }

    /* ===== FORMS ===== */
    .form-group { margin-bottom: 16px; }
    .form-label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }
    .form-control {
      width: 100%;
      padding: 10px 13px;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-size: 13px;
      color: var(--text);
      background: white;
      outline: none;
      transition: border-color 0.18s, box-shadow 0.18s;
      font-family: inherit;
    }
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(5,150,105,0.12);
    }
    .form-hint { font-size: 11px; color: var(--muted); margin-top: 4px; }
    .form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    /* ===== TABLE ===== */
    .table-responsive { overflow-x: auto; }
    .table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
      white-space: nowrap;
    }
    .table th {
      background: #F8FAFC;
      padding: 11px 14px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      color: var(--muted);
      border-bottom: 1.5px solid var(--border);
      text-align: left;
      letter-spacing: 0.4px;
    }
    .table td {
      padding: 13px 14px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }
    .table tr:last-child td { border-bottom: none; }
    .table tr:hover td { background: #F8FAFC; }

    /* ===== BADGES / STATUS ===== */
    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .badge-pending     { background: #FEF3C7; color: #92400E; }
    .badge-disetujui   { background: #D1FAE5; color: #065F46; }
    .badge-ditolak     { background: #FEE2E2; color: #991B1B; }
    .badge-dikembalikan{ background: #DBEAFE; color: #1E40AF; }
    .badge-green       { background: #D1FAE5; color: #065F46; }
    .badge-gray        { background: #F1F5F9; color: #475569; }

    /* ===== ALERTS ===== */
    .alert {
      display: flex; align-items: flex-start; gap: 10px;
      padding: 13px 16px;
      border-radius: 10px;
      font-size: 13px;
      margin-bottom: 20px;
    }
    .alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
    .alert-danger  { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
    .alert-info    { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
    .alert-warning { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }

    /* ===== STAT CARDS ===== */
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .stat-num  { font-size: 24px; font-weight: 800; line-height: 1; }
    .stat-lbl  { font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 600; margin-top: 3px; letter-spacing: 0.3px; }

    /* ===== FILTER TABS ===== */
    .filter-tabs { display: flex; gap: 6px; margin-bottom: 18px; flex-wrap: wrap; }
    .filter-tab {
      padding: 7px 14px;
      border-radius: 7px;
      font-size: 12px; font-weight: 600;
      text-decoration: none;
      border: 1.5px solid var(--border);
      background: white; color: var(--muted);
      transition: all 0.15s;
    }
    .filter-tab:hover { border-color: var(--primary); color: var(--primary); }
    .filter-tab.active { background: var(--primary); color: white; border-color: var(--primary); }

    /* ===== MODAL ===== */
    .modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 2000;
      align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
      background: white; border-radius: 14px;
      width: 100%; max-width: 540px;
      padding: 28px; margin: 16px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.2);
      animation: slideIn 0.2s ease;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateY(-16px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .modal-title   { font-size: 17px; font-weight: 700; color: var(--secondary); margin-bottom: 18px; }
    .modal-actions { display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end; }

    /* ===== FOOTER ===== */
    .footer {
      background: white;
      border-top: 1px solid var(--border);
      padding: 18px 28px;
      text-align: center;
      font-size: 11.5px;
      color: var(--muted);
      margin-top: auto;
    }

    /* ===== PRINT STYLES ===== */
    @media print {
      .navbar, .top-bar, .footer, .no-print, .btn, .filter-tabs, .modal-overlay { display: none !important; }
      body { background: white; font-size: 12px; }
      .page-wrap { margin: 0; padding: 0; max-width: 100%; }
      .card { box-shadow: none; border: 1px solid #ccc; }
      .table th, .table td { padding: 7px 10px; }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .stat-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
      .top-bar { display: none; }
      .navbar  { padding: 10px 14px; }
      .page-wrap { padding: 0 10px; margin: 14px auto; }
      .form-row { grid-template-columns: 1fr; }
      .stat-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) {
      .stat-grid { grid-template-columns: 1fr; }
    }

    /* ===== SWEETALERT2 INVENTRA STYLING ===== */
    .swal2-container {
      z-index: 99999 !important;
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
    }
    .swal2-backdrop-show {
      background: rgba(15, 23, 42, 0.55) !important;
    }
    .swal2-popup.inventra-swal-popup {
      border-radius: 20px !important;
      padding: 28px 24px 24px !important;
      box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35), 0 0 0 1px rgba(226, 232, 240, 0.8) !important;
      background: #FFFFFF !important;
      width: 440px !important;
      max-width: 90vw !important;
    }
    .swal2-icon {
      margin: 8px auto 14px !important;
      transform: scale(0.92);
      border-width: 3px !important;
    }
    .swal2-icon.swal2-question {
      border-color: #059669 !important;
      color: #059669 !important;
    }
    .swal2-icon.swal2-warning {
      border-color: #EF4444 !important;
      color: #EF4444 !important;
    }
    .swal2-icon.swal2-info {
      border-color: #0284C7 !important;
      color: #0284C7 !important;
    }
    .swal2-icon.swal2-success {
      border-color: #059669 !important;
      color: #059669 !important;
    }
    .swal2-title.inventra-swal-title {
      font-size: 20px !important;
      font-weight: 700 !important;
      color: #0F172A !important;
      padding: 0 !important;
      margin: 0 0 8px 0 !important;
      line-height: 1.35 !important;
    }
    .swal2-html-container.inventra-swal-text {
      font-size: 13.5px !important;
      color: #64748B !important;
      line-height: 1.6 !important;
      margin: 0 0 20px 0 !important;
      padding: 0 4px !important;
    }
    .swal2-actions {
      display: flex !important;
      gap: 12px !important;
      margin-top: 10px !important;
      width: 100% !important;
      justify-content: center !important;
    }
    .swal2-actions button {
      border-radius: 10px !important;
      font-size: 13.5px !important;
      font-weight: 600 !important;
      padding: 10px 22px !important;
      font-family: 'Poppins', sans-serif !important;
      box-shadow: none !important;
      transition: all 0.2s ease !important;
      cursor: pointer !important;
      outline: none !important;
    }
    .inventra-swal-confirm {
      border: none !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
    }
    .inventra-swal-confirm:hover {
      filter: brightness(0.92);
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.16) !important;
    }
    .inventra-swal-cancel {
      background: #F1F5F9 !important;
      color: #475569 !important;
      border: 1px solid #CBD5E1 !important;
    }
    .inventra-swal-cancel:hover {
      background: #E2E8F0 !important;
      color: #1E293B !important;
    }


  </style>
  @yield('styles')
</head>
<body>

<!-- Navbar Dinamis -->
<nav class="navbar">
  <a href="{{ route('landing') }}" class="brand-wrap">
    @if(file_exists(public_path('images/logo.png')))
      <img src="{{ asset('images/logo.png') }}" alt="Logo SMK Negeri 5 Surakarta" class="brand-logo-img">
    @else
      <div class="brand-logo-fallback">IV</div>
    @endif
    <div>
      <div class="brand-name">INVENTRA</div>
      <div class="brand-sub">SMK NEGERI 5 SURAKARTA</div>
    </div>
  </a>

  <div class="nav-links">
    <a href="{{ route('landing') }}"  class="{{ request()->routeIs('landing') ? 'active' : '' }}">Home</a>
    <a href="{{ route('katalog') }}"  class="{{ request()->routeIs('katalog') ? 'active' : '' }}">Katalog</a>

    @auth
      @if(in_array(Auth::user()->role, ['admin', 'petugas']))
        <a href="{{ route('dashboard') }}"        class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.tools.index') }}" class="{{ request()->routeIs('admin.tools*') ? 'active' : '' }}">Kelola Barang</a>
        @if(Auth::user()->role === 'admin')
          <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">Kelola User</a>
        @endif
        <a href="{{ route('admin.rekap') }}"       class="{{ request()->routeIs('admin.rekap*') ? 'active' : '' }}">Rekap &amp; Cetak</a>
      @else
        <a href="{{ route('ajukan') }}"       class="{{ request()->routeIs('ajukan*') ? 'active' : '' }}">Ajukan Pinjam</a>
        <a href="{{ route('riwayat.saya') }}" class="{{ request()->routeIs('riwayat.saya*') ? 'active' : '' }}">Peminjaman Saya</a>
      @endif

      <div class="nav-user">
        <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div>
          <div class="user-name">{{ Auth::user()->name }}</div>
          <span class="role-chip {{ in_array(Auth::user()->role, ['admin', 'petugas']) ? 'chip-admin' : 'chip-siswa' }}">{{ Auth::user()->role }}</span>
        </div>
      </div>

      <form action="{{ route('logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-sm" style="background:rgba(0,0,0,0.2); color:white; border:1px solid rgba(255,255,255,0.2);">
          Keluar
        </button>
      </form>
    @else
      <a href="{{ route('ajukan') }}" class="{{ request()->routeIs('ajukan*') ? 'active' : '' }}">Ajukan Pinjam</a>
      <a href="{{ route('login') }}"    class="btn btn-sm btn-outline-green" style="background:rgba(255,255,255,0.1); color:white; border-color:rgba(255,255,255,0.4);">Masuk</a>
      <a href="{{ route('register') }}" class="btn btn-sm" style="background:rgba(0,0,0,0.2); color:white;">Daftar</a>
    @endauth
  </div>
</nav>

<!-- Konten Utama -->
<div class="page-wrap">

  {{-- Alert Messages --}}
  @if(session('success'))
    <div class="alert alert-success">
      <span>✓</span><div>{!! session('success') !!}</div>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">
      <span>✕</span><div>{!! session('error') !!}</div>
    </div>
  @endif
  @if(session('info'))
    <div class="alert alert-info">
      <span>ℹ</span><div>{!! session('info') !!}</div>
    </div>
  @endif
  @if(session('warning'))
    <div class="alert alert-warning">
      <span>⚠</span><div>{!! session('warning') !!}</div>
    </div>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="alert alert-danger" style="flex-direction:column; align-items:flex-start;">
      <strong>Ada kesalahan pada pengisian form:</strong>
      <ul style="margin-top:6px; padding-left:18px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  @yield('content')
</div>

<!-- Footer -->
<footer class="footer">
  <div>&copy; 2026 <strong>INVENTRA</strong> — Sistem Peminjaman Inventaris SMK Negeri 5 Surakarta</div>
</footer>

<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
  // Fallback to CDN if local script is missing
  if (typeof Swal === 'undefined') {
    const fallbackScript = document.createElement('script');
    fallbackScript.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    document.head.appendChild(fallbackScript);
  }

  // Modal open/close helper
  function openModal(id)  { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }
  document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
  });

  // Global SweetAlert2 Confirmation for Forms
  document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form || !form.dataset.confirm) return;

    if (form.dataset.confirmed === 'true') {
      // Allow the submission
      return;
    }

    e.preventDefault();

    const title = form.dataset.confirmTitle || 'Konfirmasi Tindakan';
    const text = form.dataset.confirm;
    const type = form.dataset.confirmType || 'warning'; // 'success', 'danger', 'info', 'warning'
    const btnText = form.dataset.confirmBtn || 'Ya, Lanjutkan';

    let confirmColor = '#059669'; // Emerald default
    let icon = 'question';

    if (type === 'danger') {
      confirmColor = '#EF4444';
      icon = 'warning';
    } else if (type === 'success') {
      confirmColor = '#059669';
      icon = 'question';
    } else if (type === 'info') {
      confirmColor = '#0284C7';
      icon = 'info';
    } else if (type === 'warning') {
      confirmColor = '#F59E0B';
      icon = 'warning';
    }

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: btnText,
        cancelButtonText: 'Batal',
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#64748B',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
          popup: 'inventra-swal-popup',
          title: 'inventra-swal-title',
          htmlContainer: 'inventra-swal-text',
          confirmButton: 'inventra-swal-confirm',
          cancelButton: 'inventra-swal-cancel',
        }
      }).then((result) => {
        if (result.isConfirmed) {
          form.dataset.confirmed = 'true';
          form.submit();
        }
      });
    } else {
      if (confirm(text)) {
        form.dataset.confirmed = 'true';
        form.submit();
      }
    }
  });
</script>

@yield('scripts')
</body>
</html>
