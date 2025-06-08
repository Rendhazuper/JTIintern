<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Magang;
use App\Services\KapasitasLowonganService;
use App\Services\NotificationService; // ✅ TAMBAHAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MagangController extends Controller
{
    protected $kapasitasService;
    protected $notificationService; // ✅ TAMBAHAN

    public function __construct(
        KapasitasLowonganService $kapasitasService,
        NotificationService $notificationService // ✅ TAMBAHAN
    ) {
        $this->kapasitasService = $kapasitasService;
        $this->notificationService = $notificationService; // ✅ TAMBAHAN
    }

    // ✅ TIDAK BERUBAH - Method index tetap sama
    public function index(Request $request)
    {
        try {
            // Query from t_lamaran table for pending applications
            $lamaran = DB::table('t_lamaran')
                ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->select(
                    't_lamaran.id_lamaran as id',
                    'm_user.name',
                    'm_mahasiswa.nim',
                    'm_user.email',
                    'm_lowongan.judul_lowongan',
                    'm_perusahaan.nama_perusahaan',
                    't_lamaran.auth',
                    DB::raw("'menunggu' as status")
                )
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'mahasiswa' => [
                            'name' => $item->name ?? 'Tidak Diketahui',
                            'nim' => $item->nim ?? 'Tidak Diketahui',
                            'email' => $item->email ?? 'Tidak Diketahui',
                        ],
                        'judul_lowongan' => $item->judul_lowongan ?? 'Tidak Diketahui',
                        'perusahaan' => [
                            'nama_perusahaan' => $item->nama_perusahaan ?? 'Tidak Diketahui',
                        ],
                        'status' => $item->status ?? 'menunggu',
                        'auth' => $item->auth ?? 'menunggu',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $lamaran
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching data lamaran: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data permintaan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ TIDAK BERUBAH - Method show tetap sama
    public function show($id)
    {
        try {
            // Fetch from t_lamaran instead of m_magang
            $lamaran = DB::table('t_lamaran')
                ->where('t_lamaran.id_lamaran', $id)
                ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->leftJoin('m_dosen', 't_lamaran.id_dosen', '=', 'm_dosen.id_dosen')
                ->select(
                    't_lamaran.id_lamaran',
                    't_lamaran.id_mahasiswa',
                    't_lamaran.id_lowongan',
                    't_lamaran.id_dosen',
                    't_lamaran.tanggal_lamaran',
                    't_lamaran.auth',
                    'm_mahasiswa.nim',
                    'm_mahasiswa.id_user',
                    'm_user.name',
                    'm_user.email',
                    'm_lowongan.judul_lowongan',
                    'm_lowongan.deskripsi',
                    'm_perusahaan.nama_perusahaan',
                    'm_perusahaan.alamat_perusahaan',
                    'm_perusahaan.contact_person',
                    'm_perusahaan.email as perusahaan_email'
                )
                ->first();

            if (!$lamaran) {
                return response()->json([
                    'success' => false,
                    'message' => "Data lamaran dengan ID {$id} tidak ditemukan."
                ], 404);
            }

            Log::info('Lamaran data fetched:', ['id' => $id, 'data' => (array)$lamaran]);

            // Fetch skills for the student
            $skills = [];
            if ($lamaran->id_user) {
                $skills = DB::table('t_skill_mahasiswa as sm')
                    ->join('m_skill as s', 'sm.skill_id', '=', 's.skill_id')
                    ->where('sm.user_id', $lamaran->id_user)
                    ->select('s.nama')
                    ->get()
                    ->pluck('nama')
                    ->toArray();
            }

            // Format the data similar to the original structure
            $formattedData = [
                'id' => $lamaran->id_lamaran,
                'mahasiswa' => [
                    'name' => $lamaran->name ?? 'Tidak Diketahui',
                    'nim' => $lamaran->nim ?? 'Tidak Diketahui',
                    'email' => $lamaran->email ?? 'Tidak Diketahui',
                    'prodi' => 'Teknologi Informasi',
                    'skills' => $skills
                ],
                'lowongan' => [
                    'judul_lowongan' => $lamaran->judul_lowongan ?? 'Tidak Diketahui',
                    'deskripsi' => $lamaran->deskripsi ?? 'Tidak Diketahui',
                    'persyaratan' => 'Tidak Diketahui',
                    'tanggal_mulai' => '-',
                    'tanggal_selesai' => '-',
                ],
                'perusahaan' => [
                    'nama_perusahaan' => $lamaran->nama_perusahaan ?? 'Tidak Diketahui',
                    'alamat_perusahaan' => $lamaran->alamat_perusahaan ?? 'Tidak Diketahui',
                    'contact_person' => $lamaran->contact_person ?? 'Tidak Diketahui',
                    'email' => $lamaran->perusahaan_email ?? 'Tidak Diketahui',
                ],
                'dokumen' => [
                    'cv_url' => '#',
                    'surat_url' => '#',
                ],
                'status' => 'menunggu',
                'auth' => $lamaran->auth ?? 'menunggu',
            ];

            // Fetch documents if available
            if ($lamaran->id_user) {
                $documents = DB::table('m_dokumen')
                    ->where('id_user', $lamaran->id_user)
                    ->get();

                if ($documents->count() > 0) {
                    foreach ($documents as $doc) {
                        if (
                            stripos($doc->description, 'cv') !== false ||
                            stripos($doc->file_type, 'cv') !== false ||
                            stripos($doc->file_name, 'cv') !== false
                        ) {
                            $formattedData['dokumen']['cv_url'] = asset('storage/' . $doc->file_path);
                        } elseif (
                            stripos($doc->description, 'surat') !== false ||
                            stripos($doc->file_name, 'surat') !== false
                        ) {
                            $formattedData['dokumen']['surat_url'] = asset('storage/' . $doc->file_path);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching detail lamaran: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ ENHANCED - Method accept dengan notifikasi
    public function accept($id)
    {
        try {
            DB::beginTransaction();

            // ✅ VALIDASI: Ambil dan validasi tanggal dari request
            $request = request();
            $tglMulai = $request->input('tgl_mulai');
            $tglSelesai = $request->input('tgl_selesai');

            // ✅ VALIDASI: Pastikan tanggal diisi
            if (!$tglMulai || !$tglSelesai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal mulai dan selesai magang harus diisi.'
                ], 400);
            }

            // ✅ VALIDASI: Format tanggal
            try {
                $startDate = \Carbon\Carbon::parse($tglMulai);
                $endDate = \Carbon\Carbon::parse($tglSelesai);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format tanggal tidak valid.'
                ], 400);
            }

            // ✅ VALIDASI: Tanggal selesai harus setelah tanggal mulai
            if ($endDate->lte($startDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal selesai harus setelah tanggal mulai.'
                ], 400);
            }

            // ✅ VALIDASI: Durasi minimal dan maksimal
            $durasiHari = $startDate->diffInDays($endDate);
            if ($durasiHari < 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'Durasi magang minimal 30 hari.'
                ], 400);
            }

            if ($durasiHari > 180) {
                return response()->json([
                    'success' => false,
                    'message' => 'Durasi magang maksimal 180 hari (6 bulan).'
                ], 400);
            }

            // Find the lamaran entry
            $lamaran = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->select(
                    't_lamaran.*',
                    'm_mahasiswa.id_user',
                    'm_user.name as mahasiswa_name',
                    'm_lowongan.judul_lowongan',
                    'm_perusahaan.nama_perusahaan'
                )
                ->first();

            if (!$lamaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data lamaran tidak ditemukan.'
                ], 404);
            }

            $id_lowongan = $lamaran->id_lowongan;
            $id_mahasiswa = $lamaran->id_mahasiswa;

            // Check capacity first
            if (!$this->kapasitasService->hasAvailableCapacity($id_lowongan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menerima permintaan magang karena kapasitas sudah penuh.'
                ], 400);
            }

            // Check if dosen is already assigned in the lamaran
            $id_dosen = $lamaran->id_dosen ?? null;

            // ✅ UPDATE: Create new entry in m_magang dengan tanggal
            $magang_id = DB::table('m_magang')->insertGetId([
                'id_lowongan' => $id_lowongan,
                'id_mahasiswa' => $id_mahasiswa,
                'id_dosen' => $id_dosen,
                'status' => 'aktif',
                'tgl_mulai' => $startDate->format('Y-m-d'),      // ✅ TAMBAHAN
                'tgl_selesai' => $endDate->format('Y-m-d'),      // ✅ TAMBAHAN
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Created new magang entry with dates', [
                'magang_id' => $magang_id,
                'tgl_mulai' => $startDate->format('Y-m-d'),
                'tgl_selesai' => $endDate->format('Y-m-d'),
                'durasi_hari' => $durasiHari
            ]);

            // ✅ ENHANCED: Trigger notifikasi dengan info tanggal
            try {
                $this->notificationService->lamaranDiterima(
                    $lamaran->id_user,
                    $lamaran->nama_perusahaan,
                    $lamaran->judul_lowongan,
                    $lamaran->id_lamaran
                );

                // ✅ ENHANCED: Trigger notifikasi welcome magang dengan info jadwal
                $this->notificationService->createNotification(
                    $lamaran->id_user,
                    'Jadwal Magang Telah Ditetapkan 📅',
                    "Selamat! Magang Anda di {$lamaran->nama_perusahaan} untuk posisi {$lamaran->judul_lowongan} akan dimulai pada " . 
                    $startDate->format('d M Y') . " dan berakhir pada " . $endDate->format('d M Y') . 
                    " (durasi {$durasiHari} hari). Bersiaplah untuk pengalaman yang menantang!",
                    'magang',
                    'success',
                    true,
                    [
                        'magang_id' => $magang_id,
                        'perusahaan' => $lamaran->nama_perusahaan,
                        'posisi' => $lamaran->judul_lowongan,
                        'tgl_mulai' => $startDate->format('Y-m-d'),
                        'tgl_selesai' => $endDate->format('Y-m-d'),
                        'durasi_hari' => $durasiHari
                    ],
                    30 // 1 bulan
                );

                Log::info('Notifications sent successfully with schedule info', [
                    'user_id' => $lamaran->id_user,
                    'magang_id' => $magang_id,
                    'schedule' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d')
                    ]
                ]);
            } catch (\Exception $notifError) {
                Log::error('Error sending notifications: ' . $notifError->getMessage());
                // Don't rollback transaction just because notification failed
            }

            // Delete ALL entries in t_lamaran for this student
            $deletedLamaran = DB::table('t_lamaran')
                ->where('id_mahasiswa', $id_mahasiswa)
                ->delete();

            Log::info('Deleted ' . $deletedLamaran . ' lamaran entries for student #' . $id_mahasiswa);

            // Decrement available capacity
            $capacityUpdated = $this->kapasitasService->decrementKapasitas($id_lowongan);

            if (!$capacityUpdated) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kapasitas tersedia.'
                ], 500);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan magang berhasil diterima dan jadwal telah ditetapkan.',
                'magang_id' => $magang_id,
                'schedule' => [
                    'tgl_mulai' => $startDate->format('d M Y'),
                    'tgl_selesai' => $endDate->format('d M Y'),
                    'durasi_hari' => $durasiHari
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error accepting magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerima permintaan magang: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ ENHANCED - Method reject dengan notifikasi
    public function reject($id)
    {
        try {
            DB::beginTransaction();

            // ✅ REVISI: Get lamaran data untuk notifikasi SEBELUM di delete
            $lamaranData = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->select(
                    't_lamaran.id_mahasiswa',
                    'm_mahasiswa.id_user',
                    'm_user.name as mahasiswa_name',
                    'm_lowongan.judul_lowongan',
                    'm_perusahaan.nama_perusahaan'
                )
                ->first();

            if (!$lamaranData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data lamaran tidak ditemukan.'
                ], 404);
            }

            // ✅ TAMBAHAN: Trigger notifikasi lamaran ditolak
            try {
                $this->notificationService->lamaranDitolak(
                    $lamaranData->id_user,
                    $lamaranData->nama_perusahaan,
                    $lamaranData->judul_lowongan,
                    'Tidak sesuai dengan kriteria yang dibutuhkan' // Default reason
                );

                Log::info('Rejection notification sent', [
                    'user_id' => $lamaranData->id_user,
                    'lamaran_id' => $id
                ]);
            } catch (\Exception $notifError) {
                Log::error('Error sending rejection notification: ' . $notifError->getMessage());
                // Don't rollback transaction just because notification failed
            }

            // Delete entry in t_lamaran table
            $deleted = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->delete();

            if (!$deleted) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data lamaran.'
                ], 500);
            }

            Log::info('Deleted lamaran entry with ID: ' . $id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan magang berhasil ditolak dan notifikasi telah dikirim.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan magang: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ TIDAK BERUBAH - Method lainnya tetap sama
    public function getAvailable()
    {
        try {
            Log::info('Fetching available applications from t_lamaran');
            
            $availableApplications = DB::table('t_lamaran')
                ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->whereNull('t_lamaran.id_dosen')
                ->where('t_lamaran.auth', 'menunggu')
                ->select(
                    't_lamaran.id_lamaran as id_magang',
                    't_lamaran.id_mahasiswa',
                    't_lamaran.id_lowongan',
                    'm_user.name',
                    'm_mahasiswa.nim',
                    'm_lowongan.judul_lowongan',
                    'm_perusahaan.nama_perusahaan'
                )
                ->get();
                
            Log::info('Found ' . $availableApplications->count() . ' available applications');

            return response()->json([
                'success' => true,
                'data' => $availableApplications
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching available applications: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load available applications: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignPendingMagang(Request $request)
    {
        try {
            $request->validate([
                'magang_id' => 'required|exists:m_magang,id_magang',
                'dosen_id' => 'required|exists:m_dosen,id_dosen',
            ]);

            $magang = Magang::findOrFail($request->magang_id);
            $magang->id_dosen = $request->dosen_id;
            $magang->save();

            return response()->json([
                'success' => true,
                'message' => 'Dosen berhasil ditugaskan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning dosen to pending magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan dosen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkDosen($id)
    {
        try {
            $lamaran = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->first();

            return response()->json([
                'success' => true,
                'has_dosen' => !empty($lamaran->id_dosen)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa data dosen pembimbing'
            ], 500);
        }
    }

    public function assignDosen(Request $request, $id)
    {
        try {
            Log::info('Assigning dosen to lamaran', ['lamaran_id' => $id, 'dosen_id' => $request->dosen_id]);
            
            $lamaran = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->first();

            if (!$lamaran) {
                Log::error('Lamaran not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Data lamaran tidak ditemukan'
                ], 404);
            }
            
            DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->update([
                    'id_dosen' => $request->dosen_id,
                    'updated_at' => now()
                ]);
                
            Log::info('Dosen assigned successfully', ['lamaran_id' => $id, 'dosen_id' => $request->dosen_id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Dosen berhasil ditugaskan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning dosen', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan dosen: ' . $e->getMessage()
            ], 500);
        }
    }
}
