<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DosenController;
use App\Http\Controllers\API\MahasiswaController;
use App\Http\Controllers\API\ProdiController;
use App\Http\Controllers\API\MagangController;
use App\Http\Controllers\API\PerusahaanController;
use App\Http\Controllers\dataMhsController;
use App\Http\Controllers\API\LowonganController;
use App\Http\Controllers\API\PeriodeController;
use App\Http\Controllers\API\AdminController;


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
    Route::get('/mahasiswa', [MahasiswaController::class, 'getData']);
    Route::get('/kelas', [dataMhsController::class, 'getKelas']);
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
    Route::get('/prodi', [ProdiController::class, 'index']);
    Route::get('/periode', [PeriodeController::class, 'index']);
    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/tambah-perusahaan', [PerusahaanController::class, 'tambahPerusahaan']);
    Route::get('/lowongan', [LowonganController::class, 'index']);
    Route::get('/dosen', [DosenController::class, 'index']);
    Route::get('/kelas-options', [MahasiswaController::class, 'getKelasOptions']);
});


Route::get('/evaluasi/{filter?}', [App\Http\Controllers\API\EvaluasiController::class, 'index']);