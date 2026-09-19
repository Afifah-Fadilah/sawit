<?php

// database/migrations/2026_09_19_100000_create_blok_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blok', function (Blueprint $table) {
            $table->id();
            $table->string('nama_blok', 100)->unique();      // contoh: Blok 1
            $table->string('afdeling', 100);                 // contoh: Afdeling I
            $table->decimal('luas', 8, 2);                   // dalam hektar, contoh: 18.20
            $table->unsignedSmallInteger('tahun_tanam');     // contoh: 2018
            $table->string('status', 20)->default('aktif');  // aktif / nonaktif
            $table->timestamps();
            $table->index('afdeling');                       // mempercepat filter per afdeling
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blok');
    }
};