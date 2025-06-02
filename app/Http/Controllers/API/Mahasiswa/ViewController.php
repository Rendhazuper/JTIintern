<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function dashboard()
    {
        return view('pages.mahasiswa.dashboard', [
            'title' => 'Dashboard Mahasiswa',
        ]);
    }
    public function profile()
    {
        return view('pages.mahasiswa.profile', [
            'title' => 'Profil Mahasiswa',
        ]);
    }
    public function magang()
    {
        return view('pages.mahasiswa.magang', [
            'title' => 'Magang Mahasiswa',
        ]);
    }
    public function lowongan()
    {
        return view('pages.mahasiswa.MhsLowongan', [
            'title' => 'Lowongan Mahasiswa',
        ]);
    }
    public function lamaran()
    {
        return view('pages.mahasiswa.MhsLamaran', [
            'title' => 'Perusahaan Mahasiswa',
        ]);
    }
    public function evaluasi()
    {
        return view('pages.mahasiswa.MhsEvaluasi', [
            'title' => 'Evaluasi Mahasiswa',
        ]);
    }
    public function log()
    {
        return view('pages.mahasiswa.MhsLog', [
            'title' => 'Evaluasi Mahasiswa',
        ]);
    }
}
