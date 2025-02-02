<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/mahasiswa', [MahasiswaController::class, 'index']);

// EXTERNAL REQUEST ROUTES
Route::post('/mahasiswa', [MahasiswaController::class, 'getTotalMahasiswaForEachProdi']);

// ADMIN ROUTES
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

Route::middleware('auth')->controller(AdminController::class)->group(function () {
    Route::prefix('/admin')->group(function () {
        // _____ Local Database ________

        // ------- Mahasiswa ---------
        Route::get('/mahasiswa', 'getAllMahasiswaFromDB')->name('admin.mahasiswa');
        Route::get('/mahasiswa/export-current', 'exportCurrentMahasiswaToExcel')->name('admin.mahasiswa.export-current');

        // --------- Prodi ------------
        Route::get('/prodi', 'getAllProdiFromDB')->name('admin.prodi');
        Route::get('/prodi/export-current', 'exportCurrentProdiToExcel')->name('admin.prodi.export-current');

        // -------- Jenjang ------------
        Route::get('/jenjang-didik', 'getAllJenjangFromDB')->name('admin.jenjang');
        Route::get('/jenjang-didik/export-current', 'exportCurrentJenjangToExcel')->name('admin.jenjang.export-current');

        // -------- Dosen ------------
        Route::get('/dosen', 'getAllDosenFromDB')->name('admin.dosen');
        Route::get('/dosen/export-current', 'exportCurrentDosenToExcel')->name('admin.dosen.export-current');

        // -------- Mata Kuliah ------------
        Route::get('/matakuliah', 'getAllMataKuliahFromDB')->name('admin.matakuliah');
        Route::get('/matakuliah/export-current', 'exportCurrentMataKuliahToExcel')->name('admin.matakuliah.export-current');


        // ______ Online Seeder ________
        Route::prefix('/seeder')->group(function () {
            // Program Studi
            Route::get('/prodi', 'getAllProdi');
            Route::post('/prodi', 'syncProdi');
            // Jenjang Pendidikan
            Route::get('/jenjang', 'getAllJenjang');
            Route::post('/jenjang', 'syncJenjang');
            // Mahasiswa
            Route::get('/mahasiswa', 'getAllMahasiswa');
            Route::post('/mahasiswa', 'syncMahasiswa');
            // Dosen
            Route::get('/dosen', 'getAllDosen');
            Route::post('/dosen', 'syncDosen');
            // Dosen
            Route::get('/matakuliah', 'getAllMataKuliah');
            Route::post('/matakuliah', 'syncMataKuliah');
        });
    });

    // Profiles
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
