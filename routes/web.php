<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Admin\ToolController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BorrowingController;

// ============================================================
// 1. HALAMAN PUBLIK (Tanpa Auth)
// ============================================================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/katalog', [LandingController::class, 'katalog'])->name('katalog');

// ============================================================
// 2. ALUR PEMINJAMAN SISWA
// ============================================================
Route::get('/ajukan', [LandingController::class, 'ajukan'])->name('ajukan');
Route::post('/ajukan', [LandingController::class, 'storeAjukan'])->name('ajukan.store');
Route::get('/riwayat-saya', [LandingController::class, 'riwayatSaya'])->name('riwayat.saya');

// ============================================================
// 3. AUTENTIKASI (Login, Logout, Register)
// ============================================================
Route::get('/login', [LandingController::class, 'login'])->name('login');
Route::post('/login', [LandingController::class, 'processLogin'])->name('login.process');
Route::post('/logout', [LandingController::class, 'logout'])->name('logout');
Route::get('/logout', [LandingController::class, 'logout']); // Fallback GET

Route::get('/register', [LandingController::class, 'register'])->name('register');
Route::post('/register', [LandingController::class, 'processRegister'])->name('register.process');

// ============================================================
// 4. DASHBOARD ADMIN UTAMA (via LandingController)
// ============================================================
Route::get('/dashboard', [LandingController::class, 'dashboard'])->name('dashboard');

// Legacy borrowing routes (proxied ke BorrowingController baru)
Route::post('/borrowing/verify/{id}', [BorrowingController::class, 'verify'])->name('borrowing.verify');
Route::post('/borrowing/return/{id}', [BorrowingController::class, 'processReturn'])->name('borrowing.return');
Route::delete('/borrowing/{id}', [BorrowingController::class, 'destroy'])->name('borrowing.destroy');

// ============================================================
// 5. ADMIN — KELOLA KATALOG BARANG (CRUD Tools)
// ============================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // Katalog Barang
    Route::get('/barang', [ToolController::class, 'index'])->name('tools.index');
    Route::post('/barang', [ToolController::class, 'store'])->name('tools.store');
    Route::get('/barang/{tool}/edit', [ToolController::class, 'edit'])->name('tools.edit');
    Route::put('/barang/{tool}', [ToolController::class, 'update'])->name('tools.update');
    Route::delete('/barang/{tool}', [ToolController::class, 'destroy'])->name('tools.destroy');

    // Manajemen User
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Rekap Peminjaman & Cetak / Export CSV
    Route::get('/rekap', [BorrowingController::class, 'rekap'])->name('rekap');
    Route::get('/rekap/export', [BorrowingController::class, 'exportCsv'])->name('rekap.export');
});
