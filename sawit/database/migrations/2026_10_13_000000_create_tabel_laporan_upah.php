<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Header laporan upah per periode
        Schema::create('laporan_upahs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->nullable()->unique();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('keterangan')->nullable();
            $table->string('status', 10)->default('draft'); // draft | final
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });

        // Satu baris = satu slip upah (pekerja atau mandor) dalam satu periode.
        // Nama, kode, rincian & tarif disimpan sebagai snapshot supaya slip lama
        // tidak berubah walau tarif / data master diubah kemudian.
        Schema::create('laporan_upah_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_upah_id')->constrained('laporan_upahs')->cascadeOnDelete();
            $table->string('tipe', 10); // pekerja | mandor
            $table->unsignedBigInteger('pekerja_id')->nullable();
            $table->unsignedBigInteger('mandor_id')->nullable();
            $table->string('kode', 50)->nullable();
            $table->string('nama');
            $table->unsignedBigInteger('upah_kotor')->default(0);
            $table->unsignedBigInteger('potongan_kasbon')->default(0);
            $table->unsignedBigInteger('upah_bersih')->default(0);
            $table->json('rincian')->nullable();
            $table->json('rincian_kasbon')->nullable();
            $table->timestamps();

            $table->index(['laporan_upah_id', 'tipe']);
        });

        // Riwayat potongan kasbon yang benar-benar sudah dipotong saat finalisasi
        Schema::create('kasbon_potongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasbon_id')->constrained('kasbons')->cascadeOnDelete();
            $table->foreignId('laporan_upah_id')->constrained('laporan_upahs')->cascadeOnDelete();
            $table->unsignedBigInteger('laporan_upah_item_id')->nullable();
            $table->unsignedBigInteger('nominal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasbon_potongans');
        Schema::dropIfExists('laporan_upah_items');
        Schema::dropIfExists('laporan_upahs');
    }
};