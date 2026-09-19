<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlokController;
use App\Http\Controllers\Admin\JenisPekerjaanController;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__ . '/auth.php';

Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {

        Route::view('/dashboard', 'admin.dashboard')
            ->name('admin.dashboard');

        Route::resource('/blok', BlokController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names('admin.blok');

        Route::view('/mandor', 'admin.mandor');

        Route::resource('/jenis-pekerjaan', JenisPekerjaanController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names('admin.jenis-pekerjaan');

        Route::view('/tarif-upah', 'admin.tarifupah');

        Route::view('/pekerja', 'admin.pekerja');

        Route::view('/hasil-kerja', 'admin.hasilkerja');

        Route::view('/kasbon', 'admin.kasbon');

        Route::view('/laporan-hasil-kerja', 'admin.laporanhasilkerja');

        Route::view('/laporan-upah', 'admin.laporanupah');

        Route::view('/pengaturan', 'admin.pengaturan');
    });

Route::prefix('mandor')
    ->middleware(['auth'])
    ->group(function () {

        Route::view('/dashboard', 'mandor.dashboard')
            ->name('mandor.dashboard');

        Route::view('/input-data-harian', 'mandor.input-data')
            ->name('mandor.input');

        Route::view('/riwayat', 'mandor.riwayat')
            ->name('mandor.riwayat');

        Route::view('/akun', 'mandor.akun')
            ->name('mandor.akun');
    });
