<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('kode_alat')->unique();
            $table->string('nama_alat');
            $table->text('deskripsi')->nullable();
            $table->integer('stok')->default(0);
            $table->enum('status', ['tersedia', 'stok_rendah', 'habis'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tools');
    }
};