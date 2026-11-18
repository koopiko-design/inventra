<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\Category;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LandingController extends Controller
{
    /**
     * Halaman Utama (Landing Page)
     */
    public function index()
    {
        $availableTools = Tool::where('status', 'tersedia')->where('stok', '>', 0)->count();
        $borrowedTools = Borrowing::where('status', 'disetujui')->count();
        $totalTransactions = Borrowing::count();
        $recentTools = Tool::with('category')->latest()->take(4)->get();

        return view('landing', compact('availableTools', 'borrowedTools', 'totalTransactions', 'recentTools'));
    }

    /**
     * Katalog Barang Inventaris Sekolah
     */
    public function katalog(Request $request)
    {
        $query = Tool::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'like', "%{$search}%")
                  ->orWhere('kode_alat', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $tools = $query->orderBy('nama_alat', 'asc')->get();
        $categories = Category::all();

        return view('katalog', compact('tools', 'categories'));
    }

    /**
     * Form Pengajuan Peminjaman Alat (Siswa)
     * Siswa dapat memilih tanggal peminjaman, tgl_kembali otomatis +3 hari
     */
    public function ajukan(Request $request)
    {
        $namaAlat = $request->query('nama', '');
        $toolId = $request->query('tool_id');
        $selectedTool = $toolId ? Tool::find($toolId) : null;
        $tools = Tool::where('stok', '>', 0)->orderBy('nama_alat', 'asc')->get();

        // Default tanggal peminjaman (hari ini) dan estimasi kembali (+3 hari)
        $tglPinjam   = Carbon::now()->format('Y-m-d');
        $tglKembali  = Carbon::now()->addDays(3)->format('Y-m-d');

        return view('ajukan', compact('namaAlat', 'toolId', 'selectedTool', 'tools', 'tglPinjam', 'tglKembali'));
    }

    /**
     * Simpan Pengajuan Peminjaman Alat
     * Jumlah unit dikunci = 1, tgl_pinjam dipilih siswa, tgl_kembali otomatis 3 hari setelah tgl_pinjam
     */
    public function storeAjukan(Request $request)
    {
        $request->validate([
            'tool_id'       => 'required|exists:tools,id',
            'nama_peminjam' => 'required|string|max:255',
            'kelas_nis'     => 'required|string|max:255',
            'tgl_pinjam'    => 'required|date',
            'alasan_pinjam' => 'required|string',
        ], [
            'tool_id.required'       => 'Silakan pilih alat inventaris yang ingin dipinjam.',
            'tool_id.exists'         => 'Alat inventaris yang dipilih tidak valid.',
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'kelas_nis.required'     => 'Kelas dan NIS wajib diisi.',
            'tgl_pinjam.required'    => 'Tanggal peminjaman wajib diisi.',
            'alasan_pinjam.required' => 'Tujuan / keperluan peminjaman wajib diisi.',
        ]);

        $tool = Tool::findOrFail($request->tool_id);

        // Jumlah unit selalu 1 (dikunci di backend)
        $jumlahUnit = 1;

        if ($tool->stok < $jumlahUnit) {
            return back()->withInput()->with('error', "Stok barang '{$tool->nama_alat}' tidak mencukupi. Sisa stok: {$tool->stok} unit.");
        }

        // Tanggal pinjam dari input siswa, tanggal kembali otomatis dihitung 3 hari setelah tgl_pinjam menggunakan Carbon
        $tglPinjam  = Carbon::parse($request->tgl_pinjam);
        $tglKembali = (clone $tglPinjam)->addDays(3);

        Borrowing::create([
            'user_id'       => Auth::id(),
            'tool_id'       => $tool->id,
            'nama_peminjam' => $request->nama_peminjam,
            'kelas_nis'     => $request->kelas_nis,
            'jumlah_unit'   => $jumlahUnit,
            'tgl_pinjam'    => $tglPinjam->format('Y-m-d'),
            'tgl_kembali'   => $tglKembali->format('Y-m-d'),
            'alasan_pinjam' => $request->alasan_pinjam,
            'status'        => 'pending',
        ]);

        if (Auth::check() && Auth::user()->role === 'siswa') {
            return redirect()->route('riwayat.saya')->with('success', "Pengajuan peminjaman '{$tool->nama_alat}' berhasil dikirim! Tanggal Pinjam: {$tglPinjam->format('d M Y')}, Rencana Kembali: {$tglKembali->format('d M Y')}. Menunggu verifikasi Admin Sarpras.");
        }

        return redirect()->route('landing')->with('success', "Pengajuan peminjaman '{$tool->nama_alat}' berhasil dikirim! Tanggal Pinjam: {$tglPinjam->format('d M Y')}, Rencana Kembali: {$tglKembali->format('d M Y')}.");
    }

    /**
     * Riwayat Peminjaman Khusus Siswa Login
     */
    public function riwayatSaya()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk melihat riwayat peminjaman.');
        }

        $user = Auth::user();
        $borrowings = Borrowing::with('tool')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('kelas_nis', 'like', "%{$user->nis}%");
            })
            ->latest()
            ->get();

        return view('riwayat_saya', compact('borrowings'));
    }

    /**
     * Dashboard Admin & Rekapitulasi Peminjaman
     */
    public function dashboard(Request $request)
    {
        // Proteksi RBAC: Khusus Admin / Petugas
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'petugas'])) {
            return redirect()->route('login')->with('error', 'Akses ditolak. Halaman Dashboard hanya dapat diakses oleh Administrator / Petugas Sarpras.');
        }

        $statusFilter = $request->query('status');

        $query = Borrowing::with(['tool', 'user'])->latest();

        if ($statusFilter && in_array($statusFilter, ['pending', 'disetujui', 'ditolak', 'dikembalikan'])) {
            $query->where('status', $statusFilter);
        }

        $borrowings = $query->get();

        // Statistik ringkas dashboard
        $totalAlat = Tool::count();
        $pendingCount = Borrowing::where('status', 'pending')->count();
        $approvedCount = Borrowing::where('status', 'disetujui')->count();
        $returnedCount = Borrowing::where('status', 'dikembalikan')->count();

        $tools = Tool::with('category')->orderBy('nama_alat', 'asc')->get();
        $categories = Category::all();

        return view('dashboard', compact(
            'borrowings', 
            'totalAlat', 
            'pendingCount', 
            'approvedCount', 
            'returnedCount', 
            'tools', 
            'categories',
            'statusFilter'
        ));
    }

    /**
     * Verifikasi & Persetujuan Peminjaman oleh Admin
     */
    public function verifyApproval(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Aksi tidak diizinkan. Hanya Admin yang dapat memverifikasi peminjaman.');
        }

        $borrowing = Borrowing::findOrFail($id);
        $action = $request->input('action');

        if ($action === 'approve') {
            $tool = $borrowing->tool;
            if ($tool && $tool->stok >= $borrowing->jumlah_unit) {
                // Potong stok alat
                $tool->decrement('stok', $borrowing->jumlah_unit);
                if ($tool->stok <= 0) {
                    $tool->update(['status' => 'habis']);
                }
                $borrowing->update(['status' => 'disetujui']);
                return back()->with('success', "Pengajuan atas nama {$borrowing->nama_peminjam} telah DISETUJUI. Stok '{$tool->nama_alat}' otomatis berkurang {$borrowing->jumlah_unit} unit.");
            }
            return back()->with('error', "Stok barang '{$tool->nama_alat}' tidak mencukupi untuk disetujui (Sisa: {$tool->stok} unit).");
        } elseif ($action === 'reject') {
            $alasan = $request->input('alasan_penolakan', 'Pengajuan belum memenuhi syarat / jadwal bentrok.');
            $borrowing->update([
                'status' => 'ditolak',
                'alasan_penolakan' => $alasan
            ]);
            return back()->with('info', "Pengajuan atas nama {$borrowing->nama_peminjam} telah DITOLAK.");
        }

        return back();
    }

    /**
     * Konfirmasi Pengembalian Barang oleh Admin
     */
    public function processReturn(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Aksi tidak diizinkan. Hanya Admin yang dapat memproses pengembalian.');
        }

        $borrowing = Borrowing::findOrFail($id);

        if ($borrowing->status === 'disetujui') {
            $tool = $borrowing->tool;
            if ($tool) {
                // Tambahkan kembali stok barang
                $tool->increment('stok', $borrowing->jumlah_unit);
                $tool->update(['status' => 'tersedia']);
            }
            $borrowing->update([
                'status' => 'dikembalikan',
                'tgl_dikembalikan' => now(),
                'kondisi_kembali' => $request->input('kondisi_kembali', 'Lengkap dan berfungsi baik')
            ]);
            return back()->with('success', "Pengembalian barang atas nama {$borrowing->nama_peminjam} berhasil dikonfirmasi. Stok '{$tool->nama_alat}' kembali tersedia.");
        }

        return back()->with('error', 'Hanya pengajuan dengan status disetujui yang dapat diproses pengembaliannya.');
    }

    /**
     * Hapus Data Transaksi Peminjaman oleh Admin
     */
    public function destroyBorrowing($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login')->with('error', 'Aksi tidak diizinkan.');
        }

        $borrowing = Borrowing::findOrFail($id);
        $borrowing->delete();

        return back()->with('success', 'Data transaksi peminjaman berhasil dihapus.');
    }

    /**
     * Tampilan Halaman Login
     */
    public function login()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin' 
                ? redirect()->route('dashboard') 
                : redirect()->route('landing');
        }

        return view('auth.login');
    }

    /**
     * Proses Login Pengguna (Admin / Siswa)
     */
    public function processLogin(Request $request)
    {
        $request->validate([
            'role'     => 'required|in:admin,petugas,siswa,Admin,Petugas,Siswa',
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'role.required'     => 'Pilihan peran / role wajib ditentukan.',
            'username.required' => 'NIS / Username / Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $selectedRole = strtolower($request->input('role'));
        $usernameInput = trim($request->input('username'));
        $passwordInput = $request->input('password');

        // Cari pengguna berdasarkan NIS atau Email
        $user = User::where(function ($q) use ($usernameInput) {
            $q->where('nis', $usernameInput)
              ->orWhere('email', $usernameInput);
        })->first();

        if (!$user || !Hash::check($passwordInput, $user->password)) {
            return back()->withInput()->with('error', 'Kombinasi NIS / Username dan Kata Sandi tidak sesuai.');
        }

        // Validasi kesesuaian role yang dipilih
        if (strtolower($user->role) !== $selectedRole) {
            $roleLabel = ucfirst($user->role);
            return back()->withInput()->with('error', "Akun ini terdaftar sebagai {$roleLabel}. Silakan pilih role yang sesuai pada form masuk.");
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        session(['role' => $user->role]);

        if (in_array($user->role, ['admin', 'petugas'])) {
            return redirect()->route('dashboard')->with('success', 'Selamat datang, ' . $user->name . '! Anda masuk sebagai ' . ucfirst($user->role) . ' Sarpras.');
        }

        return redirect()->route('landing')->with('success', 'Selamat datang, ' . $user->name . '! Anda masuk sebagai Siswa.');
    }

    /**
     * Proses Keluar (Logout)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'Anda telah berhasil keluar (logout) dari sistem INVENTRA.');
    }

    /**
     * Tampilan Form Registrasi Akun Siswa Baru
     */
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('landing');
        }

        return view('auth.register');
    }

    /**
     * Proses Pendaftaran Akun Siswa Baru
     */
    public function processRegister(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'nis'                   => 'required|string|max:50|unique:users,nis',
            'email'                 => 'nullable|email|max:255|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
        ], [
            'name.required'         => 'Nama lengkap wajib diisi.',
            'nis.required'          => 'NIS siswa wajib diisi.',
            'nis.unique'            => 'NIS ini sudah terdaftar di sistem. Silakan gunakan NIS lain atau masuk ke akun Anda.',
            'email.unique'          => 'Alamat email ini sudah terdaftar.',
            'password.required'     => 'Kata sandi wajib diisi.',
            'password.min'          => 'Kata sandi minimal berjumlah 6 karakter.',
            'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $email = $request->filled('email') 
            ? $request->email 
            : ($request->nis . '@siswa.inventra.test');

        $user = User::create([
            'name'     => $request->name,
            'nis'      => $request->nis,
            'email'    => $email,
            'password' => Hash::make($request->password),
            'role'     => 'siswa', // role default siswa
        ]);

        // Otomatis login akun baru
        Auth::login($user);
        $request->session()->regenerate();
        session(['role' => 'siswa']);

        return redirect()->route('landing')->with('success', 'Pendaftaran berhasil! Selamat datang di INVENTRA, ' . $user->name . '.');
    }
}
