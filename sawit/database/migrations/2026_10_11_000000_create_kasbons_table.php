<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kasbons', function (Blueprint $table) {
            $table->id();

            // Peminjam bisa pekerja ATAU mandor (salah satu diisi, satunya null).
            $table->enum('tipe_peminjam', ['pekerja', 'mandor']);
            $table->foreignId('pekerja_id')->nullable()->constrained('pekerjas')->nullOnDelete();
            $table->foreignId('mandor_id')->nullable()->constrained('mandors')->nullOnDelete();

            $table->foreignId('blok_id')->constrained('blok')->cascadeOnDelete();

            $table->date('tanggal_kasbon');
            $table->decimal('nominal_kasbon', 14, 2);
            $table->text('tujuan_kasbon');

            $table->date('tanggal_rencana_potongan');
            $table->decimal('nominal_potongan_per_periode', 14, 2);

            // Akumulasi yang sudah terbayar. 0 sampai kasbon ditandai lunas.
            $table->decimal('nominal_terbayar', 14, 2)->default(0);

            $table->enum('status', ['aktif', 'lunas'])->default('aktif');
            $table->date('tanggal_lunas')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('tanggal_kasbon');
            $table->index('tanggal_rencana_potongan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kasbons');
    }
};