<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DosenMahasiswaController extends Controller
{
    public function index()
    {
        return view('pages.dosen.DosenMahasiswa', [
            'title' => 'Data Mahasiswa',
        ]);
    }
}
