<?php
// database/migrations/2026_09_19_090000_create_mandors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mandors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('kode_mandor', 20)->unique();   // MDR001, MDR002, ...
            $table->string('nama', 100);
            $table->string('phone', 20);
            $table->string('afdeling', 100);                // contoh: Afdeling I
            $table->string('blok_kelola', 100);              // contoh: Blok 1 - 2 (bebas teks, atau relasi ke blok kalau 1 blok per mandor)
            $table->string('status', 20)->default('aktif');  // aktif / nonaktif
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mandors');
    }
};