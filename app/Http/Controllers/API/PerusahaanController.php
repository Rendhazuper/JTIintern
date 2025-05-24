<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Log;

class PerusahaanController extends Controller
{
    public function index()
    {
        return view('pages.data_perusahaan');
    }

    public function showDetail($id)
    {
        return view('pages.detail_perusahaan', ['id' => $id]);
    }

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
                'nama_perusahaan' => 'required|string',
                'alamat_perusahaan' => 'required|string',
                'kota' => 'required|string',
                'contact_person' => 'required|string',
                'email' => 'required|email',
                'instagram' => 'nullable|string',
                'website' => 'nullable|url',
                'deskripsi' => 'nullable|string',
                'gmaps' => 'nullable|url'
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

    public function tambahPerusahaan(Request $request)
    {
        try {
            $request->validate([
                'nama_perusahaan' => 'required|string',
                'alamat_perusahaan' => 'required|string',
                'kota' => 'required|string',
                'contact_person' => 'required|string',
                'email' => 'required|email',
                'instagram' => 'nullable|string',
                'website' => 'nullable|url',
                'deskripsi' => 'nullable|string',
                'gmaps' => 'nullable|url'
            ]);

            $perusahaan = Perusahaan::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil ditambahkan',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $perusahaan = Perusahaan::with('lowongan')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
