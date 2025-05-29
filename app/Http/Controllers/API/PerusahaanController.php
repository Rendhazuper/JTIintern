<?php
// filepath: d:\laragon\www\JTIintern\app\Http\Controllers\API\PerusahaanController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                        'contact_person' => $p->contact_person,
                        'email' => $p->email,
                        'instagram' => $p->instagram,
                        'website' => $p->website,
                        'deskripsi' => $p->deskripsi,
                        'gmaps' => $p->gmaps,
                        'lowongan_count' => $p->lowongan->count(),
                        'logo' => $p->logo,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching perusahaan data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetailPerusahaan($id)
    {
        try {
            $perusahaan = Perusahaan::with(['wilayah', 'lowongan'])->where('perusahaan_id', $id)->first();

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

    public function update(Request $request, $id)
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
            $perusahaan = Perusahaan::findOrFail($id);
            $perusahaanData = $request->except(['logo', '_method']);

            // Tangani upload logo jika ada
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                // Hapus logo lama jika ada
                if ($perusahaan->logo) {
                    $oldLogoPath = str_replace('storage/', '', $perusahaan->logo);
                    if (Storage::disk('public')->exists($oldLogoPath)) {
                        Storage::disk('public')->delete($oldLogoPath);
                    }
                }
                
                // Upload logo baru
                $logoPath = $request->file('logo')->store('perusahaan_logos', 'public');
                $perusahaanData['logo'] = $logoPath;
            }

            $perusahaan->update($perusahaanData);

            return response()->json([
                'success' => true,
                'message' => 'Data perusahaan berhasil diperbarui!',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating perusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $perusahaan = Perusahaan::with(['wilayah', 'lowongan'])->findOrFail($id);
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

    public function destroy($id)
    {
        try {
            // Cari perusahaan
            $perusahaan = Perusahaan::findOrFail($id);

            // Mulai transaksi database
            DB::beginTransaction();

            // 1. Hapus semua lowongan terkait
            $perusahaan->lowongan()->delete();

            // 2. Hapus file logo jika ada
            if ($perusahaan->logo && !empty($perusahaan->logo)) {
                $logoPath = str_replace('storage/', '', $perusahaan->logo);
                if (Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
            }

            // 3. Hapus perusahaan
            $perusahaan->delete();

            // Commit transaksi
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data perusahaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            Log::error('Error deleting perusahaan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }
}