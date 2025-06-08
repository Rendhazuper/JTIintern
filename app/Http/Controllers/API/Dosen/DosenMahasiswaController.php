<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DosenMahasiswaController extends Controller
{
    public function index()
    {
        return view('pages.dosen.DosenMahasiswa', [
            'title' => 'Data Mahasiswa',
        ]);
    }

    public function getMahasiswaBimbingan($id_dosen, Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $perusahaan = $request->get('perusahaan');

            $mahasiswa = DB::table('m_magang as mg')
                ->join('m_mahasiswa as mhs', 'mg.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->join('m_kelas as k', 'mhs.id_kelas', '=', 'k.id_kelas')
                ->leftJoin('m_lowongan as l', 'mg.id_lowongan', '=', 'l.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
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
                ->select(
                    'mhs.id_mahasiswa',
                    'u.name',
                    'mhs.nim',
                    'k.nama_kelas',
                    'mhs.ipk',
                    'mg.status',
                    'p.nama_perusahaan',
                    'l.judul_lowongan'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => $mahasiswa,
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
}
