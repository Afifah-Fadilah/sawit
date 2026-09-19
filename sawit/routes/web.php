<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlokController;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::prefix('admin')
    ->middleware(['auth'])
    ->group(function () {

        Route::view('/dashboard', 'admin.dashboard')
            ->name('dashboard');

        Route::resource('/blok', BlokController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names('admin.blok');

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