<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        try {
            $admins = DB::table('m_user')
                ->where('role', 'admin')
                ->select('id_user', 'name', 'email')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $admins
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
            $admin = DB::table('m_user')
                ->where('id_user', $id)
                ->where('role', 'admin')
                ->select('id_user', 'name', 'email')
                ->first();

            if (!$admin) {
                return response()->json(['success' => false, 'message' => 'Admin tidak ditemukan'], 404);
            }

            return response()->json(['success' => true, 'data' => $admin]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:m_user,email',
            'password' => 'required|string|min:6'
        ]);

        try {
            $id = DB::table('m_user')->insertGetId([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'admin'
            ]);
            return response()->json(['success' => true, 'id_user' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:m_user,email,' . $id . ',id_user',
            'password' => 'nullable|string|min:6'
        ]);

        try {
            $data = [
                'name'     => $request->name,
                'email'    => $request->email
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $updated = DB::table('m_user')
                ->where('id_user', $id)
                ->where('role', 'admin')
                ->update($data);

            if ($updated) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Admin tidak ditemukan atau tidak ada perubahan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('m_user')
                ->where('id_user', $id)
                ->where('role', 'admin')
                ->delete();

            if ($deleted) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Admin tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
