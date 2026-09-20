<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlokController;
use App\Http\Controllers\Admin\PekerjaController;
use App\Http\Controllers\Admin\JenisPekerjaanController;
use App\Http\Controllers\Admin\MandorController;
use App\Http\Controllers\Admin\JadwalMandorController;
use App\Http\Controllers\Mandor\DashboardController as MandorDashboardController;
use App\Http\Controllers\Admin\TarifUpahController;
use App\Http\Controllers\Mandor\InputDataController;
use App\Http\Controllers\Mandor\RiwayatController;

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

        Route::resource('/tarif-upah', TarifUpahController::class)
        ->only(['index', 'store', 'update', 'destroy']);

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

        Route::get('/dashboard', [MandorDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/input-data-harian', [InputDataController::class, 'index'])
    ->name('input');

Route::post('/input-data-harian', [InputDataController::class, 'store'])
    ->name('input.store');

        Route::get('/riwayat', [RiwayatController::class, 'index'])
            ->name('riwayat');

        Route::get('/riwayat/unduh', [RiwayatController::class, 'unduh'])
            ->name('riwayat.unduh');

        Route::view('/akun', 'mandor.akun')
            ->name('akun');
    });