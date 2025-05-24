<?php

namespace App\Http\Controllers\API;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class MahasiswaController extends Controller
{
    public function getData(Request $request)
    {
        try {
            $query = Mahasiswa::with(['user', 'skills', 'magang', 'programStudi', 'lamaran']);

            // Filter by prodi jika ada parameter prodi
            if ($request->has('prodi') && $request->prodi != '') {
                $query->whereHas('programStudi', function ($q) use ($request) {
                    $q->where('nama_prodi', $request->prodi);
                });
            }

            $mahasiswa = $query->get()->map(function ($mahasiswa) {
                $status = 'Belum Magang'; // Default status

                if ($mahasiswa->magang) {
                    if ($mahasiswa->magang->status === 'aktif') {
                        $status = 'Sedang Magang';
                    } elseif ($mahasiswa->magang->status === 'selesai') {
                        $status = 'Selesai Magang';
                    } elseif ($mahasiswa->magang->status === 'tidak aktif') {
                        $status = $mahasiswa->lamaran->isNotEmpty() ? 'Menunggu Konfirmasi' : 'Belum Magang';
                    }
                }

                return [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'name' => $mahasiswa->user->name ?? '-',
                    'email' => $mahasiswa->user->email ?? '-',
                    'nim' => $mahasiswa->nim,
                    'prodi' => $mahasiswa->programStudi->nama_prodi ?? '-',
                    'status_magang' => $status,
                ];
            });

            Log::info('Data mahasiswa:', $mahasiswa->toArray()); // Tambahkan log untuk debugging

            return response()->json([
                'success' => true,
                'data' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            Log::error('Error memuat data mahasiswa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data mahasiswa'
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:m_user,email',
            'password' => 'required|string|min:6',
            'nim' => 'required|string|unique:m_mahasiswa,nim',
            'kode_prodi' => 'required|string|exists:m_prodi,kode_prodi',
            'alamat' => 'required|string',
            'ipk' => 'nullable|numeric|min:0|max:4'
        ]);

        // 1. Insert ke m_user
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Akan otomatis di-bcrypt oleh mutator
            'role' => 'mahasiswa'
        ]);

        // 2. Insert ke m_mahasiswa
        $mahasiswa = \App\Models\Mahasiswa::create([
            'nim' => $request->nim,
            'id_user' => $user->id_user,
            'kode_prodi' => $request->kode_prodi,
            'alamat' => $request->alamat,
            'ipk' => $request->ipk,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil ditambahkan',
            'data' => [
                'user' => $user,
                'mahasiswa' => $mahasiswa
            ]
        ]);
    }
    public function show($id)
    {
        try {
            $mahasiswa = Mahasiswa::with(['user', 'magang', 'programStudi', 'skills', 'dokumen'])
                ->where('id_mahasiswa', $id)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'name' => $mahasiswa->user->name ?? '-',
                    'email' => $mahasiswa->user->email ?? '-',
                    'nim' => $mahasiswa->nim,
                    'prodi' => $mahasiswa->programStudi->nama_prodi ?? '-',
                    'status_magang' => $mahasiswa->magang->status ?? 'Belum Magang',
                    'alamat' => $mahasiswa->alamat ?? '-',
                    'ipk' => $mahasiswa->ipk ?? '-',
                    'skills' => $mahasiswa->skills->map(function ($skill) {
                        return [
                            'name' => $skill->nama ?? 'Tidak Diketahui',
                            'lama_skill' => $skill->pivot->lama_skill ?? 'Tidak Diketahui'
                        ];
                    }),
                    'dokumen' => $mahasiswa->dokumen->map(function ($dokumen) {
                        return [
                            'file_name' => $dokumen->file_name,
                            'file_url' => asset('storage/' . $dokumen->file_path),
                            'file_type' => $dokumen->file_type,
                            'description' => $dokumen->description ?? 'Tidak ada deskripsi'
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error memuat detail mahasiswa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail mahasiswa'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $mahasiswa = Mahasiswa::with('user')->findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'kode_prodi' => 'required|string|max:10',
                'alamat' => 'required|string|max:255',
                'nim' => 'required|string|max:15',
                'ipk' => 'required|numeric|min:0|max:4',
            ]);

            // Update data mahasiswa
            $mahasiswa->update([
                'kode_prodi' => $validatedData['kode_prodi'],
                'alamat' => $validatedData['alamat'],
                'nim' => $validatedData['nim'],
                'ipk' => $validatedData['ipk']
            ]);

            // Update nama di tabel user
            if ($mahasiswa->user) {
                $mahasiswa->user->update([
                    'name' => $validatedData['name']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating mahasiswa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data mahasiswa'
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $mahasiswa = Mahasiswa::findOrFail($id);

            // Hapus data mahasiswa
            $mahasiswa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting mahasiswa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data mahasiswa'
            ], 500);
        }
    }
}
