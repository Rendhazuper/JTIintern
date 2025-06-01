<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MahasiswaLowonganController extends Controller
{
    public function view()
    {
        return view('pages.mahasiswa.lowongan', [
            'title' => 'Lowongan Magang'
        ]);
    }

    public function index()
    {
        try {
            $lowongan = DB::table('m_lowongan as l')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->join('m_wilayah as w', 'p.wilayah_id', '=', 'w.wilayah_id')
                ->join('m_periode as pr', 'l.periode_id', '=', 'pr.periode_id')
                ->where('l.status', 'active')
                ->select(
                    'l.id_lowongan',
                    'l.judul_lowongan',
                    'l.deskripsi',
                    'l.kapasitas',
                    'p.nama_perusahaan',
                    'w.nama_kota',
                    'pr.waktu'
                )
                ->get();

            $lowonganData = $lowongan->map(function($item) {
                // Get skills for this lowongan from t_skill_lowongan
                $skills = DB::table('t_skill_lowongan as sl')
                    ->join('m_skill as s', 'sl.id_skill', '=', 's.skill_id')
                    ->where('sl.id_lowongan', $item->id_lowongan)
                    ->select('s.skill_id', 's.nama as nama_skill')
                    ->get()
                    ->map(function($skill) {
                        return [
                            'skill_id' => $skill->skill_id,
                            'nama_skill' => $skill->nama_skill
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id_lowongan' => $item->id_lowongan,
                    'judul_lowongan' => $item->judul_lowongan,
                    'deskripsi' => $item->deskripsi,
                    'kapasitas' => $item->kapasitas,
                    'perusahaan' => [
                        'nama_perusahaan' => $item->nama_perusahaan,
                        'nama_kota' => $item->nama_kota
                    ],
                    'skills' => $skills, // This will now be an array of skill objects
                    'periode' => [
                        'waktu' => $item->waktu
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $lowonganData
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data lowongan: ' . $e->getMessage()
            ], 500);
        }
    }
}