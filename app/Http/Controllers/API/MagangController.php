<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Magang;
use App\Services\KapasitasLowonganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MagangController extends Controller
{
    protected $kapasitasService;

    public function __construct(KapasitasLowonganService $kapasitasService)
    {
        $this->kapasitasService = $kapasitasService;
    }

    public function index(Request $request)
    {
        try {
            $magang = Magang::with(['mahasiswa.user', 'lowongan.perusahaan', 'dokumen'])
                ->get()
                ->map(function ($item) {
                    $auth = 'Menunggu';
                    if ($item->lowongan && isset($item->lowongan->lamaran) && $item->lowongan->lamaran->isNotEmpty()) {
                        $auth = $item->lowongan->lamaran->first()->auth ?? 'Menunggu';
                    }

                    return [
                        'id' => $item->id_magang,
                        'mahasiswa' => [
                            'name' => $item->mahasiswa->user->name ?? 'Tidak Diketahui',
                            'nim' => $item->mahasiswa->nim ?? 'Tidak Diketahui',
                            'email' => $item->mahasiswa->user->email ?? 'Tidak Diketahui',
                        ],
                        'judul_lowongan' => $item->lowongan->judul_lowongan ?? $item->lowongan->posisi ?? 'Tidak Diketahui',
                        'perusahaan' => [
                            'nama_perusahaan' => $item->lowongan->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                        ],
                        'status' => $item->status ?? 'Menunggu',
                        'auth' => $auth,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $magang
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching data magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data magang: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            // Ambil data magang berdasarkan ID dengan relasi yang diperlukan
            $magang = Magang::with(['mahasiswa.user', 'lowongan.perusahaan'])
                ->findOrFail($id);

            // Logging untuk debugging
            Log::info('Magang data fetched:', ['id' => $id, 'data' => $magang->toArray()]);

            // Format data dengan pengecekan null yang lebih aman
            $formattedData = [
                'id' => $magang->id_magang,
                'mahasiswa' => [
                    'name' => $magang->mahasiswa->user->name ?? 'Tidak Diketahui',
                    'nim' => $magang->mahasiswa->nim ?? 'Tidak Diketahui',
                    'email' => $magang->mahasiswa->user->email ?? 'Tidak Diketahui',
                    'prodi' => 'Teknologi Informasi', // Default atau ambil dari model
                    'skills' => [] // Default atau ambil dari query terpisah
                ],
                'lowongan' => [
                    'judul_lowongan' => $magang->lowongan->judul_lowongan ?? $magang->lowongan->posisi ?? 'Tidak Diketahui',
                    'deskripsi' => $magang->lowongan->deskripsi ?? 'Tidak Diketahui',
                    'persyaratan' => $magang->lowongan->kapasitas ?? 'Tidak Diketahui',
                    'tanggal_mulai' => $magang->lowongan->tanggal_mulai ?? 'Tidak Diketahui',
                    'tanggal_selesai' => $magang->lowongan->tanggal_selesai ?? 'Tidak Diketahui',
                ],
                'perusahaan' => [
                    'nama_perusahaan' => $magang->lowongan->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                    'alamat_perusahaan' => $magang->lowongan->perusahaan->alamat_perusahaan ?? 'Tidak Diketahui',
                    'kota' => $magang->lowongan->perusahaan->wilayah->nama_kota ?? 'Tidak Diketahui',
                    'contact_person' => $magang->lowongan->perusahaan->contact_person ?? 'Tidak Diketahui',
                    'email' => $magang->lowongan->perusahaan->email ?? 'Tidak Diketahui',
                ],
                'dokumen' => [
                    'cv_url' => '#', // Placeholder atau ambil dari model
                    'surat_url' => '#', // Placeholder atau ambil dari model
                ],
                'status' => $magang->status ?? 'Belum Diproses',
            ];

            // Fetch skills secara terpisah untuk menghindari null reference
            if ($magang->mahasiswa && $magang->mahasiswa->id_user) {
                $skills = DB::table('t_skill_mahasiswa as sm')
                    ->join('m_skill as s', 'sm.skill_id', '=', 's.skill_id')
                    ->where('sm.user_id', $magang->mahasiswa->id_user)
                    ->select('s.nama')
                    ->get()
                    ->pluck('nama')
                    ->toArray();

                $formattedData['mahasiswa']['skills'] = $skills;
            }

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Specific handling for "not found" errors
            Log::warning("Magang with ID {$id} not found.");

            return response()->json([
                'success' => false,
                'message' => "Data magang dengan ID {$id} tidak ditemukan."
            ], 404); // Return proper 404 status code

        } catch (\Exception $e) {
            // General error handling remains the same
            Log::error('Error fetching detail magang: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail magang: ' . $e->getMessage()
            ], 500);
        }
    }



    public function accept($id)
    {
        try {
            DB::beginTransaction();
            
            // Find internship application
            $magang = Magang::findOrFail($id);
            $id_lowongan = $magang->id_lowongan;
            
            // Check capacity first
            if (!$this->kapasitasService->hasAvailableCapacity($id_lowongan)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menerima permintaan magang karena kapasitas sudah penuh.'
                ], 400);
            }
            
            // Update status in m_magang
            $magang->status = 'aktif';
            $magang->save();
            
            // Update auth column in lamaran table
            $lamaran = $magang->lamaran->first();
            if ($lamaran) {
                $lamaran->auth = 'diterima';
                $lamaran->save();
            }
            
            // Decrement available capacity
            $capacityUpdated = $this->kapasitasService->decrementKapasitas($id_lowongan);
            
            if (!$capacityUpdated) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kapasitas tersedia.'
                ], 500);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Permintaan magang berhasil diterima.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error accepting magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerima permintaan magang: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            DB::beginTransaction();
            
            // Find internship application
            $magang = Magang::findOrFail($id);
            $id_lowongan = $magang->id_lowongan;
            
            // Only increment capacity if status was previously 'aktif'
            $wasActive = $magang->status === 'aktif';
            
            // Delete entry in related lamaran table
            $lamaran = $magang->lamaran->first();
            if ($lamaran) {
                $lamaran->delete();
            }
            
            // Delete entry in m_magang table
            $magang->delete();
            
            // If was active, increment available capacity
            if ($wasActive) {
                $this->kapasitasService->incrementKapasitas($id_lowongan);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Permintaan magang dan lamaran terkait berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak permintaan magang: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getAvailable()
    {
        try {
            Log::info('Fetching available magang entries');

            // Get internships without assigned supervisors
            $availableMagang = Magang::with(['mahasiswa.user', 'lowongan.perusahaan'])
                ->whereNull('id_dosen')  // This is the crucial part
                ->orWhere('id_dosen', 0)
                ->get();

            Log::info('Found ' . $availableMagang->count() . ' available magang entries');

            return response()->json([
                'success' => true,
                'data' => $availableMagang
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching available magang: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load available internships: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignPendingMagang(Request $request)
    {
        try {
            $request->validate([
                'magang_id' => 'required|exists:m_magang,id_magang',
                'dosen_id' => 'required|exists:m_dosen,id_dosen',
            ]);

            $magang = Magang::findOrFail($request->magang_id);
            $magang->id_dosen = $request->dosen_id;
            $magang->save();

            return response()->json([
                'success' => true,
                'message' => 'Dosen berhasil ditugaskan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning dosen to pending magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan dosen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkDosen($id)
    {
        try {
            $magang = Magang::findOrFail($id);

            return response()->json([
                'success' => true,
                'has_dosen' => !empty($magang->id_dosen)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa data dosen pembimbing'
            ], 500);
        }
    }

    public function assignDosen(Request $request, $id)
    {
        try {
            $magang = Magang::findOrFail($id);
            $magang->id_dosen = $request->dosen_id;
            $magang->save();

            return response()->json([
                'success' => true,
                'message' => 'Dosen berhasil ditugaskan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan dosen: ' . $e->getMessage()
            ], 500);
        }
    }
}
