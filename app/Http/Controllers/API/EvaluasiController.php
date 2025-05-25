<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Evaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('t_evaluasi as e')
                ->join('m_magang as mg', 'e.id_magang', '=', 'mg.id_magang')
                ->join('m_mahasiswa as mhs', 'mg.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->join('m_dosen as d', 'mg.id_dosen', '=', 'd.id_dosen')
                ->join('m_user as ud', 'd.user_id', '=', 'ud.id_user')
                ->join('m_lowongan as l', 'mg.id_lowongan', '=', 'l.id_lowongan')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->select(
                    'e.id_evaluasi',
                    'e.nilai',
                    'e.eval as evaluasi',
                    'e.created_at',
                    'u.name as nama_mahasiswa',
                    'ud.name as nama_dosen',
                    'p.nama_perusahaan',
                    'p.perusahaan_id',
                    'd.id_dosen'
                )
                ->orderBy('e.created_at', 'desc');

            // Apply filters
            if ($request->has('dosen_id')) {
                $query->where('d.id_dosen', $request->dosen_id);
            }

            if ($request->has('perusahaan_id')) {
                $query->where('p.perusahaan_id', $request->perusahaan_id);
            }

            $evaluations = $query->get();

            return response()->json([
                'success' => true,
                'data' => $evaluations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching evaluations: ' . $e->getMessage()
            ], 500);
        }
    }
}