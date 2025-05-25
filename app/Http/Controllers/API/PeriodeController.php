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
}
