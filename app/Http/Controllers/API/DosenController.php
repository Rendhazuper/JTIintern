<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::with(['user', 'wilayah'])->get();
        $data = $dosen->map(function ($item) {
            return [
                'id_dosen' => $item->id_dosen,
                'nama_dosen' => $item->user->name ?? '-',
                'email' => $item->user->email ?? '-',
                'wilayah' => $item->wilayah->nama_kota ?? '-', // pastikan relasi wilayah ada
                'nip' => $item->nip ?? '-',
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'wilayah_id' => 'required|integer|exists:m_wilayah,wilayah_id',
            'nip' => 'required|unique:m_dosen,nip',
        ]);

        // Email dan password sama dengan NIP
        $nip = $request->nip;
        $email = $nip . '@gmail.com';

        // Buat user baru
        $user = User::create([
            'name' => $request->nama_dosen,
            'email' => $email,
            'password' => bcrypt($nip),
            'role' => 'dosen'
        ]);

        // Buat dosen baru
        Dosen::create([
            'user_id' => $user->id_user,
            'wilayah_id' => $request->wilayah_id,
            'nip' => $nip
        ]);

        return response()->json(['success' => true]);
    }
    public function show($id)
    {
        $dosen = Dosen::with(['user', 'wilayah'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id_dosen' => $dosen->id_dosen,
                'nama_dosen' => $dosen->user->name ?? '-',
                'email' => $dosen->user->email ?? '-',
                'wilayah_id' => $dosen->wilayah_id,
                'wilayah' => $dosen->wilayah->nama_kota ?? '-',
                'nip' => $dosen->nip ?? '-',
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'wilayah_id' => 'required|integer|exists:m_wilayah,wilayah_id',
            'nip' => 'required|unique:m_dosen,nip,' . $id . ',id_dosen',
        ]);

        $dosen = Dosen::findOrFail($id);
        $user = $dosen->user;

        // Update user (nama)
        $user->name = $request->nama_dosen;
        $user->save();

        // Update dosen
        $dosen->wilayah_id = $request->wilayah_id;
        $dosen->nip = $request->nip;
        $dosen->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $dosen = Dosen::findOrFail($id);
        $user = $dosen->user;
        $dosen->delete();
        if ($user) $user->delete();
        return response()->json(['success' => true]);
    }
}
