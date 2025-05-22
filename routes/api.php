<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\dataMhsController;

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
    Route::get('/mahasiswa', [dataMhsController::class, 'getData']);
    Route::get('/kelas', [dataMhsController::class, 'getKelas']);
    Route::post('/tambahMahasiswa',[dataMhsController::class, 'tambahMahasiswa']);
});
