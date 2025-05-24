<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodi = \App\Models\Prodi::all();

        return response()->json([
            'success' => true,
            'data' => $prodi
        ]);
    }
}
