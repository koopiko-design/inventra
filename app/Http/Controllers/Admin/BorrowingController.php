<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    /**
     * Proteksi akses: Admin / Petugas.
     */
    private function ensureAdmin()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'petugas'])) {
            abort(403, 'Akses ditolak. Hanya Administrator / Petugas Sarpras yang berwenang.');
        }
    }

    /**
     * Halaman rekap peminjaman (dengan filter).
     */
    public function rekap(Request $request)
    {
        $this->ensureAdmin();

        $query = Borrowing::with(['tool.category', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tgl_pinjam', [
                Carbon::parse($request->dari)->startOfDay(),
                Carbon::parse($request->sampai)->endOfDay(),
            ]);
        }

        $borrowings = $query->get();

        $stats = [
            'total'       => $borrowings->count(),
            'pending'     => $borrowings->where('status', 'pending')->count(),
            'disetujui'   => $borrowings->where('status', 'disetujui')->count(),
            'ditolak'     => $borrowings->where('status', 'ditolak')->count(),
            'dikembalikan'=> $borrowings->where('status', 'dikembalikan')->count(),
        ];

        $printDate = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY');

        return view('admin.rekap', compact('borrowings', 'stats', 'printDate'));
    }

    /**
     * Export data rekap peminjaman ke format CSV / Excel.
     */
    public function exportCsv(Request $request)
    {
        $this->ensureAdmin();

        $query = Borrowing::with(['tool.category', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tgl_pinjam', [
                Carbon::parse($request->dari)->startOfDay(),
                Carbon::parse($request->sampai)->endOfDay(),
            ]);
        }

        $borrowings = $query->get();

        $filename = 'rekap-peminjaman-inventra-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($borrowings) {
            $file = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar terbaca sempurna di Microsoft Excel / Calc / Spreadsheet
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom CSV
            fputcsv($file, [
                'No',
                'Nama Peminjam',
                'Kelas / NIS',
                'Kode Alat',
                'Nama Barang / Alat',
                'Kategori',
                'Jumlah Unit',
                'Tanggal Pinjam',
                'Rencana Kembali',
                'Tanggal Dikembalikan',
                'Status Peminjaman',
                'Tujuan / Alasan Peminjaman',
                'Alasan Penolakan',
                'Kondisi Pengembalian',
            ]);

            foreach ($borrowings as $index => $b) {
                fputcsv($file, [
                    $index + 1,
                    $b->nama_peminjam,
                    $b->kelas_nis,
                    $b->tool->kode_alat ?? '-',
                    $b->tool->nama_alat ?? '-',
                    $b->tool->category->nama_kategori ?? '-',
                    $b->jumlah_unit,
                    $b->tgl_pinjam,
                    $b->tgl_kembali,
                    $b->tgl_dikembalikan ?? '-',
                    strtoupper($b->status),
                    $b->alasan_pinjam,
                    $b->rejection_reason ?? ($b->alasan_penolakan ?? '-'),
                    $b->kondisi_kembali ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Verifikasi / Approval pengajuan peminjaman.
     * Mendukung aksi 'approve' dan 'reject' (dengan alasan penolakan).
     */
    public function verify(Request $request, $id)
    {
        $this->ensureAdmin();

        $borrowing = Borrowing::findOrFail($id);
        $action = $request->input('action');

        if ($action === 'approve') {
            $tool = $borrowing->tool;
            if (!$tool || $tool->stok < $borrowing->jumlah_unit) {
                $sisa = $tool ? $tool->stok : 0;
                return back()->with('error', "Stok '{$tool->nama_alat}' tidak mencukupi (tersisa {$sisa} unit, diajukan {$borrowing->jumlah_unit} unit).");
            }

            $tool->decrement('stok', $borrowing->jumlah_unit);
            $tool->update(['status' => $tool->fresh()->stok <= 0 ? 'habis' : 'tersedia']);
            $borrowing->update(['status' => 'disetujui']);

            return back()->with('success', "Pengajuan atas nama <strong>{$borrowing->nama_peminjam}</strong> telah DISETUJUI. Stok '{$tool->nama_alat}' berkurang {$borrowing->jumlah_unit} unit.");
        }

        if ($action === 'reject') {
            $request->validate([
                'alasan_penolakan' => 'required|string|max:500',
            ], [
                'alasan_penolakan.required' => 'Alasan penolakan wajib diisi saat menolak pengajuan.',
            ]);

            $borrowing->update([
                'status'           => 'ditolak',
                'alasan_penolakan' => $request->alasan_penolakan,
                'rejection_reason' => $request->alasan_penolakan, // Kolom baru
            ]);

            return back()->with('info', "Pengajuan atas nama <strong>{$borrowing->nama_peminjam}</strong> telah DITOLAK.");
        }

        return back();
    }

    /**
     * Konfirmasi pengembalian barang.
     */
    public function processReturn(Request $request, $id)
    {
        $this->ensureAdmin();

        $borrowing = Borrowing::findOrFail($id);

        if ($borrowing->status !== 'disetujui') {
            return back()->with('error', 'Hanya peminjaman berstatus "Disetujui" yang bisa diproses pengembaliannya.');
        }

        $tool = $borrowing->tool;
        if ($tool) {
            $tool->increment('stok', $borrowing->jumlah_unit);
            $tool->update(['status' => 'tersedia']);
        }

        $borrowing->update([
            'status'           => 'dikembalikan',
            'tgl_dikembalikan' => Carbon::now(),
            'kondisi_kembali'  => $request->input('kondisi_kembali', 'Baik dan lengkap'),
        ]);

        return back()->with('success', "Pengembalian barang atas nama <strong>{$borrowing->nama_peminjam}</strong> berhasil dikonfirmasi.");
    }

    /**
     * Hapus data transaksi peminjaman.
     */
    public function destroy($id)
    {
        $this->ensureAdmin();

        $borrowing = Borrowing::findOrFail($id);
        $nama = $borrowing->nama_peminjam;
        $borrowing->delete();

        return back()->with('success', "Data transaksi peminjaman atas nama '{$nama}' berhasil dihapus.");
    }
}
