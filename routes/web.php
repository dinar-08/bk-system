<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\PushSubscriptionController;

// Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GuruBKController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\ArsipController;
use App\Http\Controllers\Admin\ImportSiswaController;
use App\Http\Controllers\Admin\PeriodeUpdateController;

// BK
use App\Http\Controllers\BK\DashboardController as BKDashboardController;
use App\Http\Controllers\BK\LaporanController;
use App\Http\Controllers\BK\PemanggilanController;
use App\Http\Controllers\BK\MonitoringController;
use App\Http\Controllers\BK\EvaluasiController;
use App\Http\Controllers\BK\RiwayatController;
use App\Http\Controllers\BK\DownloadController;

// Orang Tua
use App\Http\Controllers\OrangTua\DashboardController as OrangTuaDashboardController;
use App\Http\Controllers\OrangTua\LaporanController as OrangTuaLaporanController;
use App\Http\Controllers\OrangTua\PerkembanganController;

/*
|--------------------------------------------------------------------------
| Route Awal
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Foto & Bukti (private, wajib login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/foto/siswa/{siswa}', [FotoController::class, 'siswa'])->name('foto.siswa');
    Route::get('/foto/guru-bk/{guruBk}', [FotoController::class, 'guruBk'])->name('foto.guru-bk');
    Route::get('/foto/user/{user}', [FotoController::class, 'user'])->name('foto.user');
    Route::get('/bukti/{laporan}', [FotoController::class, 'bukti'])->name('bukti.show');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/push-subscription', [PushSubscriptionController::class, 'store'])
        ->name('push-subscription.store');
    Route::delete('/push-subscription', [PushSubscriptionController::class, 'destroy'])
        ->name('push-subscription.destroy');
});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'must.change.password'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Ganti Password Pertama Kali
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/ganti-password-awal', [ProfileController::class, 'showChangePassword'])
        ->name('password.change.form');

    Route::post('/ganti-password-awal', [ProfileController::class, 'processChangePassword'])
        ->name('password.change.process');
});


/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin', 'must.change.password'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::resource('/admin/guru-bk', GuruBKController::class)
        ->names('admin.guru-bk');

    Route::resource('/admin/siswa', SiswaController::class)
        ->names('admin.siswa');

    Route::get('/admin/arsip', [ArsipController::class, 'index'])
        ->name('admin.arsip.index');

    Route::get('/admin/arsip/{id}', [ArsipController::class, 'show'])
        ->name('admin.arsip.show');

    Route::patch('/admin/siswa/nonaktifkan-kelas', [SiswaController::class, 'nonaktifkanKelas'])
        ->name('admin.siswa.nonaktifkan-kelas');

    Route::patch('/admin/siswa/update-kelas-massal', [SiswaController::class, 'updateKelasMassal'])
        ->name('admin.siswa.update-kelas-massal');

    Route::post('/admin/import-siswa', [ImportSiswaController::class, 'store'])
        ->name('admin.import-siswa.store');

    Route::get('/admin/import-siswa/download-hasil', [ImportSiswaController::class, 'downloadHasil'])
        ->name('admin.import-siswa.download-hasil');

    Route::get('/admin/siswa-download', [SiswaController::class, 'download'])
        ->name('admin.siswa.download');

    Route::resource('/admin/periode-update', PeriodeUpdateController::class)
        ->only(['store', 'update', 'destroy'])
        ->names('admin.periode-update');
});

/*
|--------------------------------------------------------------------------
| Guru BK
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:bk', 'must.change.password'])->group(function () {
    Route::get('/bk/dashboard', [BKDashboardController::class, 'index'])
        ->name('bk.dashboard');

    Route::post('/bk/laporan/{id}/proses', [LaporanController::class, 'proses'])
        ->name('bk.laporan.proses');

    Route::resource('/bk/laporan', LaporanController::class)
        ->names('bk.laporan');

    Route::resource('/bk/pemanggilan', PemanggilanController::class)
        ->only(['store', 'update', 'destroy'])
        ->names('bk.pemanggilan');

    Route::resource('/bk/monitoring', MonitoringController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names('bk.monitoring');

    Route::resource('/bk/evaluasi', EvaluasiController::class)
        ->only(['show', 'store', 'update', 'destroy'])
        ->names('bk.evaluasi');

    Route::resource('/bk/riwayat', RiwayatController::class)
        ->only(['index', 'show'])
        ->names('bk.riwayat');

    Route::get('/bk/riwayat/export/pdf', [RiwayatController::class, 'exportPdf'])
        ->name('bk.riwayat.exportPdf');

    Route::get('/admin/import-siswa/download-hasil', [ImportSiswaController::class, 'downloadHasil'])
        ->name('admin.import-siswa.download-hasil');

    Route::get('/bk/riwayat/{id}/download', [RiwayatController::class, 'downloadKasus'])
        ->name('bk.download.kasus');


    Route::get('/bk/download/semua-pdf', [DownloadController::class, 'downloadSemuaPdf'])
        ->name('bk.download.semua-pdf');

    Route::get('/bk/download/semua-excel', [DownloadController::class, 'downloadSemuaExcel'])
        ->name('bk.download.semua-excel');
});

/*
|--------------------------------------------------------------------------
| Orang Tua
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:orang_tua', 'must.change.password', 'cek.periode.update'])->group(function () {
    Route::get('/orang-tua/dashboard', [OrangTuaDashboardController::class, 'index'])
        ->name('orang_tua.dashboard');

    Route::resource('/orang-tua/laporan', OrangTuaLaporanController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->names('orang_tua.laporan');

    Route::get('/orang-tua/perkembangan', [PerkembanganController::class, 'index'])
        ->name('orang_tua.perkembangan');

    Route::get('/orang-tua/perkembangan/{id}', [PerkembanganController::class, 'show'])
        ->name('orang_tua.perkembangan.show');
});

Route::middleware(['auth', 'role:orang_tua', 'must.change.password'])->group(function () {
    Route::get('/orang-tua/update-data', [ProfileController::class, 'editSiswa'])
        ->name('orang_tua.update-data');

    Route::patch('/orang-tua/update-data', [ProfileController::class, 'updateSiswa'])
        ->name('orang_tua.update-data.save');
});


/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';