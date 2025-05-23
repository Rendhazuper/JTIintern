<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function index()
    {
        return view('pages.data_perusahaan');
    }

    public function getData()
    {
        $perusahaan = Perusahaan::all();
        return response()->json([
            'success' => true,
            'data' => $perusahaan
        ]);
    }

    public function tambahPerusahaan(Request $request)
    {
        try {
            $request->validate([
                'nama_perusahaan' => 'required|string',
                'alamat_perusahaan' => 'required|string',
                'kota' => 'required|string',
                'contact_person' => 'required|string',
                'email' => 'required|email',
                'instagram' => 'nullable|string',
                'website' => 'nullable|url'
            ]);

            $perusahaan = Perusahaan::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil ditambahkan',
                'data' => $perusahaan
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
            $perusahaan = Perusahaan::findOrFail($id);
            return view('pages.detail_perusahaan', compact('perusahaan'));
        } catch (\Exception $e) {
            return redirect()->route('data-perusahaan')->with('error', 'Perusahaan tidak ditemukan');
        }
    }
}