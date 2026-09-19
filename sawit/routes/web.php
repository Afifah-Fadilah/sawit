<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {

        Route::view('/dashboard', 'admin.dashboard')
            ->name('dashboard');

        Route::view('/kelola-blok', 'admin.kelolablok');

        Route::view('/mandor', 'admin.mandor');

        Route::view('/jenis-pekerjaan', 'admin.jenispekerjaan');

        Route::view('/tarif-upah', 'admin.tarifupah');

        Route::view('/pekerja', 'admin.pekerja');

        Route::view('/hasil-kerja', 'admin.hasilkerja');

        Route::view('/kasbon', 'admin.kasbon');

        Route::view('/laporan-hasil-kerja', 'admin.laporanhasilkerja');

        Route::view('/laporan-upah', 'admin.laporanupah');

        Route::view('/pengaturan', 'admin.pengaturan');
    });