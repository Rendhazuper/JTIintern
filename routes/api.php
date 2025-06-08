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
use App\Http\Controllers\API\EvaluasiController;
use App\Http\Controllers\API\PlottingController;
use App\Http\Controllers\API\LogbookController;
use App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController;
use App\Http\Controllers\API\NotificationController;

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

// =========================================================
// 1. PUBLIC ROUTES - No Authentication Required
// =========================================================
Route::get('/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
})->middleware('web');

Route::get('/wilayah', [WilayahController::class, 'index']);
Route::get('/dosen/with-perusahaan', [DosenController::class, 'withPerusahaan']);
Route::get('/dosen/with-details', [DosenController::class, 'withDetails']);
Route::get('/wilayah', [WilayahController::class, 'index']);

// =========================================================
// 2. AUTHENTICATION ROUTES
// =========================================================
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// =========================================================
// 3. MAHASISWA ROUTES - For Mahasiswa Role (DIPINDAHKAN KE DEPAN)
// =========================================================
Route::middleware(['web', 'auth', 'role:mahasiswa'])->prefix('mahasiswa')->group(function () {
    // Lowongan routes
    Route::get('/lowongan', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'index']);
    Route::get('/lowongan/{id}', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'show']);
    Route::get('/active-internship', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'checkActiveInternship']);
    Route::post('/apply/{lowongan_id}', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'applyLowongan']);

    // Profile management routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\API\MahasiswaController::class, 'getProfile']);
        Route::put('/', [App\Http\Controllers\API\MahasiswaController::class, 'updateProfile']);
        Route::get('/skills', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'getSkills']);
        Route::post('/skills', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'updateSkills']);
        Route::get('/minat', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'getMinat']);
        Route::post('/minat', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'updateMinat']);
        Route::post('/update', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'update']);
        Route::post('/avatar', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'updateAvatar']);
        Route::post('/password', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'updatePassword']);
        Route::get('/completion', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'checkCompletion']);
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [App\Http\Controllers\API\NotificationController::class, 'index']);
        Route::get('/count', [App\Http\Controllers\API\NotificationController::class, 'getUnreadCount']);
        Route::post('/{id}/read', [App\Http\Controllers\API\NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [App\Http\Controllers\API\NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [App\Http\Controllers\API\NotificationController::class, 'destroy']);
        Route::delete('/', [App\Http\Controllers\API\NotificationController::class, 'clearAll']);
        Route::delete('/read', [App\Http\Controllers\API\NotificationController::class, 'clearRead']);
        Route::delete('/expired', [App\Http\Controllers\API\NotificationController::class, 'clearExpired']);
    });

    // ✅ CLEANED: Lamaran routes (menggabungkan duplikasi)
    Route::prefix('lamaran')->group(function () {
        Route::get('/', [App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'getLamaranMahasiswa']);
        Route::get('/reload', [App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'reloadLamaranData']);
        Route::delete('/{id}/cancel', [App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'cancelLamaran']);
    });

    // ✅ FIXED: Logbook routes menggunakan controller yang benar
    Route::prefix('logbook')->group(function () {
        Route::get('/', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'index']);
        Route::post('/', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'store']);
        Route::delete('/{id}', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'destroy']);
    });

    // Recommendations route
    Route::get('/recommendations', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'getRecommendations']);

<<<<<<< HEAD
=======
    // Route untuk lamaran
    Route::post('/apply/{lowongan_id}', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'applyLowongan']);
    Route::get('/applications', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'getApplications']);
    Route::delete('/cancel-application/{id}', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'cancelApplication']);
    Route::get('/applications/user', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'getUserApplications']);

    Route::get('/lamaran/reload', [App\Http\Controllers\API\Mahasiswa\ViewController::class, 'lamaran']);
    Route::get('/lamaran', [App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'getLamaranMahasiswa']);
    Route::delete('/lamaran/{id}/cancel', [App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'cancelLamaran']);


    Route::get('/logbook', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'index']);
    Route::post('/logbook', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'store']);

>>>>>>> 59721635f51241da58774a8ff1a5f25131d4ef49
    // Evaluasi routes
    Route::prefix('evaluasi')->group(function () {
        Route::get('/', [App\Http\Controllers\API\Mahasiswa\EvaluasiController::class, 'index']);
        Route::get('/filter-options', [App\Http\Controllers\API\Mahasiswa\EvaluasiController::class, 'getFilterOptions']);
    });
});

