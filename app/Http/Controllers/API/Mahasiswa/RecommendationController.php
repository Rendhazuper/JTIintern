<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    /**
     * Get internship recommendations using EDAS method
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecommendations()
    {
        try {
            // Get current user
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Get student data
            $mahasiswa = DB::table('m_mahasiswa')
                ->join('m_wilayah', 'm_mahasiswa.wilayah_id', '=', 'm_wilayah.wilayah_id')
                ->where('m_mahasiswa.id_user', $user->id_user)
                ->select('m_mahasiswa.*', 'm_wilayah.nama_kota')
                ->first();
                
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Student data not found'
                ], 404);
            }
            
            // Get student skills
            $studentSkills = DB::table('t_skill_mahasiswa')
                ->join('m_skill', 't_skill_mahasiswa.skill_id', '=', 'm_skill.skill_id')
                ->where('t_skill_mahasiswa.user_id', $mahasiswa->id_user)
                ->pluck('m_skill.skill_id')
                ->toArray();
                
            // Get current active period
            $activePeriod = DB::table('t_periode')
                ->join('m_periode', 't_periode.periode_id', '=', 'm_periode.periode_id')
                ->whereDate('m_periode.tgl_mulai', '<=', now())
                ->whereDate('m_periode.tgl_selesai', '>=', now())
                ->first();
                
            if (!$activePeriod) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No active period found'
                ]);
            }
                
            // Get available internship opportunities with capacity check
            $opportunities = DB::table('m_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->join('m_wilayah', 'm_perusahaan.wilayah_id', '=', 'm_wilayah.wilayah_id')
                ->join('t_kapasitas_lowongan', 'm_lowongan.id_lowongan', '=', 't_kapasitas_lowongan.id_lowongan')
                ->where('m_lowongan.periode_id', $activePeriod->periode_id)
                ->where('t_kapasitas_lowongan.kapasitas_tersedia', '>', 0)
                ->whereRaw('m_lowongan.min_ipk <= ?', [$mahasiswa->ipk ?? 0])
                ->select(
                    'm_lowongan.id_lowongan',
                    'm_lowongan.judul_lowongan',
                    'm_lowongan.min_ipk',
                    'm_lowongan.deskripsi',
                    'm_perusahaan.perusahaan_id',
                    'm_perusahaan.nama_perusahaan',
                    'm_perusahaan.logo as logo_perusahaan',
                    'm_wilayah.nama_kota as lokasi',
                    't_kapasitas_lowongan.kapasitas_tersedia'
                )
                ->get();
                
            if ($opportunities->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No available opportunities found'
                ]);
            }
                
            // Get required skills for each opportunity and calculate matches
            foreach ($opportunities as $opportunity) {
                $requiredSkills = DB::table('t_skill_lowongan')
                    ->where('id_lowongan', $opportunity->id_lowongan)
                    ->pluck('id_skill')
                    ->toArray();
                    
                $opportunity->required_skills = $requiredSkills;
                
                // Calculate skill match percentage
                if (count($requiredSkills) > 0 && count($studentSkills) > 0) {
                    $matchedSkills = array_intersect($studentSkills, $requiredSkills);
                    $opportunity->skill_match = (count($matchedSkills) / count($requiredSkills)) * 100;
                } else {
                    $opportunity->skill_match = 0;
                }
                    
                // Calculate location match
                $opportunity->location_match = ($opportunity->lokasi == $mahasiswa->nama_kota) ? 100 : 30;
                
                // IPK match calculation
                if ($opportunity->min_ipk > 0 && $mahasiswa->ipk) {
                    $opportunity->ipk_match = min(100, ($mahasiswa->ipk / 4.0) * 100); // Assuming max IPK is 4.0
                } else {
                    $opportunity->ipk_match = 50; // Default if no IPK data
                }
            }
            
            // Apply EDAS method
            $recommendations = $this->applyEDASMethod(
                $opportunities->toArray(), 
                ['location_match', 'skill_match', 'ipk_match'],
                [0.3, 0.5, 0.2] // Weights: Location 30%, Skills 50%, GPA 20%
            );
            
            // Return top 6 recommendations
            return response()->json([
                'success' => true,
                'data' => array_slice($recommendations, 0, 6)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating recommendations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recommendations'
            ], 500);
        }
    }
    
    /**
     * Apply EDAS method to rank alternatives
     */
    private function applyEDASMethod($alternatives, $criteria, $weights)
    {
        if (empty($alternatives)) {
            return [];
        }
        
        // Step 1: Create decision matrix
        $decisionMatrix = [];
        foreach ($alternatives as $alt) {
            $row = [];
            foreach ($criteria as $criterion) {
                $row[] = $alt->$criterion ?? 0;
            }
            $decisionMatrix[] = $row;
        }
        
        // Step 2: Calculate average solution for each criterion
        $avgSolution = [];
        $numAlternatives = count($decisionMatrix);
        $numCriteria = count($criteria);
        
        for ($j = 0; $j < $numCriteria; $j++) {
            $sum = 0;
            for ($i = 0; $i < $numAlternatives; $i++) {
                $sum += $decisionMatrix[$i][$j];
            }
            $avgSolution[$j] = $numAlternatives > 0 ? $sum / $numAlternatives : 0;
        }
        
        // Step 3: Calculate PDA (Positive Distance from Average)
        $pda = [];
        for ($i = 0; $i < $numAlternatives; $i++) {
            $pdaRow = [];
            for ($j = 0; $j < $numCriteria; $j++) {
                if ($avgSolution[$j] > 0 && $decisionMatrix[$i][$j] > $avgSolution[$j]) {
                    $pdaRow[$j] = ($decisionMatrix[$i][$j] - $avgSolution[$j]) / $avgSolution[$j];
                } else {
                    $pdaRow[$j] = 0;
                }
            }
            $pda[] = $pdaRow;
        }
        
        // Step 4: Calculate NDA (Negative Distance from Average)
        $nda = [];
        for ($i = 0; $i < $numAlternatives; $i++) {
            $ndaRow = [];
            for ($j = 0; $j < $numCriteria; $j++) {
                if ($avgSolution[$j] > 0 && $decisionMatrix[$i][$j] < $avgSolution[$j]) {
                    $ndaRow[$j] = ($avgSolution[$j] - $decisionMatrix[$i][$j]) / $avgSolution[$j];
                } else {
                    $ndaRow[$j] = 0;
                }
            }
            $nda[] = $ndaRow;
        }
        
        // Step 5: Calculate weighted sum of PDA and NDA
        $sp = [];
        $sn = [];
        for ($i = 0; $i < $numAlternatives; $i++) {
            $spSum = 0;
            $snSum = 0;
            for ($j = 0; $j < $numCriteria; $j++) {
                $spSum += $weights[$j] * $pda[$i][$j];
                $snSum += $weights[$j] * $nda[$i][$j];
            }
            $sp[] = $spSum;
            $sn[] = $snSum;
        }
        
        // Step 6: Calculate normalized weighted sum
        $nsp = [];
        $nsn = [];
        $maxSp = max($sp);
        $maxSn = max($sn);
        
        // Avoid division by zero
        if ($maxSp == 0) $maxSp = 1;
        if ($maxSn == 0) $maxSn = 1;
        
        for ($i = 0; $i < $numAlternatives; $i++) {
            $nsp[] = $sp[$i] / $maxSp;
            $nsn[] = 1 - ($sn[$i] / $maxSn);
        }
        
        // Step 7: Calculate appraisal score
        $as = [];
        for ($i = 0; $i < $numAlternatives; $i++) {
            $as[] = 0.5 * ($nsp[$i] + $nsn[$i]);
        }
        
        // Step 8: Rank alternatives based on appraisal score
        $results = [];
        for ($i = 0; $i < $numAlternatives; $i++) {
            $alternatives[$i]->appraisal_score = $as[$i];
            $results[] = $alternatives[$i];
        }
        
        // Sort by appraisal score in descending order
        usort($results, function($a, $b) {
            return $b->appraisal_score <=> $a->appraisal_score;
        });
        
        return $results;
    }
}