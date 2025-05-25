<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Lowongan::with('perusahaan')->orderBy('created_at', 'desc');

            if ($request->has('perusahaan_id') && $request->perusahaan_id) {
                $query->where('perusahaan_id', $request->perusahaan_id);
            }

            $lowongan = $query->get();

            return response()->json([
                'success' => true,
                'data' => $lowongan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data lowongan'
            ], 500);
        }
    }
}
