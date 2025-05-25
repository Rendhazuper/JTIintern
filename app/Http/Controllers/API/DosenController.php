<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Dosen::with(['user', 'perusahaan'])
                         ->select('m_dosen.*', 'm_user.name as nama_dosen')
                         ->join('m_user', 'm_dosen.user_id', '=', 'm_user.id_user')
                         ->orderBy('nama_dosen');

            if ($request->has('perusahaan_id') && $request->perusahaan_id) {
                $query->where('perusahaan_id', $request->perusahaan_id);
            }

            $dosen = $query->get();

            return response()->json([
                'success' => true,
                'data' => $dosen
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dosen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data dosen: ' . $e->getMessage()
            ], 500);
        }
    }
}