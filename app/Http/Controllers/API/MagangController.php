<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Magang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MagangController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil data magang dengan relasi mahasiswa, lowongan, perusahaan, dan lamaran
            $magang = Magang::with(['mahasiswa', 'lowongan.perusahaan', 'lowongan.lamaran'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Format data untuk frontend
            $formattedData = $magang->map(function ($item) {
                // Ambil auth dari lamaran pertama yang terkait dengan lowongan
                $auth = $item->lowongan->lamaran->first()->auth ?? 'Tidak Diketahui';

                return [
                    'id' => $item->id_magang,
                    'mahasiswa' => [
                        'name' => $item->mahasiswa->user->name ?? 'Tidak Diketahui',
                        'nim' => $item->mahasiswa->nim ?? 'Tidak Diketahui',
                    ],
                    'perusahaan' => [
                        'nama_perusahaan' => $item->lowongan->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                    ],
                    'judul_lowongan' => $item->lowongan->judul_lowongan ?? 'Tidak Diketahui',
                    'status' => $item->status ?? 'Belum Diproses',
                    'auth' => $auth, // Ambil auth dari lamaran
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching data magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data magang'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Ambil data magang berdasarkan ID dengan relasi mahasiswa, lowongan, perusahaan, dan dokumen
            $magang = Magang::with(['mahasiswa.user', 'lowongan.perusahaan', 'dokumen', 'mahasiswa.skills'])
                ->findOrFail($id);

            // Format data untuk frontend
            $formattedData = [
                'id' => $magang->id_magang,
                'mahasiswa' => [
                    'name' => $magang->mahasiswa->user->name ?? 'Tidak Diketahui',
                    'nim' => $magang->mahasiswa->nim ?? 'Tidak Diketahui',
                    'email' => $magang->mahasiswa->user->email ?? 'Tidak Diketahui',
                    'prodi' => $magang->mahasiswa->prodi->nama_prodi ?? 'Tidak Diketahui', // Ambil nama_prodi
                    'skills' => $magang->mahasiswa->skills->pluck('nama_skill') ?? [], // Ambil nama skill
                ],
                'lowongan' => [
                    'judul_lowongan' => $magang->lowongan->judul_lowongan ?? 'Tidak Diketahui',
                    'deskripsi' => $magang->lowongan->deskripsi ?? 'Tidak Diketahui',
                    'persyaratan' => $magang->lowongan->kapasitas ?? 'Tidak Diketahui',
                    'tanggal_mulai' => $magang->lowongan->tanggal_mulai ?? 'Tidak Diketahui',
                    'tanggal_selesai' => $magang->lowongan->tanggal_selesai ?? 'Tidak Diketahui',
                ],
                'perusahaan' => [
                    'nama_perusahaan' => $magang->lowongan->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                    'alamat_perusahaan' => $magang->lowongan->perusahaan->alamat_perusahaan ?? 'Tidak Diketahui',
                    'kota' => $magang->lowongan->perusahaan->kota ?? 'Tidak Diketahui',
                    'contact_person' => $magang->lowongan->perusahaan->contact_person ?? 'Tidak Diketahui',
                    'email' => $magang->lowongan->perusahaan->email ?? 'Tidak Diketahui',
                ],
                'dokumen' => [
                    'cv_url' => $magang->dokumen->cv_url ?? null,
                    'surat_url' => $magang->dokumen->surat_url ?? null,
                ],
                'status' => $magang->status ?? 'Belum Diproses',
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching detail magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail magang'
            ], 500);
        }
    }

    public function accept($id)
    {
        try {
            // Cari data magang berdasarkan ID
            $magang = Magang::findOrFail($id);

            // Perbarui status di tabel m_magang
            $magang->status = 'aktif';
            $magang->save();

            // Perbarui kolom auth di tabel lamaran
            $lamaran = $magang->lamaran->first(); // Ambil lamaran pertama terkait
            if ($lamaran) {
                $lamaran->auth = 'diterima';
                $lamaran->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Permintaan magang berhasil diterima.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerima permintaan magang.'
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            // Cari data magang berdasarkan ID
            $magang = Magang::findOrFail($id);

            // Hapus entri di tabel lamaran terkait
            $lamaran = $magang->lamaran->first(); // Ambil lamaran pertama terkait
            if ($lamaran) {
                $lamaran->delete();
            }

            // Hapus entri di tabel m_magang
            $magang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan magang dan lamaran terkait berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan magang.'
            ], 500);
        }
    }
}
