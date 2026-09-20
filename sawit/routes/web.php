<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlokController;
use App\Http\Controllers\Admin\PekerjaController;
use App\Http\Controllers\Admin\JenisPekerjaanController;
use App\Http\Controllers\Admin\MandorController;
use App\Http\Controllers\Admin\JadwalMandorController;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {

        Route::view('/dashboard', 'admin.dashboard')
            ->name('dashboard');

        Route::resource('/blok', BlokController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // ==== MANDOR ====
        Route::resource('/mandor', MandorController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Route::put('/mandor/{mandor}', [MandorController::class, 'update'])
        //     ->name('mandor.update');

        Route::put('/mandor/{mandor}/akun', [MandorController::class, 'updateAkun'])
            ->name('mandor.akun.update');

        Route::delete('/mandor/{mandor}/akun', [MandorController::class, 'destroyAkun'])
            ->name('mandor.akun.destroy');

        Route::post('/mandor/jadwal', [JadwalMandorController::class, 'store'])
            ->name('mandor.jadwal.store');

        Route::put('/mandor/jadwal/{jadwal}', [JadwalMandorController::class, 'update'])
            ->name('mandor.jadwal.update');

        Route::delete('/mandor/jadwal/{jadwal}', [JadwalMandorController::class, 'destroy'])
            ->name('mandor.jadwal.destroy');
        // ==== END MANDOR ====

        Route::resource('/jenis-pekerjaan', JenisPekerjaanController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::resource('/pekerja', PekerjaController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::view('/tarif-upah', 'admin.tarifupah')
            ->name('tarifupah');

        Route::view('/hasil-kerja', 'admin.hasilkerja')
            ->name('hasilkerja');

        Route::view('/kasbon', 'admin.kasbon')
            ->name('kasbon');

        Route::view('/laporan-hasil-kerja', 'admin.laporanhasilkerja')
            ->name('laporanhasilkerja');

        Route::view('/laporan-upah', 'admin.laporanupah')
            ->name('laporanupah');

        Route::view('/pengaturan', 'admin.pengaturan')
            ->name('pengaturan');
    });

Route::prefix('mandor')
    ->middleware(['auth'])
    ->name('mandor.')
    ->group(function () {

        Route::view('/dashboard', 'mandor.dashboard')
            ->name('dashboard');

        Route::view('/input-data-harian', 'mandor.input-data')
            ->name('input');

        Route::view('/riwayat', 'mandor.riwayat')
            ->name('riwayat');

        Route::view('/akun', 'mandor.akun')
            ->name('akun');
    });