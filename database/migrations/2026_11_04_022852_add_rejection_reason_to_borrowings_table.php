<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom rejection_reason ke tabel borrowings.
     * Catatan: alasan_penolakan sudah ada, kolom ini ditambahkan sebagai alias
     * standar agar kompatibel dengan kebutuhan baru.
     */
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Hanya tambahkan jika belum ada (alasan_penolakan sudah ada dari migrasi sebelumnya)
            if (!Schema::hasColumn('borrowings', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('alasan_penolakan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            if (Schema::hasColumn('borrowings', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
