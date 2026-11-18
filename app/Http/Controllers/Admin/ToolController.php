<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{
    /**
     * Proteksi akses: hanya Admin.
     */
    private function ensureAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Administrator yang dapat mengakses halaman ini.');
        }
    }

    /**
     * Kelola Katalog Barang (CRUD) — tampil di dashboard sebagai partial.
     * Ini diakses via dashboard#kelola-barang, bukan halaman terpisah.
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $query = Tool::with('category');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama_alat', 'like', "%{$q}%")
                   ->orWhere('kode_alat', 'like', "%{$q}%");
            });
        }

        $tools = $query->orderBy('nama_alat')->paginate(10)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get();

        return view('admin.tools.index', compact('tools', 'categories'));
    }

    /**
     * Simpan barang baru ke katalog inventaris.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'kode_alat'   => 'required|string|max:50|unique:tools,kode_alat',
            'nama_alat'   => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'deskripsi'   => 'nullable|string',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ], [
            'kode_alat.required' => 'Kode alat wajib diisi.',
            'kode_alat.unique'   => 'Kode alat ini sudah digunakan oleh barang lain.',
            'nama_alat.required' => 'Nama alat / barang wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'stok.min'           => 'Stok tidak boleh kurang dari 0.',
            'gambar.image'       => 'File yang diupload harus berupa gambar.',
            'gambar.mimes'       => 'Format gambar harus JPEG, PNG, JPG, WEBP, atau GIF.',
            'gambar.max'         => 'Ukuran gambar maksimal 2MB.',
        ]);

        $status = $request->stok > 0 ? 'tersedia' : 'habis';

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('tools', 'public');
        }

        Tool::create([
            'kode_alat'   => strtoupper(trim($request->kode_alat)),
            'nama_alat'   => $request->nama_alat,
            'category_id' => $request->category_id,
            'deskripsi'   => $request->deskripsi,
            'stok'        => $request->stok,
            'status'      => $status,
            'gambar'      => $gambarPath,
        ]);

        return redirect()->route('admin.tools.index')->with('success', "Barang '{$request->nama_alat}' berhasil ditambahkan ke katalog inventaris.");
    }

    /**
     * Tampil form edit barang.
     */
    public function edit(Tool $tool)
    {
        $this->ensureAdmin();
        $categories = Category::orderBy('nama_kategori')->get();
        return view('admin.tools.edit', compact('tool', 'categories'));
    }

    /**
     * Update data barang.
     */
    public function update(Request $request, Tool $tool)
    {
        $this->ensureAdmin();

        $request->validate([
            'kode_alat'   => 'required|string|max:50|unique:tools,kode_alat,' . $tool->id,
            'nama_alat'   => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'deskripsi'   => 'nullable|string',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ], [
            'gambar.image' => 'File yang diupload harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPEG, PNG, JPG, WEBP, atau GIF.',
            'gambar.max'   => 'Ukuran gambar maksimal 2MB.',
        ]);

        $status = $request->stok > 0 ? 'tersedia' : 'habis';

        $data = [
            'kode_alat'   => strtoupper(trim($request->kode_alat)),
            'nama_alat'   => $request->nama_alat,
            'category_id' => $request->category_id,
            'deskripsi'   => $request->deskripsi,
            'stok'        => $request->stok,
            'status'      => $status,
        ];

        if ($request->boolean('hapus_gambar') && $tool->gambar) {
            Storage::disk('public')->delete($tool->gambar);
            $data['gambar'] = null;
        }

        if ($request->hasFile('gambar')) {
            if ($tool->gambar) {
                Storage::disk('public')->delete($tool->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('tools', 'public');
        }

        $tool->update($data);

        return redirect()->route('admin.tools.index')->with('success', "Barang '{$tool->nama_alat}' berhasil diperbarui.");
    }

    /**
     * Hapus barang dari katalog.
     */
    public function destroy(Tool $tool)
    {
        $this->ensureAdmin();

        $namaAlat = $tool->nama_alat;

        // Cek apakah masih ada peminjaman aktif
        $activeBorrowings = $tool->borrowings()->whereIn('status', ['pending', 'disetujui'])->count();
        if ($activeBorrowings > 0) {
            return back()->with('error', "Barang '{$namaAlat}' tidak dapat dihapus karena masih ada transaksi peminjaman aktif ({$activeBorrowings} transaksi).");
        }

        if ($tool->gambar) {
            Storage::disk('public')->delete($tool->gambar);
        }

        $tool->delete();

        return redirect()->route('admin.tools.index')->with('success', "Barang '{$namaAlat}' berhasil dihapus dari katalog inventaris.");
    }
}
