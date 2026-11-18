<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('tool_id')->constrained('tools')->onDelete('cascade');
            $table->string('nama_peminjam');
            $table->string('kelas_nis');
            $table->integer('jumlah_unit')->default(1);
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali');
            $table->date('tgl_dikembalikan')->nullable();
            $table->text('alasan_pinjam');
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dikembalikan'])->default('pending');
            $table->text('alasan_penolakan')->nullable();
            $table->string('kondisi_kembali')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('borrowings');
    }
};