// =========================================================
// 4. ADMIN & SUPERADMIN ROUTES - Dashboard & Main Functionality
// =========================================================
Route::middleware(['api', 'web', 'auth:sanctum', 'role:admin,superadmin'])->group(function () {
    // Dashboard
    Route::get('/dashboard/active-period', [DashboardController::class, 'getActivePeriod']);
    Route::get('/dashboard/summary', [DashboardController::class, 'getSummary']);
    Route::get('/dashboard/latest-applications', [DashboardController::class, 'getLatestApplications']);

    // Mahasiswa Management
    Route::get('/export/pdf', [MahasiswaController::class, 'exportPDF']);
    Route::post('/import', [MahasiswaController::class, 'importCSV']);
    Route::get('/template', [MahasiswaController::class, 'downloadTemplate']);
    Route::get('/mahasiswa', [MahasiswaController::class, 'index']);
    Route::post('/mahasiswa', [MahasiswaController::class, 'store']);
    Route::get('/mahasiswa/{id}', [MahasiswaController::class, 'show']);
    Route::put('/mahasiswa/{id}', [MahasiswaController::class, 'update']);
    Route::delete('/mahasiswa/{id}', [MahasiswaController::class, 'destroy']);
    Route::get('/kelas-options', [MahasiswaController::class, 'getKelasOptions']);


    // Magang Management
    Route::get('/magang/available', [MagangController::class, 'getAvailable']);
    Route::get('/magang', [MagangController::class, 'index']);
    Route::get('/magang/{id}', [MagangController::class, 'show']);
    Route::post('/magang/{id}/accept', [MagangController::class, 'accept']);
    Route::post('/magang/{id}/reject', [MagangController::class, 'reject']);
    Route::post('/magang/assign-dosen/{id}', [MagangController::class, 'assignDosen']);
    Route::get('/magang/{id}/check-dosen', [MagangController::class, 'checkDosen']);

    // Perusahaan Management
    Route::get('/perusahaan', [PerusahaanController::class, 'getPerusahaanData']);
    Route::get('/perusahaan/{id}', [PerusahaanController::class, 'getDetailPerusahaan']);
    Route::post('/perusahaan', [PerusahaanController::class, 'store']);
    Route::put('/perusahaan/{id}', [PerusahaanController::class, 'update']);
    Route::delete('/perusahaan/{id}', [PerusahaanController::class, 'destroy']);
    Route::post('/perusahaan/import', [PerusahaanController::class, 'import']);
    Route::get('/perusahaan/export/pdf', [PerusahaanController::class, 'exportPDF']);
    Route::post('/tambah-perusahaan', [PerusahaanController::class, 'tambahPerusahaan']);

    // Lowongan Management
    Route::get('/lowongan', [LowonganController::class, 'index']);
    Route::post('/lowongan', [LowonganController::class, 'store']);
    Route::get('/lowongan/{id}', [LowonganController::class, 'show']);
    Route::put('/lowongan/{id}', [LowonganController::class, 'update']);
    Route::delete('/lowongan/{id}', [LowonganController::class, 'destroy']);
    Route::get('/lowongan/{id}/capacity', [LowonganController::class, 'getAvailableCapacity']);
    Route::post('/lowongan/{id}/sync-capacity', [LowonganController::class, 'syncCapacity']);

    // Dosen Management
    Route::get('/dosen', [DosenController::class, 'index']);
    Route::post('/dosen', [DosenController::class, 'store']);
    Route::get('/dosen/{id}', [DosenController::class, 'show']);
    Route::put('/dosen/{id}', [DosenController::class, 'update']);
    Route::delete('/dosen/{id}', [DosenController::class, 'destroy']);
    Route::post('/dosen/import', [DosenController::class, 'import']);
    Route::get('/dosen/export/pdf', [DosenController::class, 'exportPDF']);
    Route::delete('/dosen/{id}/assignments', [DosenController::class, 'removeAssignments']);
    Route::post('/dosen/{id}/assign-mahasiswa', [DosenController::class, 'assignMahasiswa']);

    // Kelas Management
    Route::get('/kelas', [dataMhsController::class, 'getKelas']);
    Route::get('/kelas', [KelasController::class, 'index']);
    Route::post('/kelas', [KelasController::class, 'store']);
    Route::get('/kelas/{id}', [KelasController::class, 'show']);
    Route::put('/kelas/{id}', [KelasController::class, 'update']);
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);

    // Periode Management
    Route::get('/periode', [PeriodeController::class, 'index']);
    Route::post('/periode', [PeriodeController::class, 'store']);
    Route::get('/periode/{id}', [PeriodeController::class, 'show']);
    Route::put('/periode/{id}', [PeriodeController::class, 'update']);
    Route::delete('/periode/{id}', [PeriodeController::class, 'destroy']);
    Route::post('/periode/set-active/{id}', [PeriodeController::class, 'setActive']);

    // Skill Management
    Route::get('/skill', [LowonganController::class, 'getSkill']);
    Route::get('/skills', [App\Http\Controllers\SkillController::class, 'getSkills']);
    Route::post('/skill', [App\Http\Controllers\SkillController::class, 'store']);
    Route::put('/skill/{id}', [App\Http\Controllers\SkillController::class, 'update']);
    Route::delete('/skill/{id}', [App\Http\Controllers\SkillController::class, 'destroy']);

    // Minat Management
    Route::get('/minat', [App\Http\Controllers\MinatController::class, 'getMinat']);
    Route::post('/minat', [App\Http\Controllers\MinatController::class, 'store']);
    Route::get('/minat/{id}', [App\Http\Controllers\MinatController::class, 'show']);
    Route::put('/minat/{id}', [App\Http\Controllers\MinatController::class, 'update']);
    Route::delete('/minat/{id}', [App\Http\Controllers\MinatController::class, 'destroy']);

    // Evaluasi
    Route::get('/evaluasi', [EvaluasiController::class, 'index']);

    // Plotting
    Route::post('/plotting/auto', [PlottingController::class, 'autoPlot']);
    Route::get('/plotting/matrix', [PlottingController::class, 'getPlottingMatrixDetails']);
    Route::get('/plotting/matrix-decision', [PlottingController::class, 'getMatrix']);

    // Misc Options
    Route::get('/jenis', [LowonganController::class, 'getJenis']);
    Route::get('/prodi', [KelasController::class, 'getProdi']);
});

// =========================================================
// 5. SUPERADMIN-ONLY ROUTES
// =========================================================
Route::middleware(['api', 'web', 'auth:sanctum', 'role:superadmin'])->prefix('superadmin')->group(function () {
    // Admin Management - only accessible by superadmin
    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/admin', [AdminController::class, 'store']);
    Route::get('/admin/{id}', [AdminController::class, 'show']);
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
});
