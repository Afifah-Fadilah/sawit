<?php
// database/migrations/2026_09_19_180000_create_jadwal_mandors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_mandors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mandor_id')->constrained('mandors')->onDelete('cascade');
            $table->foreignId('blok_id')->constrained('blok')->onDelete('cascade');

            $table->date('tanggal_mulai');   // awal periode penugasan (misal Senin minggu ini)
            $table->date('tanggal_selesai'); // akhir periode
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_mandors');
    }
};