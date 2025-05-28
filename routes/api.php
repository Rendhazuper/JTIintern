<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DosenController;
use App\Http\Controllers\API\MahasiswaController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\MagangController;
use App\Http\Controllers\API\PerusahaanController;
use App\Http\Controllers\dataMhsController;
use App\Http\Controllers\API\LowonganController;
use App\Http\Controllers\API\PeriodeController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\WilayahController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
})->middleware('web');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});



// Dashboard routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard/summary', [DashboardController::class, 'getSummary']);
    Route::get('/dashboard/latest-applications', [DashboardController::class, 'getLatestApplications']);
    Route::get('/kelas', [dataMhsController::class, 'getKelas']);
    Route::get('/mahasiswa', [MahasiswaController::class, 'index']);
    Route::post('/mahasiswa', [MahasiswaController::class, 'store']);
    Route::get('/mahasiswa/{id}', [MahasiswaController::class, 'show']);
    Route::put('/mahasiswa/{id}', [MahasiswaController::class, 'update']);
    Route::delete('/mahasiswa/{id}', [MahasiswaController::class, 'destroy']);
    Route::get('/magang', [MagangController::class, 'index']);
    Route::get('/magang/{id}', [MagangController::class, 'show']);
    Route::post('/magang/{id}/accept', [MagangController::class, 'accept']);
    Route::post('/magang/{id}/reject', [MagangController::class, 'reject']);
    Route::get('/perusahaan', [PerusahaanController::class, 'getPerusahaanData']);
    Route::get('/perusahaan/{id}', [PerusahaanController::class, 'getDetailPerusahaan']);
    Route::post('/perusahaan', [PerusahaanController::class, 'store']);
    Route::get('/wilayah', [WilayahController::class, 'index']);
    Route::post('/lowongan', [LowonganController::class, 'store']);
    Route::get('/lowongan/{id}', [LowonganController::class, 'show']);
    Route::put('/lowongan/{id}', [LowonganController::class, 'update']);
    Route::delete('/lowongan/{id}', [LowonganController::class, 'destroy']);
    Route::get('/dosen', [DosenController::class, 'index']);
    Route::post('/dosen', [DosenController::class, 'store']);
    Route::get('/dosen/{id}', [DosenController::class, 'show']);
    Route::put('/dosen/{id}', [DosenController::class, 'update']);
    Route::delete('/dosen/{id}', [DosenController::class, 'destroy']);
    Route::get('/skill', [LowonganController::class, 'getSkill']);
    Route::get('/jenis', [LowonganController::class, 'getJenis']);
    Route::get('/prodi', [KelasController::class, 'getProdi']);
    Route::get('/kelas', [KelasController::class, 'index']);
    Route::post('/kelas', [KelasController::class, 'store']);
    Route::get('/kelas/{id}', [KelasController::class, 'show']);
    Route::put('/kelas/{id}', [KelasController::class, 'update']);
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);
    Route::get('/periode', [PeriodeController::class, 'index']);
    Route::post('/periode', [PeriodeController::class, 'store']);
    Route::get('/periode/{id}', [PeriodeController::class, 'show']);
    Route::put('/periode/{id}', [PeriodeController::class, 'update']);
    Route::delete('/periode/{id}', [PeriodeController::class, 'destroy']);
    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/admin', [AdminController::class, 'store']);
    Route::get('/admin/{id}', [AdminController::class, 'show']);
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
    Route::post('/tambah-perusahaan', [PerusahaanController::class, 'tambahPerusahaan']);
    Route::get('/lowongan', [LowonganController::class, 'index']);
    Route::get('/kelas-options', [MahasiswaController::class, 'getKelasOptions']);
});


Route::get('/evaluasi/{filter?}', [App\Http\Controllers\API\EvaluasiController::class, 'index']);
