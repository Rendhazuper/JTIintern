<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Skill;
use App\Models\Jenis;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Lowongan::with(['perusahaan.wilayah', 'skill', 'jenis'])->orderBy('created_at', 'desc');

            if ($request->has('perusahaan_id') && $request->perusahaan_id) {
                $query->where('perusahaan_id', $request->perusahaan_id);
            }

            $lowongan = $query->get();

            $formattedLowongan = $lowongan->map(function ($item) {
                return [
                    'id_lowongan' => $item->id_lowongan,
                    'judul_lowongan' => $item->judul_lowongan,
                    'deskripsi' => $item->deskripsi,
                    'kapasitas' => $item->kapasitas,

                    'perusahaan' => [
                        'nama_perusahaan' => $item->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                        'nama_kota' => $item->perusahaan->wilayah->nama_kota ?? 'Tidak Diketahui',
                    ],
                    'skill' => [
                        'nama_skill' => $item->skill->nama ?? 'Tidak Diketahui',
                    ],
                    'jenis' => [
                        'nama_jenis' => $item->jenis->nama_jenis ?? 'Tidak Diketahui',
                    ],
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedLowongan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data lowongan'
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'judul_lowongan' => 'required|string|max:255',
            'perusahaan_id' => 'required|exists:m_perusahaan,perusahaan_id',
            'periode_id' => 'required|exists:m_periode,periode_id',
            'skill_id' => 'required|exists:m_skill,skill_id',
            'jenis_id' => 'required|exists:m_jenis,jenis_id',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'required|string',

        ]);

        try {
            Lowongan::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil ditambahkan.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan lowongan.',
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $lowongan = Lowongan::with(['perusahaan', 'periode', 'skill', 'jenis'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id_lowongan' => $lowongan->id_lowongan,
                    'judul_lowongan' => $lowongan->judul_lowongan,
                    'kapasitas' => $lowongan->kapasitas,
                    'deskripsi' => $lowongan->deskripsi,
                    'perusahaan' => [
                        'nama_perusahaan' => $lowongan->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                    ],
                    'periode' => [
                        'waktu' => $lowongan->periode->waktu ?? 'Tidak Diketahui',
                    ],
                    'skill' => [
                        'nama_skill' => $lowongan->skill->nama ?? 'Tidak Diketahui',
                    ],
                    'jenis' => [
                        'nama_jenis' => $lowongan->jenis->nama_jenis ?? 'Tidak Diketahui',
                    ],
                    'created_at' => $lowongan->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lowongan detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail lowongan.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_lowongan' => 'required|string|max:255',
            'perusahaan_id' => 'required|exists:m_perusahaan,perusahaan_id',
            'periode_id' => 'required|exists:m_periode,periode_id',
            'skill_id' => 'required|exists:m_skill,skill_id',
            'jenis_id' => 'required|exists:m_jenis,jenis_id',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'required|string',
        ]);

        try {
            $lowongan = Lowongan::findOrFail($id);
            $lowongan->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui lowongan.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $lowongan = Lowongan::findOrFail($id);
            $lowongan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
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
            $skills = Skill::all();
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
            $jenis = Jenis::all();
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
}
