<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    public function index()
    {
        return view('pages.dosen.dashboard', [
            'title' => 'Dashboard Dosen',
        ]);
    }
}
