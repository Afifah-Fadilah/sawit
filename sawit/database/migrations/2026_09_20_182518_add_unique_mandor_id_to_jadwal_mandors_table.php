<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_mandors', function (Blueprint $table) {
            $table->unique('mandor_id');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_mandors', function (Blueprint $table) {
            $table->dropUnique(['mandor_id']);
        });
    }
};