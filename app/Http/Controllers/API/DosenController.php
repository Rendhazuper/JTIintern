<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Magang;

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

    public function withPerusahaan()
    {
        try {
            // Remove this line:
            // DB::statement("SET SESSION query_cache_type = OFF");

            // Keep everything else the same
            $dosens = Dosen::with(['user', 'wilayah'])->get();

            $dosens = $dosens->map(function ($dosen) {
                try {
                    // Load magangBimbingan relationship manually
                    $bimbingan = Magang::where('id_dosen', $dosen->id_dosen)
                        ->where(function ($query) {
                            $query->where('status', '!=', 'ditolak')
                                ->orWhereNull('status');
                        })
                        ->get(['id_magang', 'id_mahasiswa', 'id_lowongan', 'status', 'created_at']);

                    // Log count for debugging
                    Log::info("Dosen ID {$dosen->id_dosen} has {$bimbingan->count()} bimbingan");

                    // Create consistent property names
                    $dosen->magangBimbingan = $bimbingan;
                    $dosen->magang_bimbingan = $bimbingan;
                } catch (\Exception $e) {
                    Log::error('Error loading magangBimbingan for dosen ID ' . $dosen->id_dosen . ': ' . $e->getMessage());
                    $dosen->magangBimbingan = [];
                    $dosen->magang_bimbingan = [];
                }

                return $dosen;
            });

            return response()->json([
                'success' => true,
                'data' => $dosens,
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            Log::error('Error in withPerusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function withDetails()
    {
        try {
            // Get dosen with relevant relationships
            $dosens = Dosen::with([
                'user',
                'wilayah',
                'skills.skill'
            ])->get();

            $dosens = $dosens->map(function ($dosen) {
                try {
                    // Load magangBimbingan relationship manually
                    $bimbingan = Magang::where('id_dosen', $dosen->id_dosen)
                        ->where(function ($query) {
                            $query->where('status', '!=', 'ditolak')
                                ->orWhereNull('status');
                        })
                        ->get(['id_magang', 'id_mahasiswa', 'id_lowongan', 'status', 'created_at']);

                    // Create consistent property names
                    $dosen->magangBimbingan = $bimbingan;
                    $dosen->magang_bimbingan = $bimbingan;
                } catch (\Exception $e) {
                    Log::error('Error loading magangBimbingan: ' . $e->getMessage());
                    $dosen->magangBimbingan = [];
                    $dosen->magang_bimbingan = [];
                }

                return $dosen;
            });

            return response()->json([
                'success' => true,
                'data' => $dosens,
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            Log::error('Error in withDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeAssignments($id)
    {
        try {
            // Log the request for debugging
            Log::info('Removing assignments for dosen ID: ' . $id);

            // Find all magang records where id_dosen matches $id and reset them
            $count = Magang::where('id_dosen', $id)->update(['id_dosen' => null]);

            Log::info('Removed ' . $count . ' assignments for dosen ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Assignments removed successfully',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing assignments: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignMahasiswa(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'magang_ids' => 'required|array',
                'magang_ids.*' => 'integer|exists:m_magang,id_magang'
            ]);

            // Log the assignment request
            Log::info('Assigning magang to dosen', [
                'dosen_id' => $id,
                'magang_ids' => $request->magang_ids
            ]);

            // Update the magang records with the dosen ID
            $count = Magang::whereIn('id_magang', $request->magang_ids)
                ->update(['id_dosen' => $id]);

            Log::info("Successfully assigned $count magang to dosen ID: $id");

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned $count magang",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning mahasiswa: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
