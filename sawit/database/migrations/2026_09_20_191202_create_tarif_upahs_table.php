<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_upahs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jenis_pekerjaan_id')
                ->unique()
                ->constrained('jenis_pekerjaans')
                ->onDelete('cascade');

            $table->unsignedBigInteger('tarif'); // dalam Rupiah, tanpa desimal
            $table->string('status', 20)->default('aktif'); // aktif / nonaktif

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_upahs');
    }
};