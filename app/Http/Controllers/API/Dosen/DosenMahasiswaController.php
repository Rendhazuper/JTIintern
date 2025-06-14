<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DosenMahasiswaController extends Controller
{
    public function index()
    {
        // Get the authenticated user's dosen data
        $user = auth()->user();
        
        if ($user->role !== 'dosen') {
            return redirect()->route('unauthorized')->with('error', 'Anda tidak memiliki akses sebagai dosen');
        }
        
        $dosen = DB::table('m_dosen')
            ->where('user_id', $user->id_user)
            ->first();
        
        if (!$dosen) {
            // Log this issue for admin awareness
            Log::warning("User with ID {$user->id_user} has dosen role but no dosen record");
        }
        
        return view('pages.dosen.DosenMahasiswa', [
            'title' => 'Data Mahasiswa',
            'dosen_id' => $dosen ? $dosen->id_dosen : null,
        ]);
    }

    public function getMahasiswaBimbingan($id_dosen, Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $perusahaan = $request->get('perusahaan');
            $periode = $request->get('periode'); // Add this line
            $perPage = 6; // Set to 6 items per page
            $page = $request->get('page', 1); // Get the requested page, default to 1

            $mahasiswaQuery = DB::table('m_magang as mg')
                ->join('m_mahasiswa as mhs', 'mg.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->join('m_kelas as k', 'mhs.id_kelas', '=', 'k.id_kelas')
                ->leftJoin('m_lowongan as l', 'mg.id_lowongan', '=', 'l.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->leftJoin('m_periode as pr', 'l.periode_id', '=', 'pr.periode_id') // Add this line
                ->where('mg.id_dosen', $id_dosen)
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('u.name', 'LIKE', "%{$search}%")
                          ->orWhere('mhs.nim', 'LIKE', "%{$search}%")
                          ->orWhere('k.nama_kelas', 'LIKE', "%{$search}%")
                          ->orWhere('p.nama_perusahaan', 'LIKE', "%{$search}%");
                    });
                })
                ->when($status, function ($query) use ($status) {
                    return $query->where('mg.status', $status);
                })
                ->when($perusahaan, function ($query) use ($perusahaan) {
                    return $query->where('p.perusahaan_id', $perusahaan);
                })
                ->when($periode, function ($query) use ($periode) { // Add this block
                    return $query->where('pr.periode_id', $periode);
                })
                ->select(
                    'mhs.id_mahasiswa',
                    'u.name',
                    'mhs.nim', 
                    'k.nama_kelas',
                    'mg.status',
                    'p.nama_perusahaan',
                    'l.judul_lowongan',
                    'pr.waktu as periode', // Add this line
                    'mg.id_magang' // <-- Tambahkan ini!
                );

            // Get total count for pagination
            $total = $mahasiswaQuery->count();
            
            // Apply pagination
            $mahasiswa = $mahasiswaQuery
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Create pagination metadata
            $meta = [
                'current_page' => (int)$page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => ($page - 1) * $perPage + 1,
                'to' => min($page * $perPage, $total)
            ];

            return response()->json([
                'success' => true,
                'data' => $mahasiswa,
                'meta' => $meta,
                'message' => 'Data mahasiswa bimbingan berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaBimbingan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add new method to get companies list
    public function getPerusahaanList()
    {
        try {
            $perusahaan = DB::table('m_perusahaan')
                ->select('perusahaan_id', 'nama_perusahaan')
                ->orderBy('nama_perusahaan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $perusahaan,
                'message' => 'Data perusahaan berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPerusahaanList: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add new method to get periode list
    public function getPeriodeList()
    {
        try {
            $periode = DB::table('m_periode')
                ->select('periode_id', 'waktu')
                ->orderBy('waktu', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $periode,
                'message' => 'Data periode berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPeriodeList: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add this method to DosenMahasiswaController.php
    public function checkEvaluationStatus($magangId)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // Check if evaluation exists for this magang
            $evaluationExists = DB::table('t_evaluasi')
                ->where('id_magang', $magangId)
                ->exists();

            // Also verify that this magang belongs to this dosen
            $magangBelongsToDosen = DB::table('m_magang')
                ->where('id_magang', $magangId)
                ->where('id_dosen', $dosen->id_dosen)
                ->exists();

            if (!$magangBelongsToDosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Magang tidak ditemukan atau bukan tanggung jawab Anda'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'has_evaluation' => $evaluationExists,
                'magang_id' => $magangId
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking evaluation status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa status evaluasi'
            ], 500);
        }
    }

    // ✅ ADD: Method untuk mendapatkan info mahasiswa
    public function getMahasiswaInfo($id_mahasiswa)
    {
        try {
            Log::info("Attempting to get info for mahasiswa ID: {$id_mahasiswa}");

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $dosen = Dosen::where('user_id', $user->id_user)->first();
            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // ✅ PERBAIKI: Verify mahasiswa belongs to this dosen dengan join yang lebih aman
            $mahasiswa = DB::table('m_magang as mg')
                ->join('m_mahasiswa as mhs', 'mg.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->join('m_kelas as k', 'mhs.id_kelas', '=', 'k.id_kelas')
                ->leftJoin('m_lowongan as l', 'mg.id_lowongan', '=', 'l.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('mg.id_dosen', $dosen->id_dosen)
                ->where('mhs.id_mahasiswa', $id_mahasiswa)
                ->select(
                    'mhs.id_mahasiswa',
                    'u.name',
                    'mhs.nim',
                    'k.nama_kelas',
                    'mg.status',
                    DB::raw('COALESCE(p.nama_perusahaan, "Belum ada perusahaan") as nama_perusahaan'),
                    DB::raw('COALESCE(l.judul_lowongan, "Belum ada posisi") as judul_lowongan')
                )
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa tidak ditemukan atau bukan bimbingan Anda'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $mahasiswa
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaInfo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ ADD: Method untuk mendapatkan logbook mahasiswa
    public function getMahasiswaLogbook($id_mahasiswa)
    {
        try {
            Log::info("Attempting to get logbook for mahasiswa ID: {$id_mahasiswa}");

            $user = Auth::user();
            if (!$user) {
                Log::error('User not authenticated');
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $dosen = Dosen::where('user_id', $user->id_user)->first();
            if (!$dosen) {
                Log::error("Dosen not found for user ID: {$user->id_user}");
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // ✅ PERBAIKI: Verify mahasiswa belongs to this dosen
            $mahasiswaBimbingan = DB::table('m_magang')
                ->where('id_dosen', $dosen->id_dosen)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->first();

            if (!$mahasiswaBimbingan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa tidak ditemukan dalam bimbingan Anda'
                ], 403);
            }

            // ✅ PERBAIKI: Cek dulu struktur tabel t_log
            $logbook = DB::table('t_log as tl')
                ->join('m_magang as mg', 'tl.id_magang', '=', 'mg.id_magang')
                ->where('mg.id_mahasiswa', $id_mahasiswa)
                ->where('mg.id_dosen', $dosen->id_dosen)
                ->select(
                    'tl.id_log as id',
                    'tl.tanggal',
                    'tl.log_aktivitas as deskripsi', // ✅ KEMUNGKINAN: ganti 'deskripsi' dengan 'kegiatan' atau kolom yang sesuai
                    'tl.foto',
                    'tl.created_at'
                )
                ->orderBy('tl.tanggal', 'desc')
                ->get();

            Log::info("Found {$logbook->count()} logbook entries for mahasiswa {$id_mahasiswa}");

            if ($logbook->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Belum ada data logbook'
                ]);
            }

            // Group by month and format data
            $groupedData = [];
            $monthGroups = [];

            foreach ($logbook as $entry) {
                try {
                    $date = \Carbon\Carbon::parse($entry->tanggal);
                    $monthKey = $date->format('F Y');
                    
                    if (!isset($monthGroups[$monthKey])) {
                        $monthGroups[$monthKey] = [];
                    }

                    $photoPath = null;
                    $hasFoto = false;

                    if ($entry->foto && !empty($entry->foto)) {
                        // ✅ PERBAIKI: Check if file exists
                        $fullPath = storage_path('app/public/' . $entry->foto);
                        if (file_exists($fullPath)) {
                            $photoPath = asset('storage/' . $entry->foto);
                            $hasFoto = true;
                        } else {
                            Log::warning("Photo file not found: {$fullPath}");
                        }
                    }

                    $monthGroups[$monthKey][] = [
                        'id' => $entry->id,
                        'tanggal' => $entry->tanggal,
                        'tanggal_formatted' => $date->format('d M Y'),
                        'tanggal_hari' => $date->format('l'),
                        'deskripsi' => $entry->deskripsi ?? '',
                        'foto' => $photoPath,
                        'has_foto' => $hasFoto,
                        'time_ago' => $date->diffForHumans(),
                        'created_at' => $entry->created_at
                    ];

                } catch (\Exception $dateError) {
                    Log::error("Error processing logbook entry: " . $dateError->getMessage());
                    continue; // Skip this entry
                }
            }

            // Convert to array format expected by frontend
            foreach ($monthGroups as $month => $entries) {
                $groupedData[] = [
                    'month' => $month,
                    'entries' => $entries
                ];
            }

            Log::info("Successfully processed logbook data with " . count($groupedData) . " month groups");

            return response()->json([
                'success' => true,
                'data' => $groupedData,
                'message' => 'Data logbook berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaLogbook: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data logbook: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function getMahasiswaEvaluasi($id_mahasiswa, Request $request)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            $magang_id = $request->get('magang_id');
            
            if (!$magang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Magang tidak ditemukan'
                ], 400);
            }

            // Verify magang belongs to this dosen and mahasiswa
            $magang = DB::table('m_magang')
                ->where('id_magang', $magang_id)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->where('id_dosen', $dosen->id_dosen)
                ->first();

            if (!$magang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data magang tidak ditemukan atau bukan tanggung jawab Anda'
                ], 403);
            }

            // Check if evaluation already exists
            $existingEvaluasi = DB::table('t_evaluasi')
                ->where('id_magang', $magang_id)
                ->first();

            $evaluasiData = [
                'id_mahasiswa' => $id_mahasiswa,
                'id_magang' => $magang_id,
                'nilai_akhir' => $existingEvaluasi->nilai_akhir ?? '',
                'catatan_evaluasi' => $existingEvaluasi->catatan_evaluasi ?? '',
                'is_existing' => $existingEvaluasi ? true : false
            ];

            return response()->json([
                'success' => true,
                'data' => $evaluasiData,
                'message' => 'Data evaluasi berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaEvaluasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeMahasiswaEvaluasi($id_mahasiswa, Request $request)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // Validate request
            $request->validate([
                'nilai_akhir' => 'required|numeric|min:0|max:100',
                'catatan_evaluasi' => 'required|string'
            ]);

            $magang_id = $request->get('magang_id');
            
            if (!$magang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Magang tidak ditemukan'
                ], 400);
            }

            // Verify magang belongs to this dosen and mahasiswa
            $magang = DB::table('m_magang')
                ->where('id_magang', $magang_id)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->where('id_dosen', $dosen->id_dosen)
                ->first();

            if (!$magang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data magang tidak ditemukan atau bukan tanggung jawab Anda'
                ], 403);
            }

            DB::beginTransaction();

            // Check if evaluation already exists
            $existingEvaluasi = DB::table('t_evaluasi')
                ->where('id_magang', $magang_id)
                ->first();

            $evaluasiData = [
                'id_magang' => $magang_id,
                'id_mahasiswa' => $id_mahasiswa,
                'id_dosen' => $dosen->id_dosen,
                'nilai_akhir' => $request->nilai_akhir,
                'catatan_evaluasi' => $request->catatan_evaluasi,
                'updated_at' => now()
            ];

            if ($existingEvaluasi) {
                // Update existing evaluation
                DB::table('t_evaluasi')
                    ->where('id_evaluasi', $existingEvaluasi->id_evaluasi)
                    ->update($evaluasiData);
                
                $message = 'Evaluasi berhasil diperbarui';
            } else {
                // Create new evaluation
                $evaluasiData['created_at'] = now();
                DB::table('t_evaluasi')->insert($evaluasiData);
                
                $message = 'Evaluasi berhasil disimpan';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storeMahasiswaEvaluasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
