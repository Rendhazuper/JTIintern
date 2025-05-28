<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index()
    {
        try {
            $kelas = DB::table('m_kelas')
                ->select('id_kelas', 'nama_kelas', 'kode_prodi', 'tahun_masuk')
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

    public function getProdi()
    {
        try {
            $prodi = DB::table('m_prodi')
                ->select('kode_prodi', 'nama_prodi')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $prodi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kode_prodi' => 'required|string|max:50',
            'tahun_masuk' => 'required|integer'
        ]);

        DB::table('m_kelas')->insert([
            'nama_kelas' => $request->nama_kelas,
            'kode_prodi' => $request->kode_prodi,
            'tahun_masuk' => $request->tahun_masuk
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $kelas = DB::table('m_kelas')->where('id_kelas', $id)->first();
        return response()->json(['success' => true, 'data' => $kelas]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kode_prodi' => 'required|string|max:50',
            'tahun_masuk' => 'required|integer'
        ]);
        DB::table('m_kelas')->where('id_kelas', $id)->update([
            'nama_kelas' => $request->nama_kelas,
            'kode_prodi' => $request->kode_prodi,
            'tahun_masuk' => $request->tahun_masuk
        ]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::table('m_kelas')->where('id_kelas', $id)->delete();
        return response()->json(['success' => true]);
    }
}
