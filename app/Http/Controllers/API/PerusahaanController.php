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
        $perusahaan = Perusahaan::with(['wilayah', 'lowongan'])->get();

        return response()->json([
            'success' => true,
            'data' => $perusahaan->map(function ($p) {
                return [
                    'perusahaan_id' => $p->perusahaan_id,
                    'nama_perusahaan' => $p->nama_perusahaan,
                    'alamat_perusahaan' => $p->alamat_perusahaan,
                    'wilayah' => $p->wilayah->nama_kota ?? 'Tidak Diketahui',
                    'wilayah_id' => $p->wilayah_id,
                    'lowongan_count' => $p->lowongan->count(),
                    'logo' => $p->logo, // Mengembalikan path logo
                    'deskripsi' => $p->deskripsi, // Mengembalikan deskripsi
                    'gmaps' => $p->gmaps, // Mengembalikan link gmaps
                ];
            }),
        ]);
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
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'wilayah_id' => 'required|exists:m_wilayah,wilayah_id',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'gmaps' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        try {
            // Buat data perusahaan
            $perusahaanData = $request->except('logo');

            // Tangani upload logo jika ada
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $logoPath = $request->file('logo')->store('perusahaan_logos', 'public');
                $perusahaanData['logo'] = $logoPath;
            }

            $perusahaan = Perusahaan::create($perusahaanData);

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil ditambahkan!',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding perusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan perusahaan: ' . $e->getMessage()
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
