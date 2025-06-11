<?php

namespace App\Http\Controllers;

use App\Models\Minat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MinatController extends Controller
{
    public function index()
    {
        return view('pages.minat');
    }

    // Update method getMinat() agar response format konsisten:
    public function getMinat()
    {
        try {
            $minat = Minat::select('minat_id', 'nama_minat', 'deskripsi')
                ->orderBy('nama_minat')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $minat,
                'message' => 'Data minat berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data minat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_minat' => 'required|string|max:255|unique:m_minat,nama_minat',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $minat = Minat::create([
            'nama_minat' => $request->nama_minat,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Minat berhasil ditambahkan',
            'data' => $minat
        ]);
    }

    public function show($id)
    {
        $minat = Minat::findOrFail($id);
        return response()->json($minat);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_minat' => 'required|string|max:255|unique:m_minat,nama_minat,'.$id.',minat_id',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $minat = Minat::findOrFail($id);
        $minat->update([
            'nama_minat' => $request->nama_minat,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Minat berhasil diperbarui',
            'data' => $minat
        ]);
    }

    public function destroy($id)
    {
        $minat = Minat::findOrFail($id);
        $minat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Minat berhasil dihapus'
        ]);
    }
}