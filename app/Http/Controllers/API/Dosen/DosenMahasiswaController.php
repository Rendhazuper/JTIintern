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
                    'pr.waktu as periode' // Add this line
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
}
