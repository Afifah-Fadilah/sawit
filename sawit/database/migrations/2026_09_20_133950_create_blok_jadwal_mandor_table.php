<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blok_jadwal_mandor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jadwal_mandor_id')
                ->constrained('jadwal_mandors')
                ->onDelete('cascade');

            $table->foreignId('blok_id')
                ->constrained('blok')
                ->onDelete('cascade');

            $table->unique(['jadwal_mandor_id', 'blok_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blok_jadwal_mandor');
    }
};