<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use App\Models\Magang;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function getSummary()
    {
        try {
            // Hitung jumlah mahasiswa aktif magang
            $mahasiswaAktif = Magang::where('status', 'aktif')->count();

            // Hitung jumlah perusahaan mitra
            $perusahaanMitra = Perusahaan::count();

            // Hitung jumlah lowongan aktif
            $lowonganAktif = Lowongan::where('id_lowongan', '!=', null)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa_aktif' => $mahasiswaAktif,
                    'perusahaan_mitra' => $perusahaanMitra,
                    'lowongan_aktif' => $lowonganAktif,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data summary dashboard.'
            ], 500);
        }
    }

    public function getLatestApplications()
    {
        try {
            $applications = Lamaran::with(['mahasiswa.user', 'lowongan.perusahaan'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($lamaran) {
                    // Validasi null untuk relasi
                    $mahasiswa = $lamaran->mahasiswa;
                    $user = $mahasiswa->user ?? null;
                    $lowongan = $lamaran->lowongan ?? null;
                    $perusahaan = $lowongan->perusahaan ?? null;

                    return [
                        'id' => $lamaran->id_lamaran,
                        'nama_mahasiswa' => $user->name ?? 'Tidak Diketahui',
                        'nim' => $mahasiswa->nim ?? 'Tidak Diketahui',
                        'perusahaan' => $perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                        'status' => $lamaran->auth ?? 'Tidak Diketahui', // Ambil status terbaru
                        'tanggal' => $lamaran->tanggal_lamaran ?? $lamaran->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving latest applications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving latest applications: ' . $e->getMessage()
            ], 500);
        }
    }
}
