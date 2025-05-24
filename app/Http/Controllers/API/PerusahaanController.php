<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Log;

class PerusahaanController extends Controller
{

    public function getPerusahaanData()
    {
        try {
            $perusahaan = Perusahaan::withCount('lowongan')->get();

            return response()->json([
                'success' => true,
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching perusahaan data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data perusahaan.'
            ], 500);
        }
    }

    public function getDetailPerusahaan($id)
    {
        try {
            $perusahaan = Perusahaan::with('lowongan')->where('perusahaan_id', $id)->first();

            if (!$perusahaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perusahaan tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching detail perusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail perusahaan.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:50',
            'alamat_perusahaan' => 'nullable|string|max:50',
            'kota' => 'nullable|string|max:50',
            'contact_person' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        try {
            $perusahaan = Perusahaan::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil ditambahkan!',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan perusahaan. Silakan coba lagi.'
            ], 500);
        }
    }
}
