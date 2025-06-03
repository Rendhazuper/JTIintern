<?php

use App\Http\Controllers\API\Dosen\dashboardController;
use App\Http\Controllers\API\Dosen\DosenEvaluasiController;
use App\Http\Controllers\API\Dosen\DosenMahasiswaController;
use App\Http\Controllers\API\Dosen\DosenProfileController;
use App\Http\Controllers\API\Mahasiswa\ViewController;
use App\Http\Controllers\API\MahasiswaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\dataMhsController;
use App\Http\Controllers\PerusahaanController;

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

// Redirect root to appropriate dashboard based on role
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'superadmin') {
            return redirect('/dashboard');
        } else if (auth()->user()->role === 'admin') {
            return redirect('/dashboard');
        } else if (auth()->user()->role === 'mahasiswa') {
            return redirect('/mahasiswa/dashboard');
        }
    }
    return redirect('/login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    // Registration routes
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.perform');
    
    // Login routes
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
    
    // Password recovery routes
    Route::get('/reset-password', [ResetPassword::class, 'show'])->name('reset-password');
    Route::post('/reset-password', [ResetPassword::class, 'send'])->name('reset.perform');
    Route::get('/change-password', [ChangePassword::class, 'show'])->name('change-password');
    Route::post('/change-password', [ChangePassword::class, 'update'])->name('change.perform');
});

// Common authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Superadmin routes
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/admin', [PageController::class, 'admin'])->name('admin');
});

// Admin routes
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/dataMhs', [dataMhsController::class, 'index'])->name('Data_Mahasiswa');
    Route::get('/data-perusahaan', [PerusahaanController::class, 'index'])->name('data-perusahaan');
    Route::get('/detail-perusahaan/{id}', [PerusahaanController::class, 'showDetail']);
    Route::get('/plotting', [PageController::class, 'plotting'])->name('plotting');
    Route::get('/{page}', [PageController::class, 'index'])->name('page');
});

//dosen routes
Route::prefix('dosen')->middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [dashboardController::class, 'index'])->name('dosen.dashboard');
    Route::get('/mahasiswa', [DosenMahasiswaController::class, 'index'])->name('dosen.mahasiswa');
    Route::get('/evaluasi', [DosenEvaluasiController::class, 'index'])->name('dosen.evaluasi');
    Route::get('/profile', [DosenProfileController::class, 'index'])->name('dosen.profile');
    Route::post('/profile/update', [DosenProfileController::class, 'update'])->name('dosen.profile.update');
});
// Mahasiswa routes
Route::prefix('mahasiswa')->middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', [ViewController::class, 'dashboard'])->name('mahasiswa.dashboard');
    Route::get('/lowongan', [ViewController::class, 'lowongan'])->name('mahasiswa.lowongan');
    Route::get('/lamaran', [ViewController::class, 'lamaran'])->name('mahasiswa.lamaran');
    Route::get('/logaktivitas', [ViewController::class, 'log'])->name('mahasiswa.logaktivitas');
    Route::get('/evaluasi', [ViewController::class, 'evaluasi'])->name('mahasiswa.evaluasi');
    Route::get('/profile', [MahasiswaController::class, 'profile'])->name('mahasiswa.profile');
});