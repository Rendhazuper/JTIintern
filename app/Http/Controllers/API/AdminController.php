<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        try {
            $admins = DB::table('m_user')
                       ->where('role', 'admin')
                       ->select('id_user', 'name', 'email', 'username')
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
}
