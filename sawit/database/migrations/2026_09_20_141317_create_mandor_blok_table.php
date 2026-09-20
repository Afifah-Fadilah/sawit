<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mandor_blok', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mandor_id')
                ->constrained('mandors')
                ->onDelete('cascade');

            $table->foreignId('blok_id')
                ->constrained('blok')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['mandor_id', 'blok_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mandor_blok');
    }
};