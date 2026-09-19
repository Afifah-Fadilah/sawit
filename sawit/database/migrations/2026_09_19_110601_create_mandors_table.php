<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mandors', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel users (Foreign Key)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('kode_mandor')->unique(); // Contoh: MDR003
            $table->string('phone');                 // No Telepon Mandor
            $table->string('afdeling');              // Kategori Afdeling (misal: Afdeling 1)
            $table->string('blok_kelola');           // Blok yang dikelola (misal: Blok 1 - 2)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandors');
    }
};
