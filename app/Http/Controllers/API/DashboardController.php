<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getSummary()
    {
        try {
            // Get count of active internship students
            $mahasiswaAktif = Mahasiswa::whereHas('lamaran', function ($query) {
                $query->where('auth', 'diterima');
            })->count();

            // Get count of partner companies
            $perusahaanMitra = Perusahaan::count();

            // Get count of active internship positions
            $lowonganAktif = Lowongan::count();

            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa_aktif' => $mahasiswaAktif,
                    'perusahaan_mitra' => $perusahaanMitra,
                    'lowongan_aktif' => $lowonganAktif
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving dashboard summary: ' . $e->getMessage()
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
                    // More robust null checking
                    $mahasiswa = $lamaran->mahasiswa;
                    $user = $mahasiswa->user;
                    $lowongan = $lamaran->lowongan;
                    $perusahaan = $lowongan->perusahaan;

                    return [
                        'id' => $lamaran->id_lamaran,
                        'nama_mahasiswa' => $user -> name,
                        'nim' => $mahasiswa -> nim,
                        'perusahaan' => $perusahaan -> nama_perusahaan,
                        'status' => $lamaran->auth,
                        'tanggal' => $lamaran->tanggal_lamaran ?? $lamaran->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving latest applications: ' . $e->getMessage()
            ], 500);
        }
    }
}
