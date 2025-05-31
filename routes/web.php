<?php

use App\Http\Controllers\API\MahasiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\dataMhsController;
use App\Http\Controllers\PerusahaanController;

Route::get('/', function () {
	return redirect('/dashboard');
})->middleware('auth');
Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest')->name('register.perform');
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.perform');
Route::get('/reset-password', [ResetPassword::class, 'show'])->middleware('guest')->name('reset-password');
Route::post('/reset-password', [ResetPassword::class, 'send'])->middleware('guest')->name('reset.perform');
Route::get('/change-password', [ChangePassword::class, 'show'])->middleware('guest')->name('change-password');
Route::post('/change-password', [ChangePassword::class, 'update'])->middleware('guest')->name('change.perform');
Route::get('/dashboard', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::group(['middleware' => 'auth'], function () {
	Route::get('/dataMhs', [dataMhsController::class, 'index'])->name('Data_Mahasiswa');
	Route::get('/{page}', [PageController::class, 'index'])->name('page');
	Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
	Route::get('/data-perusahaan', [PerusahaanController::class, 'index'])->name('data-perusahaan');
	Route::get('/detail-perusahaan/{id}', [PerusahaanController::class, 'showDetail']);
	Route::get('/plotting', [PageController::class, 'plotting'])->name('plotting');
});

Route::prefix('mahasiswa')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
    Route::get('/lowongan', [MahasiswaController::class, 'lowongan'])->name('mahasiswa.lowongan');
    Route::get('/lamaran', [MahasiswaController::class, 'lamaran'])->name('mahasiswa.lamaran');
    Route::get('/evaluasi', [MahasiswaController::class, 'evaluasi'])->name('mahasiswa.evaluasi');
    Route::get('/profile', [MahasiswaController::class, 'profile'])->name('mahasiswa.profile');
});