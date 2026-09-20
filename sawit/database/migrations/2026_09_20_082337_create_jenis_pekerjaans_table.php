<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pekerjaans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->enum('jenis', ['Pemanen', 'Pemberondol', 'Pemupuk', 'Penyemprot'])->unique();
            $table->string('satuan');
            $table->text('keterangan')->nullable();
            $table->string('warna', 20)->default('#5f6f52');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pekerjaans');
    }
};