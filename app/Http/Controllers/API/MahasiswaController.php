<?php

namespace App\Http\Controllers\API;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class MahasiswaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:m_user,email',
            'password' => 'required|string|min:6',
            'nim' => 'required|string|unique:m_mahasiswa,nim',
            'id_kelas' => 'required|exists:m_kelas,id_kelas',
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
            'id_kelas' => $request->id_kelas,
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
            $mahasiswa = Mahasiswa::with(['user', 'magang', 'programStudi', 'kelas'])
                ->where('id_mahasiswa', $id)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa tidak ditemukan'
                ], 404);
            }

            // Load skills melalui user_id dari tabel t_skill_mahasiswa
            $skills = DB::table('t_skill_mahasiswa as sm')
                ->join('m_skill as s', 'sm.skill_id', '=', 's.skill_id')
                ->where('sm.user_id', $mahasiswa->id_user)
                ->select('s.nama', 'sm.lama_skill')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'name' => $mahasiswa->user->name ?? '-',
                    'email' => $mahasiswa->user->email ?? '-',
                    'nim' => $mahasiswa->nim,
                    'nama_kelas' => $mahasiswa->kelas->nama_kelas ?? '-',
                    'status_magang' => $mahasiswa->magang->status ?? 'Belum Magang',
                    'alamat' => $mahasiswa->alamat ?? '-',
                    'ipk' => $mahasiswa->ipk ?? '-',
                    'skills' => $skills,
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
                'id_kelas' => 'required|exists:m_kelas,id_kelas',
                'alamat' => 'required|string|max:255',
                'nim' => 'required|string|max:15',
                'ipk' => 'required|numeric|min:0|max:4',
            ]);

            // Update data mahasiswa
            $mahasiswa->update([
                'id_kelas' => $validatedData['id_kelas'],
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

    public function index(Request $request)
    {
        try {
            $query = DB::table('m_mahasiswa as m')
                ->leftJoin('m_user as u', 'm.id_user', '=', 'u.id_user')
                ->leftJoin('m_magang as mg', 'm.id_mahasiswa', '=', 'mg.id_mahasiswa')
                ->leftJoin('m_kelas as k', 'm.id_kelas', '=', 'k.id_kelas')
                ->select(
                    'm.id_mahasiswa',
                    'u.name',
                    'u.email',
                    'm.nim',
                    'm.alamat',
                    'm.ipk',
                    'k.id_kelas',
                    'k.nama_kelas',
                    DB::raw('CASE 
                WHEN mg.status = "active" THEN "Sedang Magang"
                WHEN mg.status = "completed" THEN "Selesai Magang"
                WHEN mg.status = "pending" THEN "Menunggu Konfirmasi"
                ELSE "Belum Magang"
            END as status_magang')
                );

            // Perubahan cara pengecekan parameter kelas
            if ($request->filled('kelas')) {  // Menggunakan filled() untuk memastikan parameter ada dan tidak kosong
                $query->where('m.id_kelas', '=', $request->kelas);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('u.name', 'like', "%{$searchTerm}%")
                        ->orWhere('m.nim', 'like', "%{$searchTerm}%")
                        ->orWhere('u.email', 'like', "%{$searchTerm}%");
                });
            }

            $mahasiswa = $query->get();

            return response()->json([
                'success' => true,
                'data' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add new method to get kelas options
    public function getKelasOptions()
    {
        try {
            $kelas = DB::table('m_kelas')
                ->select('id_kelas', 'nama_kelas', 'kode_prodi', 'tahun_masuk')
                ->orderBy('nama_kelas')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kelas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function import(Request $request)
    {
        // Validate the request
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $path = $request->file('csv_file')->getRealPath();

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak dapat diakses'
                ], 400);
            }

            $file = fopen($path, 'r');
            $header = fgetcsv($file); // Get header row

            // Expected headers (ubah id_kelas menjadi nama_kelas)
            $expectedHeaders = ['nama', 'nim', 'alamat', 'ipk', 'nama_kelas'];

            // Validate headers
            if ($header !== $expectedHeaders) {
                fclose($file);
                return response()->json([
                    'success' => false,
                    'message' => 'Format CSV tidak sesuai. Header yang diharapkan: ' . implode(', ', $expectedHeaders)
                ], 400);
            }

            $imported = 0;
            $errors = [];

            // Ambil semua data kelas untuk mapping
            $allKelas = \App\Models\Kelas::pluck('id_kelas', 'nama_kelas')->toArray();

            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                // Skip if row doesn't have enough columns
                if (count($row) !== count($expectedHeaders)) {
                    continue;
                }

                $namaKelas = trim($row[4]); // Mengambil nama_kelas dari CSV
                
                // Cari id_kelas berdasarkan nama_kelas
                $idKelas = array_key_exists($namaKelas, $allKelas) ? $allKelas[$namaKelas] : null;

                $data = [
                    'nama' => $row[0],
                    'nim' => $row[1],
                    'alamat' => $row[2],
                    'ipk' => $row[3],
                    'id_kelas' => $idKelas, // Gunakan id_kelas hasil pencarian
                    'nama_kelas' => $namaKelas // Simpan juga nama_kelas untuk pesan error
                ];

                // Validate data
                $validator = Validator::make($data, [
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|unique:m_mahasiswa,nim',
                    'alamat' => 'required|string',
                    'ipk' => 'nullable|numeric|min:0|max:4',
                    'id_kelas' => 'required|exists:m_kelas,id_kelas',
                ], [
                    'id_kelas.required' => "Nama kelas '{$namaKelas}' tidak ditemukan",
                    'id_kelas.exists' => "Nama kelas '{$namaKelas}' tidak ditemukan"
                ]);

                if ($validator->fails()) {
                    $errors[] = "Error pada baris data {$data['nim']}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                try {
                    // Create user
                    $user = \App\Models\User::create([
                        'name' => $data['nama'],
                        'email' => strtolower($data['nim']) . '@student.com',
                        'password' => bcrypt($data['nim']),
                        'role' => 'mahasiswa'
                    ]);

                    // Create mahasiswa
                    Mahasiswa::create([
                        'nim' => $data['nim'],
                        'id_user' => $user->id_user,
                        'alamat' => $data['alamat'],
                        'ipk' => $data['ipk'],
                        'id_kelas' => $data['id_kelas'], // ID kelas yang sudah dikonversi
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error pada data {$data['nim']}: " . $e->getMessage();
                }
            }

            fclose($file);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$imported} data mahasiswa",
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($file)) {
                fclose($file);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Get filtered data
            $query = DB::table('m_mahasiswa as m')
                ->leftJoin('m_user as u', 'm.id_user', '=', 'u.id_user')
                ->leftJoin('m_magang as mg', 'm.id_mahasiswa', '=', 'mg.id_mahasiswa')
                ->leftJoin('m_kelas as k', 'm.id_kelas', '=', 'k.id_kelas')
                ->select(
                    'u.name',
                    'm.nim',
                    'k.nama_kelas',
                    'm.alamat',
                    'm.ipk',
                    DB::raw('CASE 
                        WHEN mg.status = "active" THEN "Sedang Magang"
                        WHEN mg.status = "completed" THEN "Selesai Magang"
                        WHEN mg.status = "pending" THEN "Menunggu Konfirmasi"
                        ELSE "Belum Magang"
                    END as status_magang')
                );

            // Apply filters if any
            if ($request->filled('kelas')) {
                $query->where('m.id_kelas', '=', $request->kelas);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('u.name', 'like', "%{$searchTerm}%")
                        ->orWhere('m.nim', 'like', "%{$searchTerm}%");
                });
            }

            $mahasiswa = $query->orderBy('u.name')->get();

            // Get current timestamp
            $timestamp = Carbon::now()->format('d-m-Y_H-i-s');

            // Load the view for PDF
            $pdf = Pdf::loadView('exports.mahasiswa-pdf', [
                'mahasiswa' => $mahasiswa,
                'timestamp' => Carbon::now()->format('d F Y H:i:s'),
                'total' => $mahasiswa->count()
            ]);

            // Set paper to landscape for better table viewing
            $pdf->setPaper('a4', 'landscape');

            // Return the PDF for download
            return $pdf->download("data_mahasiswa_{$timestamp}.pdf");

        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengeksport PDF'
            ], 500);
        }
    }
    

    public function dashboard()
    {
        return view('pages.mahasiswa.dashboard');
    }

}
