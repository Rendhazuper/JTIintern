<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;

class WilayahController extends Controller
{
    public function index()
    {
        $wilayah = Wilayah::all();

        return response()->json([
            'success' => true,
            'data' => $wilayah,
        ]);
    }
}