<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SimpleAutomationService
{
    // ✅ FIX: Remove config dependency untuk sementara
    public function __construct()
    {
        // Empty constructor untuk sementara
    }

    /**
     * ✅ PERBAIKI: Auto complete expired magang
     */
    public function autoCompleteExpired()
    {
        try {
            // ✅ FIX: Define isDebugMode di dalam method
            $isDebugMode = config('app.env') === 'local';
            
            // ✅ PRODUCTION: Ubah cache key untuk interval lebih lama
            // DEBUG: per menit (Y-m-d-H-i) | PRODUCTION: per jam (Y-m-d-H)
            $cacheKey = 'automation_last_check_' . now()->format($isDebugMode ? 'Y-m-d-H-i' : 'Y-m-d-H');
            
            // ✅ PRODUCTION: Cache lebih lama untuk menghindari duplicate
            // DEBUG: 240 detik (4 menit) | PRODUCTION: 3600 detik (1 jam)
            if (cache()->has($cacheKey)) {
                Log::info('🕐 Automation skipped - already checked recently');
                
                return [
                    'success' => true, 
                    'skipped' => true, 
                    'completed' => 0,
                    'failed' => 0,
                    'reason' => 'already_checked',
                    'timestamp' => now()->toDateTimeString()
                ];
            }
            
            $today = Carbon::now()->toDateString();
            $hasExpired = DB::table('m_magang')
                ->where('status', 'aktif')
                ->whereNotNull('tgl_selesai')
                ->where('tgl_selesai', '<=', $today)
                ->limit(1)
                ->exists();
            
            if (!$hasExpired) {
                // ✅ PRODUCTION: Cache negative result lebih lama
                // DEBUG: 240 detik | PRODUCTION: 7200 detik (2 jam)
                $cacheDuration = $isDebugMode ? 240 : 7200;
                cache()->put($cacheKey, now()->toDateTimeString(), $cacheDuration);
                Log::info('🔍 No expired candidates - cached result');
                
                return [
                    'success' => true, 
                    'completed' => 0,
                    'failed' => 0,
                    'cached' => true,
                    'message' => 'No expired magang found',
                    'timestamp' => now()->toDateTimeString()
                ];
            }
            
            $result = $this->processExpiredMagang();
            // ✅ PRODUCTION: Cache successful result untuk 1 jam
            // DEBUG: 120 detik | PRODUCTION: 3600 detik
            $successCacheDuration = $isDebugMode ? 120 : 3600;
            cache()->put($cacheKey, now()->toDateTimeString(), $successCacheDuration);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('💥 Auto completion failed: ' . $e->getMessage());
            
            return [
                'success' => false, 
                'completed' => 0,
                'failed' => 0,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }

    private function processExpiredMagang()
    {
        $today = now()->toDateString();
        $completed = 0;
        $failed = 0;
        
        try {
            // ✅ FIX: Define isDebugMode di dalam method
            $isDebugMode = config('app.env') === 'local';
            
            // ✅ PRODUCTION: Ubah batch size untuk handling lebih banyak data
            // DEBUG: 3 records per batch | PRODUCTION: 20 records per batch
            $batchSize = $isDebugMode ? 3 : 20;
            
            $expiredMagang = DB::table('m_magang as m')
                ->leftJoin('m_mahasiswa as mhs', 'm.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->leftJoin('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->where('m.status', 'aktif')
                ->whereNotNull('m.tgl_selesai')
                ->where('m.tgl_selesai', '<=', $today)
                ->limit($batchSize)
                ->get(['m.*']); // ✅ FIX: Ambil hanya kolom dari m_magang
            
            Log::info('🎯 Processing batch', [
                'count' => $expiredMagang->count(),
                'target_date' => $today,
                'batch_size' => $batchSize,
                'environment' => config('app.env')
            ]);
            
            foreach ($expiredMagang as $magang) {
                try {
                    Log::info("🔄 Attempting to update magang", [
                        'id_magang' => $magang->id_magang,
                        'current_status' => $magang->status,
                        'tgl_selesai' => $magang->tgl_selesai
                    ]);
                    
                    DB::beginTransaction();
                    
                    $updateData = [
                        'status' => 'selesai',
                        'updated_at' => now()->format('Y-m-d H:i:s')
                    ];
                    
                    $updated = DB::table('m_magang')
                        ->where('id_magang', $magang->id_magang)
                        ->where('status', 'aktif')
                        ->update($updateData);
                    
                    if ($updated) {
                        try {
                            DB::table('m_magang')
                                ->where('id_magang', $magang->id_magang)
                                ->update([
                                    'completed_at' => now()->format('Y-m-d H:i:s'),
                                    'completed_by' => 'system',
                                    'catatan_penyelesaian' => 'Auto-completed by system - magang period expired'
                                ]);
                        } catch (\Exception $e) {
                            Log::warning("⚠️ Completion fields update failed for ID: {$magang->id_magang}", [
                                'error' => $e->getMessage()
                            ]);
                        }
                        
                        DB::commit();
                        $completed++;
                        
                        Log::info("✅ Successfully completed magang ID: {$magang->id_magang}");
                        
                    } else {
                        DB::rollback();
                        $failed++;
                        Log::warning("⚠️ No rows updated for magang ID: {$magang->id_magang}");
                    }
                    
                } catch (\Exception $e) {
                    DB::rollback();
                    $failed++;
                    Log::error("❌ Failed to update magang ID: {$magang->id_magang}", [
                        'error' => $e->getMessage()
                    ]);
                }
                
                // ✅ PRODUCTION: Delay antar record untuk menghindari database overload
                // DEBUG: 50000 microseconds (0.05s) | PRODUCTION: 100000 microseconds (0.1s)
                $delay = $isDebugMode ? 50000 : 100000;
                usleep($delay);
            }
            
        } catch (\Exception $e) {
            Log::error('💥 Batch processing failed', [
                'error' => $e->getMessage()
            ]);
            $failed++;
        }
        
        $summary = [
            'success' => true,
            'completed' => $completed,
            'failed' => $failed,
            'processed' => $completed + $failed,
            'timestamp' => now()->toDateTimeString(),
            'batch_size' => $batchSize ?? 'unknown',
            'environment' => config('app.env')
        ];
        
        Log::info('📊 Batch completion summary', $summary);
        return $summary;
    }

    /**
     * ✅ PERBAIKI: Complete single magang dengan validasi yang lebih fleksibel
     */
    private function completeSingleMagang($magang)
    {
        try {
            DB::beginTransaction();

            $today = Carbon::now();
            $endDate = Carbon::parse($magang->tgl_selesai);
            $daysExpired = $endDate->diffInDays($today, false);

            // ✅ PERBAIKI: Validasi yang lebih fleksibel (izinkan pada hari yang sama)
            if ($today->lt($endDate)) { // ✅ CHANGED: dari lte ke lt
                throw new \Exception("Magang belum expired. End date: {$magang->tgl_selesai}, today: {$today->toDateString()}");
            }

            Log::info('🎯 Processing magang completion', [
                'id_magang' => $magang->id_magang,
                'end_date' => $magang->tgl_selesai,
                'today' => $today->toDateString(),
                'days_expired' => $daysExpired,
                'status' => 'valid_for_completion'
            ]);

            // ✅ CHECK: Kolom yang tersedia
            $tableColumns = DB::select("SHOW COLUMNS FROM m_magang");
            $columnNames = array_column($tableColumns, 'Field');
            
            $updateData = [
                'status' => 'selesai',
                'updated_at' => $today
            ];

            // ✅ CONDITIONAL: Tambahkan kolom jika ada
            if (in_array('completed_at', $columnNames)) {
                $updateData['completed_at'] = $today;
            }
            if (in_array('completed_by', $columnNames)) {
                $updateData['completed_by'] = 'system';
            }
            if (in_array('catatan_penyelesaian', $columnNames)) {
                $updateData['catatan_penyelesaian'] = $daysExpired >= 0 
                    ? "Magang diselesaikan otomatis oleh sistem pada tanggal selesai" 
                    : "Magang diselesaikan otomatis oleh sistem setelah {$daysExpired} hari melewati tanggal selesai";
            }

            // ✅ UPDATE: Status magang ke selesai
            $updated = DB::table('m_magang')
                ->where('id_magang', $magang->id_magang)
                ->update($updateData);

            Log::info('📝 Database update result', [
                'id_magang' => $magang->id_magang,
                'rows_affected' => $updated,
                'update_data' => $updateData
            ]);

            // ✅ VERIFY: Update berhasil
            $verification = DB::table('m_magang')
                ->where('id_magang', $magang->id_magang)
                ->first();

            Log::info('🔍 Verification after update', [
                'id_magang' => $magang->id_magang,
                'new_status' => $verification->status ?? 'NOT_FOUND',
                'updated_at' => $verification->updated_at ?? 'NOT_FOUND'
            ]);

            // ✅ CREATE: History record
            $riwayatId = $this->createHistoryRecord($magang, $today, $daysExpired);

            // ✅ SEND: Notification
            if ($magang->id_user) {
                $this->sendCompletionNotification($magang, $daysExpired);
            }

            // ✅ UPDATE: Kapasitas lowongan
            $this->updateLowonganCapacity($magang->id_lowongan);

            DB::commit();

            return [
                'success' => true,
                'id_magang' => $magang->id_magang,
                'id_riwayat' => $riwayatId,
                'mahasiswa' => $magang->nama_mahasiswa ?? 'Unknown',
                'perusahaan' => $magang->nama_perusahaan ?? 'Unknown',
                'old_status' => $magang->status,
                'new_status' => 'selesai',
                'days_expired' => $daysExpired,
                'completed_at' => $today->toDateTimeString(),
                'rows_updated' => $updated
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('💥 Failed to complete single magang', [
                'id_magang' => $magang->id_magang,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'id_magang' => $magang->id_magang
            ];
        }
    }

    /**
     * ✅ NEW: Create history record
     */
    private function createHistoryRecord($magang, $today, $daysExpired)
    {
        try {
            $tableExists = DB::select("SHOW TABLES LIKE 't_riwayat_magang'");
            
            if (!empty($tableExists)) {
                $startDate = Carbon::parse($magang->tgl_mulai);
                $endDate = Carbon::parse($magang->tgl_selesai);
                $durasiHari = $startDate->diffInDays($endDate);

                $riwayatId = DB::table('t_riwayat_magang')->insertGetId([
                    'id_magang' => $magang->id_magang,
                    'id_mahasiswa' => $magang->id_mahasiswa,
                    'id_lowongan' => $magang->id_lowongan,
                    'id_dosen' => $magang->id_dosen,
                    'tgl_mulai' => $magang->tgl_mulai,
                    'tgl_selesai' => $magang->tgl_selesai,
                    'durasi_hari' => $durasiHari,
                    'status_awal' => 'aktif',
                    'status_akhir' => 'selesai',
                    'completed_at' => $today,
                    'completed_by' => 'system',
                    'status_completion' => 'auto_completed',
                    'catatan_penyelesaian' => $daysExpired >= 0 
                        ? "Diselesaikan otomatis pada tanggal selesai"
                        : "Diselesaikan otomatis setelah {$daysExpired} hari",
                    'created_at' => $today,
                    'updated_at' => $today
                ]);
                
                Log::info('📝 History record created', ['id_riwayat' => $riwayatId]);
                return $riwayatId;
            } else {
                Log::info('📝 Table t_riwayat_magang not found, skipping history creation');
                return null;
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Failed to create history record: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ SEND: Enhanced notification
     */
    private function sendCompletionNotification($magang, $daysExpired)
    {
        try {
            $perusahaan = $magang->nama_perusahaan ?? 'Perusahaan';
            $posisi = $magang->judul_lowongan ?? 'Posisi Magang';
            $endDate = Carbon::parse($magang->tgl_selesai)->format('d M Y');

            $message = $daysExpired <= 1 
                ? "Selamat! Magang Anda di {$perusahaan} untuk posisi {$posisi} telah selesai pada {$endDate}. Terima kasih atas dedikasi Anda!"
                : "Magang Anda di {$perusahaan} untuk posisi {$posisi} telah diselesaikan otomatis karena telah melewati tanggal selesai ({$endDate}) selama {$daysExpired} hari.";

            // ✅ NOTIFICATION: Try multiple table names
            $notificationData = [
                'id_user' => $magang->id_user,
                'judul' => 'Magang Selesai 🎉',
                'pesan' => $message,
                'kategori' => 'magang',
                'jenis' => 'success',
                'is_important' => true,
                'is_read' => false,
                'data_terkait' => json_encode([
                    'id_magang' => $magang->id_magang,
                    'perusahaan' => $perusahaan,
                    'posisi' => $posisi,
                    'tgl_selesai' => $magang->tgl_selesai,
                    'days_expired' => $daysExpired,
                    'completion_type' => 'auto_completed'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // ✅ CONDITIONAL: Check if expired_at column exists
            $notificationTables = ['m_notifikasi', 't_notifikasi', 'notifications'];
            $notificationSent = false;

            foreach ($notificationTables as $tableName) {
                try {
                    $tableExists = DB::select("SHOW TABLES LIKE '{$tableName}'");
                    
                    if (!empty($tableExists)) {
                        // Check if expired_at column exists
                        $tableColumns = DB::select("SHOW COLUMNS FROM {$tableName}");
                        $columnNames = array_column($tableColumns, 'Field');
                        
                        if (in_array('expired_at', $columnNames)) {
                            $notificationData['expired_at'] = now()->addDays(30);
                        }

                        DB::table($tableName)->insert($notificationData);
                        
                        Log::info("✉️ Completion notification sent to {$tableName}", [
                            'user_id' => $magang->id_user,
                            'magang_id' => $magang->id_magang
                        ]);
                        
                        $notificationSent = true;
                        break;
                    }
                } catch (\Exception $e) {
                    Log::debug("Failed to send notification to {$tableName}: " . $e->getMessage());
                    continue;
                }
            }

            if (!$notificationSent) {
                Log::warning('No notification table found or accessible');
            }

        } catch (\Exception $e) {
            Log::warning('Failed to send completion notification: ' . $e->getMessage());
        }
    }

    /**
     * ✅ UPDATE: Lowongan capacity
     */
    private function updateLowonganCapacity($idLowongan)
    {
        try {
            // ✅ CHECK: Apakah tabel t_kapasitas_lowongan ada
            $tableExists = DB::select("SHOW TABLES LIKE 't_kapasitas_lowongan'");
            
            if (!empty($tableExists)) {
                // Increment available capacity (karena ada slot yang kosong)
                DB::table('t_kapasitas_lowongan')
                    ->where('id_lowongan', $idLowongan)
                    ->increment('kapasitas_tersedia');
                    
                Log::info('📈 Lowongan capacity updated', ['id_lowongan' => $idLowongan]);
            } else {
                // ✅ ALTERNATIVE: Update m_lowongan kapasitas langsung
                $lowongan = DB::table('m_lowongan')->where('id_lowongan', $idLowongan)->first();
                if ($lowongan) {
                    // Hitung current usage
                    $currentUsage = DB::table('m_magang')
                        ->where('id_lowongan', $idLowongan)
                        ->where('status', 'aktif')
                        ->count();
                    
                    Log::info('📊 Lowongan capacity info', [
                        'id_lowongan' => $idLowongan,
                        'total_kapasitas' => $lowongan->kapasitas ?? 'Unknown',
                        'current_usage' => $currentUsage
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update lowongan capacity: ' . $e->getMessage());
        }
    }

    /**
     * ✅ GET: Current status
     */
    public function getCurrentStatus()
    {
        $today = Carbon::now()->toDateString();
        
        return [
            'active_magang' => DB::table('m_magang')->where('status', 'aktif')->count(),
            'expired_magang' => DB::table('m_magang')
                ->where('status', 'aktif')
                ->whereNotNull('tgl_selesai')
                ->where('tgl_selesai', '<', $today)
                ->count(),
            'expiring_soon' => DB::table('m_magang')
                ->where('status', 'aktif')
                ->whereNotNull('tgl_selesai')
                ->whereBetween('tgl_selesai', [
                    $today,
                    Carbon::now()->addDays(7)->toDateString()
                ])
                ->count(),
            'completed_magang' => DB::table('m_magang')->where('status', 'selesai')->count(),
            'last_auto_run' => cache()->get('last_auto_completion', 'Never'),
            'last_stats' => cache()->get('auto_completion_stats', [])
        ];
    }

    /**
     * ✅ CHECK: Warning for expiring soon
     */
    public function checkExpiringMagang($daysBefore = 3)
    {
        try {
            $targetDate = Carbon::now()->addDays($daysBefore)->toDateString();
            $today = Carbon::now()->toDateString();
            
            $expiring = DB::table('m_magang as m')
                ->leftJoin('m_mahasiswa as mhs', 'm.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->leftJoin('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->leftJoin('m_lowongan as low', 'm.id_lowongan', '=', 'low.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('m.status', 'aktif')
                ->whereNotNull('m.tgl_selesai')
                ->whereBetween('m.tgl_selesai', [$today, $targetDate])
                ->select([
                    'm.id_magang',
                    'm.tgl_selesai',
                    'u.id_user',
                    'u.name as nama_mahasiswa',
                    'p.nama_perusahaan',
                    'low.judul_lowongan'
                ])
                ->get();

            Log::info('🔍 Checking expiring magang', [
                'target_date' => $targetDate,
                'found_count' => $expiring->count()
            ]);

            $notificationsSent = 0;

            foreach ($expiring as $magang) {
                try {
                    $daysLeft = Carbon::now()->diffInDays(Carbon::parse($magang->tgl_selesai));
                    $this->sendExpiringWarning($magang, $daysLeft);
                    $notificationsSent++;
                } catch (\Exception $e) {
                    Log::warning('Failed to send expiring warning: ' . $e->getMessage());
                }
            }

            return [
                'success' => true,
                'expiring_count' => $expiring->count(),
                'notifications_sent' => $notificationsSent,
                'checked_date' => $targetDate
            ];

        } catch (\Exception $e) {
            Log::error('Error checking expiring magang: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * ✅ SEND: Expiring warning
     */
    private function sendExpiringWarning($magang, $daysLeft)
    {
        try {
            $perusahaan = $magang->nama_perusahaan ?? 'Perusahaan';
            $endDate = Carbon::parse($magang->tgl_selesai)->format('d M Y');

            $notificationData = [
                'id_user' => $magang->id_user,
                'judul' => 'Magang Akan Berakhir ⏰',
                'pesan' => "Magang Anda di {$perusahaan} akan berakhir pada {$endDate} ({$daysLeft} hari lagi). Pastikan semua tugas dan logbook sudah selesai!",
                'kategori' => 'magang',
                'jenis' => 'warning',
                'is_important' => true,
                'is_read' => false,
                'data_terkait' => json_encode([
                    'id_magang' => $magang->id_magang,
                    'days_left' => $daysLeft,
                    'end_date' => $magang->tgl_selesai
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // ✅ SEND: To notification table
            $notificationTables = ['m_notifikasi', 't_notifikasi', 'notifications'];
            
            foreach ($notificationTables as $tableName) {
                try {
                    $tableExists = DB::select("SHOW TABLES LIKE '{$tableName}'");
                    if (!empty($tableExists)) {
                        DB::table($tableName)->insert($notificationData);
                        Log::info("⏰ Expiring warning sent to {$tableName}", [
                            'user_id' => $magang->id_user,
                            'days_left' => $daysLeft
                        ]);
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send expiring warning: ' . $e->getMessage());
        }
    }

    /**
     * ✅ MANUAL: Complete specific magang
     */
    public function manualComplete($magangId, $reason = null)
    {
        try {
            $magang = DB::table('m_magang as m')
                ->leftJoin('m_mahasiswa as mhs', 'm.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->leftJoin('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->leftJoin('m_lowongan as low', 'm.id_lowongan', '=', 'low.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('m.id_magang', $magangId)
                ->select([
                    'm.id_magang',
                    'm.id_mahasiswa',
                    'm.id_lowongan',
                    'm.id_dosen',
                    'm.status',
                    'm.tgl_mulai',
                    'm.tgl_selesai',
                    'u.name as nama_mahasiswa',
                    'u.id_user',
                    'p.nama_perusahaan',
                    'low.judul_lowongan'
                ])
                ->first();
            
            if (!$magang) {
                throw new \Exception("Magang with ID {$magangId} not found");
            }

            if ($magang->status !== 'aktif') {
                throw new \Exception("Magang status is not active: {$magang->status}");
            }

            // Override completion method
            $result = $this->completeSingleMagang($magang);
            
            if ($result['success']) {
                // Update with manual completion details
                $tableColumns = DB::select("SHOW COLUMNS FROM m_magang");
                $columnNames = array_column($tableColumns, 'Field');
                
                $updateData = [];
                if (in_array('completed_by', $columnNames)) {
                    $updateData['completed_by'] = 'admin';
                }
                if (in_array('catatan_penyelesaian', $columnNames)) {
                    $updateData['catatan_penyelesaian'] = $reason ?? 'Diselesaikan manual oleh admin';
                }

                if (!empty($updateData)) {
                    DB::table('m_magang')
                        ->where('id_magang', $magangId)
                        ->update($updateData);
                }

                // Update riwayat if exists
                try {
                    $tableExists = DB::select("SHOW TABLES LIKE 't_riwayat_magang'");
                    if (!empty($tableExists)) {
                        DB::table('t_riwayat_magang')
                            ->where('id_magang', $magangId)
                            ->update([
                                'completed_by' => 'admin',
                                'status_completion' => 'manual_completed',
                                'catatan_penyelesaian' => $reason ?? 'Diselesaikan manual oleh admin'
                            ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to update riwayat: ' . $e->getMessage());
                }
            }

            return $result;

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * ✅ DEBUG: Get database structure info
     */
    public function getDatabaseInfo()
    {
        try {
            $info = [
                'tables' => [],
                'magang_columns' => [],
                'sample_data' => []
            ];

            // Check tables
            $tables = ['m_magang', 't_riwayat_magang', 'm_notifikasi', 't_notifikasi', 't_kapasitas_lowongan'];
            foreach ($tables as $table) {
                $exists = DB::select("SHOW TABLES LIKE '{$table}'");
                $info['tables'][$table] = !empty($exists);
            }

            // Check m_magang columns
            if ($info['tables']['m_magang']) {
                $columns = DB::select("SHOW COLUMNS FROM m_magang");
                $info['magang_columns'] = array_column($columns, 'Field');
            }

            // Get sample data
            $info['sample_data'] = [
                'total_magang' => DB::table('m_magang')->count(),
                'active_magang' => DB::table('m_magang')->where('status', 'aktif')->count(),
                'sample_magang' => DB::table('m_magang')->limit(3)->get()
            ];

            return $info;

        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * ✅ NEW: Force complete today (untuk testing)
     */
    public function forceCompleteToday()
    {
        try {
            $today = Carbon::now()->toDateString();
            
            Log::info('🔧 FORCE COMPLETION: Starting manual completion for today', [
                'target_date' => $today
            ]);
            
            // Ambil semua magang aktif yang seharusnya selesai hari ini atau sebelumnya
            $targetMagang = DB::table('m_magang as m')
                ->leftJoin('m_mahasiswa as mhs', 'm.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->leftJoin('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->leftJoin('m_lowongan as low', 'm.id_lowongan', '=', 'low.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('m.status', 'aktif')
                ->whereNotNull('m.tgl_selesai')
                ->where('m.tgl_selesai', '<=', $today)
                ->select([
                    'm.id_magang',
                    'm.id_mahasiswa',
                    'm.id_lowongan',
                    'm.id_dosen',
                    'm.tgl_mulai',
                    'm.tgl_selesai',
                    'm.status',
                    'u.name as nama_mahasiswa',
                    'u.id_user',
                    'p.nama_perusahaan',
                    'low.judul_lowongan'
                ])
                ->get();

            Log::info('🎯 FORCE: Found target magang', [
                'count' => $targetMagang->count(),
                'details' => $targetMagang->map(function($m) {
                    return [
                        'id' => $m->id_magang,
                        'mahasiswa' => $m->nama_mahasiswa,
                        'end_date' => $m->tgl_selesai,
                        'status' => $m->status
                    ];
                })
            ]);

            $results = [];
            foreach ($targetMagang as $magang) {
                try {
                    $result = $this->completeSingleMagang($magang);
                    $results[] = $result;
                    
                    Log::info('✅ FORCE: Completed magang', [
                        'id_magang' => $magang->id_magang,
                        'result' => $result
                    ]);
                } catch (\Exception $e) {
                    $results[] = [
                        'success' => false,
                        'id_magang' => $magang->id_magang,
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('❌ FORCE: Failed to complete magang', [
                        'id_magang' => $magang->id_magang,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return [
                'success' => true,
                'processed' => $targetMagang->count(),
                'results' => $results,
                'summary' => [
                    'successful' => collect($results)->where('success', true)->count(),
                    'failed' => collect($results)->where('success', false)->count()
                ]
            ];

        } catch (\Exception $e) {
            Log::error('💥 FORCE COMPLETION FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * ✅ DEBUG: Method untuk test return structure
     */
    public function debugReturnStructure()
    {
        $testCases = [
            'no_expired' => $this->autoCompleteExpired(),
            'with_cache' => cache()->put('automation_last_check_' . now()->format('Y-m-d-H-i'), now()->toDateTimeString(), 60),
        ];
        
        // Test cached response
        $cachedResult = $this->autoCompleteExpired();
        
        return [
            'structure_test' => [
                'no_expired' => $testCases['no_expired'],
                'cached_response' => $cachedResult
            ],
            'required_keys' => ['success', 'completed', 'failed'],
            'validation' => $this->validateReturnStructure($cachedResult)
        ];
    }

    private function validateReturnStructure($result)
    {
        $requiredKeys = ['success', 'completed', 'failed'];
        $missing = [];
        
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $result)) {
                $missing[] = $key;
            }
        }
        
        return [
            'valid' => empty($missing),
            'missing_keys' => $missing,
            'available_keys' => array_keys($result)
        ];
    }
}