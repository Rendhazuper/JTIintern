<?php
// filepath: d:\laragon\www\JTIintern\app\Http\Controllers\API\EvaluasiController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluasiController extends Controller
{
    /**
     * Mendapatkan struktur tabel untuk debugging
     */
    public function getTableStructure()
    {
        try {
            $columns = DB::select("DESCRIBE t_evaluasi");
            
            return response()->json([
                'success' => true,
                'columns' => $columns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan semua data evaluasi dengan join ke tabel terkait
     */
     public function index()
    {
        try {
            Log::info('Fetching evaluations data...');
            
            // Query untuk mendapatkan evaluasi dengan join tabel lain untuk data tambahan
            $evaluations = DB::table('t_evaluasi AS e')
                ->leftJoin('m_magang AS m', 'e.id_magang', '=', 'm.id_magang')
                ->leftJoin('m_mahasiswa AS mhs', 'm.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->leftJoin('m_dosen AS d', 'm.id_dosen', '=', 'd.id_dosen')
                ->leftJoin('m_lowongan AS l', 'm.id_lowongan', '=', 'l.id_lowongan')
                ->leftJoin('m_perusahaan AS p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->leftJoin('m_user AS u_mhs', 'mhs.id_user', '=', 'u_mhs.id_user')
                ->leftJoin('m_user AS u_dsn', 'd.user_id', '=', 'u_dsn.id_user')
                ->select([
                    'e.id_evaluasi',
                    'e.id_magang',
                    'e.nilai_akhir AS nilai',
                    'e.catatan_dosen AS eval',
                    'e.grade',
                    'e.nilai_perusahaan',
                    'e.nilai_dosen',
                    'e.status_evaluasi',
                    'e.tanggal_evaluasi_dosen',
                    'e.created_at',
                    'e.updated_at',
                    'u_mhs.name AS nama_mahasiswa',
                    'mhs.nim',
                    'u_dsn.name AS nama_dosen',
                    'd.id_dosen',
                    'p.nama_perusahaan',
                    'p.perusahaan_id'
                ])
                ->orderBy('e.created_at', 'desc')
                ->get();
            
            Log::info('Evaluations fetched: ' . count($evaluations));
            
            return response()->json([
                'success' => true,
                'data' => $evaluations,
                'message' => 'Data evaluasi berhasil ditemukan',
                'count' => count($evaluations)
            ]);
        } catch (\Exception $e) {
            // Log error untuk debug
            Log::error('Error fetching evaluations: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data evaluasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}