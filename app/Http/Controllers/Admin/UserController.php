<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Proteksi akses: hanya Admin.
     */
    private function ensureAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Administrator yang dapat mengelola data pengguna.');
        }
    }

    /**
     * Halaman Manajemen User (Daftar semua pengguna).
     */
    public function index(Request $request)
    {
        $this->ensureAdmin();

        $query = User::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('name', 'like', "%{$q}%")
                   ->orWhere('nis', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'name'     => 'required|string|max:255',
            'nis'      => 'nullable|string|max:50|unique:users,nis',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,petugas,siswa',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'nis.unique'        => 'NIS ini sudah digunakan oleh pengguna lain.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email ini sudah terdaftar.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
            'role.required'     => 'Peran (role) wajib dipilih.',
        ]);

        User::create([
            'name'     => $request->name,
            'nis'      => $request->nis ?: null,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna '{$request->name}' berhasil ditambahkan.");
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        $this->ensureAdmin();
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update data user.
     */
    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

        $request->validate([
            'name'     => 'required|string|max:255',
            'nis'      => 'nullable|string|max:50|unique:users,nis,' . $user->id,
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,petugas,siswa',
            'password' => 'nullable|string|min:6',
        ], [
            'nis.unique'    => 'NIS ini sudah digunakan pengguna lain.',
            'email.unique'  => 'Email ini sudah digunakan pengguna lain.',
            'password.min'  => 'Kata sandi baru minimal 6 karakter.',
        ]);

        // Cegah admin me-downgrade dirinya sendiri
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return back()->with('error', 'Anda tidak bisa mengubah role akun Anda sendiri dari Admin.');
        }

        $updateData = [
            'name'  => $request->name,
            'nis'   => $request->nis ?: null,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        $this->ensureAdmin();

        // Cegah admin menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $namaUser = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna '{$namaUser}' berhasil dihapus.");
    }
}
