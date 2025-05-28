<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeController extends Controller
{
    public function index()
    {
        try {
            $periode = DB::table('m_periode')
                ->select('periode_id', 'waktu')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $periode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $periode = DB::table('m_periode')->where('periode_id', $id)->first();
            if (!$periode) {
                return response()->json(['success' => false, 'message' => 'Periode tidak ditemukan'], 404);
            }
            return response()->json(['success' => true, 'data' => $periode]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'waktu' => 'required|string|max:255'
        ]);

        try {
            $id = DB::table('m_periode')->insertGetId([
                'waktu' => $request->waktu
            ]);
            return response()->json(['success' => true, 'periode_id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'waktu' => 'required|string|max:255'
        ]);

        try {
            $affected = DB::table('m_periode')->where('periode_id', $id)->update([
                'waktu' => $request->waktu
            ]);
            if ($affected) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Periode tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('m_periode')->where('periode_id', $id)->delete();
            if ($deleted) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Periode tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
