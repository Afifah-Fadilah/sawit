<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pekerjas', function (Blueprint $table) {
            $table->dropColumn('jenis_pekerjaan');
            $table->foreignId('jenis_pekerjaan_id')->nullable()->after('no_hp')
                ->constrained('jenis_pekerjaans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pekerjas', function (Blueprint $table) {
            $table->dropForeign(['jenis_pekerjaan_id']);
            $table->dropColumn('jenis_pekerjaan_id');
            $table->string('jenis_pekerjaan')->nullable();
        });
    }
};