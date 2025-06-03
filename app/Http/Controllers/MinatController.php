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

    public function getMinat()
    {
        $minat = Minat::all();
        return response()->json($minat);
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