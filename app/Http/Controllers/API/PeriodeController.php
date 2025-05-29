<?php
// filepath: d:\laragon\www\JTIintern\app\Http\Controllers\API\PeriodeController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeriodeController extends Controller
{
    /**
     * Mendapatkan semua data periode
     */
    public function index()
    {
        try {
            $periodes = DB::table('m_periode')
                ->orderBy('waktu', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $periodes,
                'message' => 'Data periode berhasil ditemukan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching periodes: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data periode',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail periode berdasarkan ID
     */
    public function show($id)
    {
        try {
            $periode = DB::table('m_periode')
                ->where('periode_id', $id)
                ->first();

            if (!$periode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $periode,
                'message' => 'Detail periode berhasil ditemukan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching periode details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail periode',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan periode baru
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'waktu' => 'required|string|max:255|unique:m_periode,waktu'
            ], [
                'waktu.required' => 'Waktu periode wajib diisi',
                'waktu.unique' => 'Periode dengan waktu tersebut sudah ada'
            ]);

            $periodeId = DB::table('m_periode')->insertGetId([
                'waktu' => $request->waktu,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Periode berhasil ditambahkan',
                'data' => [
                    'periode_id' => $periodeId
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating periode: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan periode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui periode yang ada
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'waktu' => 'required|string|max:255|unique:m_periode,waktu,' . $id . ',periode_id'
            ], [
                'waktu.required' => 'Waktu periode wajib diisi',
                'waktu.unique' => 'Periode dengan waktu tersebut sudah ada'
            ]);

            $affected = DB::table('m_periode')
                ->where('periode_id', $id)
                ->update([
                    'waktu' => $request->waktu,
                    'updated_at' => now()
                ]);

            if (!$affected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Periode berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating periode: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui periode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus periode
     */
    public function destroy($id)
    {
        try {
            // Periksa apakah periode digunakan dalam relasi
            $usedInLowongan = DB::table('m_lowongan')
                ->where('periode_id', $id)
                ->exists();

            if ($usedInLowongan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak dapat dihapus karena masih digunakan dalam data lowongan'
                ], 400);
            }

            $deleted = DB::table('m_periode')
                ->where('periode_id', $id)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Periode berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting periode: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus periode: ' . $e->getMessage()
            ], 500);
        }
    }
}