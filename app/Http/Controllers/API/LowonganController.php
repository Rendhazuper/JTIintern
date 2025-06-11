<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Services\KapasitasLowonganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Skill;
use App\Models\Jenis;

class LowonganController extends Controller
{
    protected $kapasitasService;

    public function __construct(KapasitasLowonganService $kapasitasService)
    {
        $this->kapasitasService = $kapasitasService;
    }

    public function index(Request $request)
    {
        try {
            // Get lowongan with relationships
            $query = Lowongan::with(['perusahaan.wilayah'])
                     ->orderBy('created_at', 'desc');

            if ($request->has('perusahaan_id') && $request->perusahaan_id) {
                $query->where('perusahaan_id', $request->perusahaan_id);
            }

            $lowongan = $query->get();

            // Get all lowongan IDs
            $lowonganIds = $lowongan->pluck('id_lowongan')->toArray();

            // Get skills manually
            $skillsData = DB::table('t_skill_lowongan as tsl')
                ->join('m_skill as ms', 'tsl.id_skill', '=', 'ms.skill_id')
                ->whereIn('tsl.id_lowongan', $lowonganIds)
                ->select('tsl.id_lowongan', 'ms.skill_id', 'ms.nama')
                ->get();

            // ✅ TAMBAHAN: Get minat data for listing
            $minatData = DB::table('t_minat_lowongan as tml')
                ->join('m_minat as mm', 'tml.minat_id', '=', 'mm.minat_id')
                ->whereIn('tml.id_lowongan', $lowonganIds)
                ->select('tml.id_lowongan', 'mm.minat_id', 'mm.nama_minat')
                ->get();

            // Group skills by lowongan ID
            $skillsByLowongan = [];
            foreach ($skillsData as $skill) {
                $skillsByLowongan[$skill->id_lowongan][] = [
                    'skill_id' => $skill->skill_id,
                    'nama' => $skill->nama
                ];
            }

            // ✅ Group minat by lowongan ID
            $minatByLowongan = [];
            foreach ($minatData as $minat) {
                $minatByLowongan[$minat->id_lowongan][] = [
                    'minat_id' => $minat->minat_id,
                    'nama_minat' => $minat->nama_minat
                ];
            }

            $formattedLowongan = $lowongan->map(function ($item) use ($skillsByLowongan, $minatByLowongan) {
                return [
                    'id_lowongan' => $item->id_lowongan,
                    'judul_lowongan' => $item->judul_lowongan,
                    'deskripsi' => $item->deskripsi,
                    'kapasitas' => $item->kapasitas,
                    'min_ipk' => $item->min_ipk,
                    'perusahaan' => [
                        'nama_perusahaan' => $item->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                        'nama_kota' => $item->perusahaan->wilayah->nama_kota ?? 'Tidak Diketahui',
                    ],
                    'skills' => $skillsByLowongan[$item->id_lowongan] ?? [],
                    'minat' => $minatByLowongan[$item->id_lowongan] ?? [], // ✅ TAMBAHAN
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedLowongan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lowongan: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data lowongan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // ✅ PERBAIKI: Validasi yang lebih lengkap
        $request->validate([
            'judul_lowongan' => 'required|string|max:255',
            'perusahaan_id' => 'required|exists:m_perusahaan,perusahaan_id',
            'periode_id' => 'required|exists:m_periode,periode_id',
            'jenis_id' => 'required|exists:m_jenis,jenis_id',
            'kapasitas' => 'required|integer|min:1',
            'min_ipk' => 'required|numeric|min:0|max:4.00',
            'deskripsi' => 'required|string',
            'skill_id' => 'required|array|min:1',
            'skill_id.*' => 'required|exists:m_skill,skill_id',
            'minat_id' => 'required|array|min:1',
            'minat_id.*' => 'required|exists:m_minat,minat_id',
        ], [
            'skill_id.required' => 'Minimal pilih satu skill',
            'skill_id.min' => 'Minimal pilih satu skill',
            'minat_id.required' => 'Minimal pilih satu minat',
            'minat_id.min' => 'Minimal pilih satu minat',
        ]);

        try {
            DB::beginTransaction();
            
            // ✅ DEBUG: Log input data
            Log::info('Creating lowongan with input:', [
                'judul' => $request->judul_lowongan,
                'skills' => $request->skill_id,
                'minat' => $request->minat_id,
                'all_request' => $request->all()
            ]);
            
            // Create lowongan
            $lowongan = new Lowongan();
            $lowongan->judul_lowongan = $request->judul_lowongan;
            $lowongan->perusahaan_id = $request->perusahaan_id;
            $lowongan->periode_id = $request->periode_id;
            $lowongan->jenis_id = $request->jenis_id;
            $lowongan->kapasitas = $request->kapasitas;
            $lowongan->min_ipk = $request->min_ipk;
            $lowongan->deskripsi = $request->deskripsi;
            $lowongan->save();
            
            Log::info('Lowongan created successfully:', [
                'id' => $lowongan->id_lowongan
            ]);
            
            // ✅ Add skills
            foreach ($request->skill_id as $skillId) {
                DB::table('t_skill_lowongan')->insert([
                    'id_lowongan' => $lowongan->id_lowongan,
                    'id_skill' => $skillId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            Log::info('Skills added successfully:', [
                'count' => count($request->skill_id)
            ]);
            
            // ✅ PERBAIKI: Add minat dengan error handling yang lebih baik
            foreach ($request->minat_id as $minatId) {
                $insertResult = DB::table('t_minat_lowongan')->insert([
                    'id_lowongan' => $lowongan->id_lowongan,
                    'minat_id' => $minatId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                if (!$insertResult) {
                    throw new \Exception("Failed to insert minat_id: $minatId");
                }
            }
            
            Log::info('Minat added successfully:', [
                'count' => count($request->minat_id)
            ]);
            
            // Initialize capacity record
            $this->kapasitasService->initializeKapasitas($lowongan->id_lowongan, $request->kapasitas);
            
            DB::commit();
            
            // ✅ VERIFIKASI: Final verification
            $savedSkills = DB::table('t_skill_lowongan')
                ->where('id_lowongan', $lowongan->id_lowongan)
                ->count();
                
            $savedMinat = DB::table('t_minat_lowongan')
                ->where('id_lowongan', $lowongan->id_lowongan)
                ->count();
                
            Log::info('Final verification:', [
                'lowongan_id' => $lowongan->id_lowongan,
                'saved_skills' => $savedSkills,
                'saved_minat' => $savedMinat,
                'expected_skills' => count($request->skill_id),
                'expected_minat' => count($request->minat_id)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil ditambahkan.',
                'data' => $lowongan,
                'verification' => [
                    'skills_saved' => $savedSkills,
                    'minat_saved' => $savedMinat,
                    'skills_expected' => count($request->skill_id),
                    'minat_expected' => count($request->minat_id)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding lowongan: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan lowongan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Load lowongan with relationships
            $lowongan = Lowongan::with(['perusahaan', 'periode', 'jenis'])->findOrFail($id);
            
            // Get capacity information
            $kapasitas = DB::table('t_kapasitas_lowongan')
                ->where('id_lowongan', $id)
                ->first();
            
            // Get skills for this lowongan
            $skills = DB::table('t_skill_lowongan as tsl')
                ->join('m_skill as ms', 'tsl.id_skill', '=', 'ms.skill_id')
                ->where('tsl.id_lowongan', $id)
                ->select('ms.skill_id', 'ms.nama')
                ->get();
            
            // ✅ Get minat for this lowongan
            $minat = DB::table('t_minat_lowongan as tml')
                ->join('m_minat as m', 'tml.minat_id', '=', 'm.minat_id')
                ->where('tml.id_lowongan', $id)
                ->select('m.minat_id', 'm.nama_minat')
                ->get();
            
            Log::info('Showing lowongan detail:', [
                'id' => $id,
                'skills_count' => $skills->count(),
                'minat_count' => $minat->count()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id_lowongan' => $lowongan->id_lowongan,
                    'judul_lowongan' => $lowongan->judul_lowongan,
                    'kapasitas' => $lowongan->kapasitas,
                    'kapasitas_tersedia' => $kapasitas ? $kapasitas->kapasitas_tersedia : null,
                    'kapasitas_total' => $kapasitas ? $kapasitas->kapasitas_total : $lowongan->kapasitas,
                    'min_ipk' => $lowongan->min_ipk,
                    'deskripsi' => $lowongan->deskripsi,
                    'perusahaan' => [
                        'perusahaan_id' => $lowongan->perusahaan->perusahaan_id,
                        'nama_perusahaan' => $lowongan->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                    ],
                    'periode' => [
                        'periode_id' => $lowongan->periode->periode_id,
                        'waktu' => $lowongan->periode->waktu ?? 'Tidak Diketahui',
                    ],
                    'skills' => $skills->map(function($skill) {
                        return [
                            'skill_id' => $skill->skill_id,
                            'nama' => $skill->nama ?? 'Tidak Diketahui',
                        ];
                    }),
                    'minat' => $minat->map(function($m) {
                        return [
                            'minat_id' => $m->minat_id,
                            'nama_minat' => $m->nama_minat ?? 'Tidak Diketahui',
                        ];
                    }),
                    'jenis' => [
                        'jenis_id' => $lowongan->jenis->jenis_id,
                        'nama_jenis' => $lowongan->jenis->nama_jenis ?? 'Tidak Diketahui',
                    ],
                    'created_at' => $lowongan->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lowongan detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail lowongan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // ✅ PERBAIKI: Validasi yang konsisten dengan store
        $request->validate([
            'judul_lowongan' => 'required|string|max:255',
            'perusahaan_id' => 'required|exists:m_perusahaan,perusahaan_id',
            'periode_id' => 'required|exists:m_periode,periode_id',
            'jenis_id' => 'required|exists:m_jenis,jenis_id',
            'kapasitas' => 'required|integer|min:1',
            'min_ipk' => 'required|numeric|min:0|max:4.00',
            'deskripsi' => 'required|string',
            'skill_id' => 'required|array|min:1',
            'skill_id.*' => 'required|exists:m_skill,skill_id',
            'minat_id' => 'required|array|min:1',
            'minat_id.*' => 'required|exists:m_minat,minat_id',
        ], [
            'skill_id.required' => 'Minimal pilih satu skill',
            'skill_id.min' => 'Minimal pilih satu skill',
            'minat_id.required' => 'Minimal pilih satu minat',
            'minat_id.min' => 'Minimal pilih satu minat',
        ]);

        try {
            DB::beginTransaction();

            // ✅ DEBUG: Log update data
            Log::info('Updating lowongan with input:', [
                'id' => $id,
                'judul' => $request->judul_lowongan,
                'skills' => $request->skill_id,
                'minat' => $request->minat_id
            ]);

            // Find and update lowongan
            $lowongan = Lowongan::findOrFail($id);
            $oldKapasitas = $lowongan->kapasitas;
            
            $lowongan->judul_lowongan = $request->judul_lowongan;
            $lowongan->perusahaan_id = $request->perusahaan_id;
            $lowongan->periode_id = $request->periode_id;
            $lowongan->jenis_id = $request->jenis_id;
            $lowongan->kapasitas = $request->kapasitas;
            $lowongan->min_ipk = $request->min_ipk;
            $lowongan->deskripsi = $request->deskripsi;
            $lowongan->save();
            
            // ✅ Delete and recreate skills
            DB::table('t_skill_lowongan')->where('id_lowongan', $id)->delete();
            foreach ($request->skill_id as $skillId) {
                DB::table('t_skill_lowongan')->insert([
                    'id_lowongan' => $id,
                    'id_skill' => $skillId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // ✅ Delete and recreate minat
            DB::table('t_minat_lowongan')->where('id_lowongan', $id)->delete();
            foreach ($request->minat_id as $minatId) {
                $insertResult = DB::table('t_minat_lowongan')->insert([
                    'id_lowongan' => $id,
                    'minat_id' => $minatId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                if (!$insertResult) {
                    throw new \Exception("Failed to update minat_id: $minatId");
                }
            }
            
            // Update kapasitas if changed
            if ($oldKapasitas != $request->kapasitas) {
                $this->kapasitasService->updateKapasitasTotal($id, $request->kapasitas);
            }
            
            DB::commit();

            // ✅ VERIFIKASI: Final verification for update
            $savedSkills = DB::table('t_skill_lowongan')
                ->where('id_lowongan', $id)
                ->count();
                
            $savedMinat = DB::table('t_minat_lowongan')
                ->where('id_lowongan', $id)
                ->count();
                
            Log::info('Update verification:', [
                'lowongan_id' => $id,
                'saved_skills' => $savedSkills,
                'saved_minat' => $savedMinat,
                'expected_skills' => count($request->skill_id),
                'expected_minat' => count($request->minat_id)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil diperbarui',
                'data' => $lowongan,
                'verification' => [
                    'skills_saved' => $savedSkills,
                    'minat_saved' => $savedMinat,
                    'skills_expected' => count($request->skill_id),
                    'minat_expected' => count($request->minat_id)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating lowongan: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui lowongan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // ✅ Delete all related data
            DB::table('t_skill_lowongan')->where('id_lowongan', $id)->delete();
            DB::table('t_minat_lowongan')->where('id_lowongan', $id)->delete(); // ✅ TAMBAHAN
            DB::table('t_kapasitas_lowongan')->where('id_lowongan', $id)->delete();
            
            // Delete the lowongan
            $lowongan = Lowongan::findOrFail($id);
            $lowongan->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus lowongan.',
            ], 500);
        }
    }

    public function getSkill()
    {
        try {
            $skills = Skill::select('skill_id', 'nama')
                ->orderBy('nama')
                ->get();
            return response()->json([
                'success' => true,
                'data' => $skills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data skill.'
            ], 500);
        }
    }

    public function getJenis()
    {
        try {
            $jenis = Jenis::select('jenis_id', 'nama_jenis')
                ->orderBy('nama_jenis')
                ->get();
            return response()->json([
                'success' => true,
                'data' => $jenis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jenis.'
            ], 500);
        }
    }

    public function getAvailableCapacity($id)
    {
        try {
            $lowongan = Lowongan::findOrFail($id);
            $kapasitas = $lowongan->kapasitas()->first();
            
            if (!$kapasitas) {
                $this->kapasitasService->syncKapasitas($id);
                $kapasitas = $lowongan->kapasitas()->first();
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id_lowongan' => $id,
                    'kapasitas_total' => $kapasitas ? $kapasitas->kapasitas_total : $lowongan->kapasitas,
                    'kapasitas_tersedia' => $kapasitas ? $kapasitas->kapasitas_tersedia : 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting capacity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi kapasitas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncCapacity($id)
    {
        try {
            $result = $this->kapasitasService->syncKapasitas($id);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kapasitas berhasil disinkronkan'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyinkronkan data kapasitas'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error syncing capacity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}