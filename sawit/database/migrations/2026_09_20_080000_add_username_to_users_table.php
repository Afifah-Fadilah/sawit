<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kolom `role` TIDAK ditambahkan di sini karena sudah ada
     * di migration create_users_table (default 'mandor').
     *
     * Kolom `username` dipisah dari `name` supaya:
     * - `name`     = nama lengkap akun (misal nama admin, atau bisa sama dengan nama mandor)
     * - `username` = handle unik untuk login (khusus dipakai fitur "Kelola Akun" mandor)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};