<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_kerjas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pekerja_id')->constrained('pekerjas')->cascadeOnDelete();
            $table->foreignId('blok_id')->constrained('blok')->cascadeOnDelete();
            $table->foreignId('jenis_pekerjaan_id')->constrained('jenis_pekerjaans')->cascadeOnDelete();
            $table->foreignId('mandor_id')->constrained('mandors')->cascadeOnDelete();

            $table->date('tanggal');
            $table->decimal('jumlah', 10, 2);
            $table->string('keterangan')->nullable();

            $table->timestamps();

            // Satu pekerja hanya boleh diinput sekali per hari
            $table->unique(['pekerja_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_kerjas');
    }
};