<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Periode;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        try {
            // Get active period from t_periode table first
            $activePeriodeRecord = DB::table('t_periode')->first();
            
            // If active period exists, get the period details
            if ($activePeriodeRecord) {
                $activePeriod = Periode::find($activePeriodeRecord->periode_id);
            }

            return view('pages.mahasiswa.dashboard', [
                'title' => 'Dashboard',
                'activePeriod' => $activePeriod ?? null 
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading dashboard: ' . $e->getMessage());
            return view('pages.mahasiswa.dashboard', [
                'title' => 'Dashboard',
                'activePeriod' => null
            ]);
        }
    }
}
