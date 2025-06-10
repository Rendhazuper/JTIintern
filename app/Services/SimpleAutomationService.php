<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SimpleAutomationService
{
    /**
     * ✅ MAIN: Auto complete expired magang (Fixed untuk struktur database yang ada)
     */
    public function autoCompleteExpired()
    {
        try {
            $today = Carbon::now();
            $todayString = $today->toDateString();
            
            Log::info('🤖 Starting auto completion process', [
                'current_date' => $todayString,
                'current_time' => $today->toDateTimeString()
            ]);
            
            // ✅ FIND: Expired magang yang masih aktif (gunakan DB query karena model mungkin belum tepat)
            $expiredMagang = DB::table('m_magang as m')
                ->leftJoin('m_mahasiswa as mhs', 'm.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->leftJoin('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->leftJoin('m_lowongan as low', 'm.id_lowongan', '=', 'low.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('m.status', 'aktif')
                ->whereNotNull('m.tgl_selesai')
                ->where('m.tgl_selesai', '<', $todayString)
                ->select([
                    'm.id_magang',
                    'm.id_mahasiswa',
                    'm.id_lowongan',
                    'm.id_dosen',
                    'm.tgl_mulai',
                    'm.tgl_selesai',
                    'u.name as nama_mahasiswa',
                    'u.id_user',
                    'p.nama_perusahaan',
                    'low.judul_lowongan'
                ])
                ->get();

            Log::info('🔍 Found expired magang', [
                'count' => $expiredMagang->count(),
                'ids' => $expiredMagang->pluck('id_magang')->toArray()
            ]);

            $completed = 0;
            $failed = 0;
            $results = [];

            foreach ($expiredMagang as $magang) {
                try {
                    $daysExpired = Carbon::parse($magang->tgl_selesai)->diffInDays($today);
                    
                    Log::info('⚙️ Processing magang', [
                        'id_magang' => $magang->id_magang,
                        'mahasiswa' => $magang->nama_mahasiswa ?? 'Unknown',
                        'tgl_selesai' => $magang->tgl_selesai,
                        'days_expired' => $daysExpired
                    ]);

                    $result = $this->completeSingleMagang($magang);
                    
                    if ($result['success']) {
                        $completed++;
                        $results[] = $result;
                        Log::info('✅ Magang completed successfully', $result);
                    } else {
                        $failed++;
                        Log::warning('❌ Failed to complete magang', $result);
                    }

                } catch (\Exception $e) {
                    $failed++;
                    Log::error('💥 Exception while completing magang', [
                        'id_magang' => $magang->id_magang,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // ✅ SUMMARY
            $summary = [
                'success' => true,
                'timestamp' => $today->toDateTimeString(),
                'total_checked' => $expiredMagang->count(),
                'completed' => $completed,
                'failed' => $failed,
                'results' => $results
            ];

            Log::info('📊 Auto completion summary', $summary);
            
            // ✅ CACHE: Last successful run
            cache()->put('last_auto_completion', $today->toDateTimeString(), 86400);
            cache()->put('auto_completion_stats', $summary, 86400);

            return $summary;

        } catch (\Exception $e) {
            Log::error('💥 Auto completion process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ];
        }
    }

    /**
     * ✅ COMPLETE: Single magang dengan validasi lengkap
     */
    private function completeSingleMagang($magang)
    {
        try {
            DB::beginTransaction();

            $today = Carbon::now();
            $endDate = Carbon::parse($magang->tgl_selesai);
            $daysExpired = $endDate->diffInDays($today);

            // ✅ VALIDATE: Pastikan magang memang sudah expired
            if ($today->lte($endDate)) {
                throw new \Exception("Magang belum expired. End date: {$magang->tgl_selesai}");
            }

            // ✅ CHECK: Apakah kolom completed_at, completed_by, catatan_penyelesaian ada
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
                $updateData['catatan_penyelesaian'] = "Magang diselesaikan otomatis oleh sistem karena telah melewati tanggal selesai ({$daysExpired} hari)";
            }

            // ✅ UPDATE: Status magang ke selesai
            DB::table('m_magang')
                ->where('id_magang', $magang->id_magang)
                ->update($updateData);

            // ✅ CALCULATE: Durasi magang
            $startDate = Carbon::parse($magang->tgl_mulai);
            $durasiHari = $startDate->diffInDays($endDate);

            // ✅ CREATE: History record yang lengkap (jika tabel ada)
            $riwayatId = null;
            try {
                // ✅ CHECK: Apakah tabel t_riwayat_magang ada
                $tableExists = DB::select("SHOW TABLES LIKE 't_riwayat_magang'");
                
                if (!empty($tableExists)) {
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
                        'catatan_penyelesaian' => "Magang diselesaikan otomatis setelah {$daysExpired} hari melewati tanggal selesai",
                        'created_at' => $today,
                        'updated_at' => $today
                    ]);
                    
                    Log::info('📝 History record created', ['id_riwayat' => $riwayatId]);
                } else {
                    Log::info('📝 Table t_riwayat_magang not found, skipping history creation');
                }
            } catch (\Exception $e) {
                Log::warning('⚠️ Failed to create history record: ' . $e->getMessage());
            }

            // ✅ SEND: Notification
            if ($magang->id_user) {
                $this->sendCompletionNotification($magang, $daysExpired);
            }

            // ✅ UPDATE: Kapasitas lowongan (kembalikan slot)
            $this->updateLowonganCapacity($magang->id_lowongan);

            DB::commit();

            return [
                'success' => true,
                'id_magang' => $magang->id_magang,
                'id_riwayat' => $riwayatId,
                'mahasiswa' => $magang->nama_mahasiswa ?? 'Unknown',
                'perusahaan' => $magang->nama_perusahaan ?? 'Unknown',
                'durasi_hari' => $durasiHari,
                'days_expired' => $daysExpired,
                'completed_at' => $today->toDateTimeString()
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to complete single magang', [
                'id_magang' => $magang->id_magang,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'id_magang' => $magang->id_magang
            ];
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
}