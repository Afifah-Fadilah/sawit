<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_pekerjaans', function (Blueprint $table) {
            $table->string('jenis', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pekerjaans');
    }
};