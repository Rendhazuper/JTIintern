<?php
// filepath: app/Http/Controllers/API/PlottingController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\DosenBebanKerja;
use App\Models\Perusahaan;
use App\Models\Mahasiswa;
use App\Models\Magang;
use App\Models\SAWKriteria;
use App\Models\PlottingRiwayat;
use App\Models\SkillDosen;
use App\Models\SkillMahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlottingController extends Controller
{
    public function index()
    {
        return view('pages.plotting');
    }

    public function autoPlot(Request $request)
    {
        try {
            // Define weights for SAW criteria
            $weights = [
                'wilayah' => 0.30, // 30% weight for geographical match
                'skill' => 0.70,   // 70% weight for skill match
            ];

            Log::info('Starting auto plot with weights', $weights);

            // 1. Get all unassigned internships
            $unassignedMagangs = Magang::with(['mahasiswa.user', 'lowongan.perusahaan'])
                ->whereNull('id_dosen')
                ->get();

            // 2. Get all available lecturers
            $allDosen = Dosen::with(['user', 'wilayah'])->get();

            if ($unassignedMagangs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada magang yang belum ditugaskan'
                ]);
            }

            if ($allDosen->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada dosen yang tersedia'
                ]);
            }

            $assignmentCount = 0;
            $results = [];

            // 3. For each unassigned internship, find the best lecturer match
            foreach ($unassignedMagangs as $magang) {
                $bestDosen = null;
                $bestScore = -1;

                foreach ($allDosen as $dosen) {
                    // Calculate score based on wilayah (30%)
                    $wilayahScore = $magang->lowongan->perusahaan->wilayah_id == $dosen->wilayah_id ? 1 : 0;

                    // Calculate score based on skill match (70%)
                    $skillScore = $this->calculateSkillMatchScore(
                        $magang->mahasiswa->user_id,
                        $dosen->id_dosen
                    );

                    // Apply weights to get total score
                    $totalScore = ($wilayahScore * $weights['wilayah']) +
                        ($skillScore * $weights['skill']);

                    Log::info("Match score for magang {$magang->id_magang} with dosen {$dosen->id_dosen}: " .
                        "wilayah={$wilayahScore}, skill={$skillScore}, total={$totalScore}");

                    if ($totalScore > $bestScore) {
                        $bestScore = $totalScore;
                        $bestDosen = $dosen;
                    }
                }

                if ($bestDosen) {
                    // Assign the best matching lecturer
                    $magang->id_dosen = $bestDosen->id_dosen;
                    $magang->save();
                    $assignmentCount++;

                    Log::info("Assigned magang {$magang->id_magang} to dosen {$bestDosen->id_dosen} with score {$bestScore}");

                    // Store results for feedback
                    $results[] = [
                        'magang_id' => $magang->id_magang,
                        'mahasiswa_name' => $magang->mahasiswa->user->name,
                        'dosen_name' => $bestDosen->user->name,
                        'score' => $bestScore
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil menetapkan {$assignmentCount} magang",
                'stats' => [
                    'total_dosen' => $allDosen->count(),
                    'total_magang' => $unassignedMagangs->count(),
                    'total_assignments' => $assignmentCount
                ],
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Auto plot error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error in auto-plot: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateSkillMatchScore($mahasiswaId, $dosenId)
    {
        try {
            // Get student user_id
            $mahasiswaUserId = DB::table('m_mahasiswa')
                ->where('id_mahasiswa', $mahasiswaId)
                ->value('id_user');

            if (!$mahasiswaUserId) {
                Log::warning("Could not find user_id for mahasiswa ID: {$mahasiswaId}");
                return 0;
            }

            // Get student skills from t_skill_mahasiswa
            $mahasiswaSkills = DB::table('t_skill_mahasiswa as sm')
                ->join('m_skill as s', 's.skill_id', '=', 'sm.skill_id')
                ->where('sm.user_id', $mahasiswaUserId)
                ->pluck('s.skill_id')
                ->toArray();

            // Get lecturer skills from t_skill_dosen
            $dosenSkills = DB::table('t_skill_dosen as sd')
                ->join('m_skill as s', 's.skill_id', '=', 'sd.skill_id')
                ->where('sd.id_dosen', $dosenId) // Using id_dosen column
                ->pluck('s.skill_id')
                ->toArray();

            Log::info("Mahasiswa ID {$mahasiswaId} (User ID: {$mahasiswaUserId}) has " . count($mahasiswaSkills) . " skills");
            Log::info("Dosen ID {$dosenId} has " . count($dosenSkills) . " skills");

            if (empty($mahasiswaSkills) || empty($dosenSkills)) {
                return 0;
            }

            // Calculate skill match percentage
            $matchingSkills = array_intersect($mahasiswaSkills, $dosenSkills);
            $matchCount = count($matchingSkills);

            Log::info("Found {$matchCount} matching skills between mahasiswa and dosen");

            // Return normalized score (0-1)
            return min(1, $matchCount / max(1, count($mahasiswaSkills)));
        } catch (\Exception $e) {
            Log::error("Error in calculateSkillMatchScore: " . $e->getMessage());
            return 0;
        }
    }

    private function clearAllAssignments()
    {
        // Reset id_dosen di tabel magang yang statusnya aktif
        Magang::where('status', 'aktif')->update(['id_dosen' => null]);
    }

    private function performSawPlottingAlgorithm($dosen, $aktivMagang, $weights)
    {
        $assignments = [];

        // Untuk setiap magang aktif, cari dosen terbaik
        foreach ($aktivMagang as $magang) {
            // Ambil data yang diperlukan
            $mahasiswaSkills = $magang->mahasiswa->skills->pluck('id_skill')->toArray();
            $perusahaanWilayahId = $magang->lowongan->perusahaan->wilayah_id ?? null;
            $perusahaanId = $magang->lowongan->perusahaan->perusahaan_id;

            // Matrix keputusan [dosen_id => [wilayah_score, skill_score]]
            $decisionMatrix = [];

            // Untuk setiap dosen, hitung skor kecocokan wilayah dan skill
            foreach ($dosen as $d) {
                // Skip dosen yang sudah mencapai kapasitas maksimal
                if (isset($d->workload) && $d->workload->current_mahasiswa >= $d->workload->max_mahasiswa) {
                    continue;
                }

                // 1. Nilai kecocokan wilayah (1 jika sama, 0 jika beda)
                $wilayahScore = ($d->wilayah_id == $perusahaanWilayahId) ? 1 : 0;

                // 2. Nilai kecocokan skill (persentase skill yang cocok)
                $dosenSkills = $d->skills->pluck('id_skill')->toArray();

                // Hitung jumlah skill yang cocok
                $matchedSkills = array_intersect($dosenSkills, $mahasiswaSkills);

                // Jika mahasiswa tidak punya skill, set score = 0.5 (netral)
                // Jika punya, hitung persentase kecocokan
                $skillScore = empty($mahasiswaSkills) ?
                    0.5 :
                    count($matchedSkills) / max(1, count($mahasiswaSkills));

                // Simpan ke matrix keputusan
                $decisionMatrix[$d->id_dosen] = [
                    'wilayah' => $wilayahScore,
                    'skill' => $skillScore
                ];
            }

            // Jika tidak ada dosen yang tersedia untuk magang ini, skip
            if (empty($decisionMatrix)) {
                continue;
            }

            // Normalisasi matrix (untuk SAW)
            $normalizedMatrix = $this->normalizeMatrix($decisionMatrix);

            // Hitung nilai preferensi untuk setiap dosen
            $preferences = [];
            foreach ($normalizedMatrix as $dosenId => $scores) {
                $preferences[$dosenId] =
                    ($scores['wilayah'] * $weights['wilayah']) +
                    ($scores['skill'] * $weights['skill']);
            }

            // Ambil dosen dengan nilai preferensi tertinggi
            if (!empty($preferences)) {
                $bestDosenId = array_search(max($preferences), $preferences);

                // Simpan assignment
                $assignments[] = [
                    'id_magang' => $magang->id_magang,
                    'id_dosen' => $bestDosenId,
                    'perusahaan_id' => $perusahaanId,
                    'score' => $preferences[$bestDosenId],
                    'wilayah_score' => $decisionMatrix[$bestDosenId]['wilayah'],
                    'skill_score' => $decisionMatrix[$bestDosenId]['skill']
                ];

                // Tambahkan beban dosen yang terpilih
                foreach ($dosen as &$d) {
                    if ($d->id_dosen == $bestDosenId && isset($d->workload)) {
                        $d->workload->current_mahasiswa += 1;
                    }
                }
            }
        }

        return $assignments;
    }

    private function normalizeMatrix($matrix)
    {
        $normalized = [];

        // Tentukan nilai max untuk setiap kriteria
        $maxWilayah = 1; // Nilai max untuk wilayah selalu 1
        $maxSkill = 0;

        foreach ($matrix as $scores) {
            if ($scores['skill'] > $maxSkill) {
                $maxSkill = $scores['skill'];
            }
        }

        // Untuk menghindari pembagian dengan nol
        $maxSkill = ($maxSkill == 0) ? 1 : $maxSkill;

        // Normalisasi
        foreach ($matrix as $dosenId => $scores) {
            $normalized[$dosenId] = [
                'wilayah' => $scores['wilayah'] / $maxWilayah,
                'skill' => $scores['skill'] / $maxSkill
            ];
        }

        return $normalized;
    }

    private function saveAssignments($assignments)
    {
        // Untuk setiap assignment, update id_dosen di tabel magang dan simpan ke history
        foreach ($assignments as $assignment) {
            // Update id_dosen di tabel magang
            Magang::where('id_magang', $assignment['id_magang'])
                ->update(['id_dosen' => $assignment['id_dosen']]);

            // Simpan ke tabel pivot dosen_perusahaan
            DB::table('t_dosen_perusahaan')->updateOrInsert(
                [
                    'id_dosen' => $assignment['id_dosen'],
                    'perusahaan_id' => $assignment['perusahaan_id']
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Simpan ke history
            PlottingRiwayat::create([
                'id_magang' => $assignment['id_magang'],
                'id_dosen' => $assignment['id_dosen'],
                'score' => $assignment['score'],
                'wilayah_score' => $assignment['wilayah_score'],
                'skill_score' => $assignment['skill_score'],
                'assigned_at' => now()
            ]);
        }
    }

    private function updateDosenWorkloads()
    {
        // Update semua workload dosen berdasarkan jumlah mahasiswa aktual
        $dosens = Dosen::with('workload')->get();

        foreach ($dosens as $dosen) {
            if (!$dosen->workload) {
                DosenBebanKerja::create([
                    'id_dosen' => $dosen->id_dosen,
                    'max_mahasiswa' => 10,
                    'current_mahasiswa' => 0
                ]);
            }

            // Hitung jumlah magang aktif yang dibimbing
            $magangCount = Magang::where('id_dosen', $dosen->id_dosen)
                ->where('status', 'aktif')
                ->count();

            // Update workload
            DosenBebanKerja::where('id_dosen', $dosen->id_dosen)
                ->update(['current_mahasiswa' => $magangCount]);
        }
    }

    public function getPlottingMatrixDetails()
    {
        try {
            // Ambil bobot kriteria
            $weights = $this->getWeights();

            // Dapatkan semua dosen aktif dengan relasi yang dibutuhkan
            $dosen = Dosen::with([
                'user',
                'skills.skill',
                'wilayah',
                'magang_bimbingan'
            ])->get();

            // Dapatkan semua magang aktif dengan semua relasi yang dibutuhkan
            $aktivMagang = Magang::with([
                'mahasiswa.user',
                'mahasiswa.skills.skill',
                'lowongan.perusahaan.wilayah'
            ])
                ->where('status', 'aktif')
                ->get();

            if ($aktivMagang->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'weights' => $weights,
                    'message' => 'Tidak ada magang aktif untuk ditampilkan'
                ]);
            }

            // Gunakan calculateDecisionMatrix yang sudah mencakup kriteria minat, skill, dan beban_kerja
            $detailedMatrix = $this->calculateDecisionMatrix($aktivMagang, $dosen);

            // Tambahkan informasi status magang yang sedang berlangsung
            foreach ($detailedMatrix as &$item) {
                $currentMagang = $aktivMagang->firstWhere('id_magang', $item['id_magang']);
                $item['current_dosen_id'] = $currentMagang->id_dosen;

                // Tandai dosen yang saat ini ditugaskan
                foreach ($item['dosen_scores'] as &$dosenScore) {
                    $dosenScore['is_current'] = ($dosenScore['dosen_id'] == $currentMagang->id_dosen);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $detailedMatrix,
                'weights' => $weights
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating plotting matrix: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat matrix plotting: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getMatrix(Request $request)
    {
        try {
            // Dapatkan bobot dari metode getWeights()
            $weights = $this->getWeights();

            // Hanya ambil magang yang tidak aktif dan belum ada dosen
            $query = Magang::with([
                'mahasiswa.user',
                'mahasiswa.skills.skill',
                'lowongan.perusahaan.wilayah'
            ]);

            // Filter untuk hanya menampilkan magang tidak aktif tanpa pembimbing
            $query->whereNull('id_dosen')
                ->where('status', 'tidak aktif');

            $magangs = $query->get();

            if ($magangs->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'weights' => $weights,
                    'message' => 'Tidak ada magang tidak aktif tanpa pembimbing'
                ]);
            }

            // Dapatkan semua dosen untuk pencocokan
            $allDosen = Dosen::with(['user', 'skills.skill', 'wilayah', 'magang_bimbingan'])->get();

            // Gunakan calculateDecisionMatrix yang sudah menyesuaikan dengan kriteria baru
            $matrixData = $this->calculateDecisionMatrix($magangs, $allDosen);

            return response()->json([
                'success' => true,
                'data' => $matrixData,
                'weights' => $weights
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMatrix: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat matrix keputusan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get weights for SAW criteria
     */
    protected function getWeights()
    {
        return [
            'minat' => 0.40,
            'skill' => 0.40,
            'beban_kerja' => 0.20
        ];
    }

    /**
     * Calculate decision matrix using SAW method
     */
    private function calculateMinatMatchScore($mahasiswaId, $dosenId)
    {
        try {
            // Get student interests from t_minat_mahasiswa
            $mahasiswaMinat = DB::table('t_minat_mahasiswa as mm')
                ->join('m_minat as m', 'm.minat_id', '=', 'mm.minat_id')
                ->where('mm.mahasiswa_id', $mahasiswaId)
                ->pluck('m.minat_id')
                ->toArray();

            // Get lecturer interests from t_minat_dosen
            $dosenMinat = DB::table('t_minat_dosen as md')
                ->join('m_minat as m', 'm.minat_id', '=', 'md.minat_id')
                ->where('md.dosen_id', $dosenId)
                ->pluck('m.minat_id')
                ->toArray();

            Log::info("Mahasiswa ID {$mahasiswaId} has " . count($mahasiswaMinat) . " interests");
            Log::info("Dosen ID {$dosenId} has " . count($dosenMinat) . " interests");

            if (empty($mahasiswaMinat) || empty($dosenMinat)) {
                return 0;
            }

            // Calculate interest match percentage
            $matchingMinat = array_intersect($mahasiswaMinat, $dosenMinat);
            $matchCount = count($matchingMinat);

            Log::info("Found {$matchCount} matching interests between mahasiswa and dosen");

            // Return normalized score (0-1)
            return min(1, $matchCount / max(1, count($mahasiswaMinat)));
        } catch (\Exception $e) {
            Log::error("Error in calculateMinatMatchScore: " . $e->getMessage());
            return 0;
        }
    }

    protected function calculateDecisionMatrix($magangs, $allDosen)
    {
        $weights = $this->getWeights();
        $matrixData = [];

        foreach ($magangs as $magang) {
            $mahasiswa = $magang->mahasiswa;
            $perusahaan = $magang->lowongan->perusahaan;

            $mahasiswaData = [
                'id_magang' => $magang->id_magang,
                'mahasiswa_name' => $mahasiswa->user->name ?? 'Tidak diketahui',
                'mahasiswa_id' => $mahasiswa->id_mahasiswa,
                'perusahaan_name' => $perusahaan->nama_perusahaan ?? 'Tidak diketahui',
                'wilayah_name' => $perusahaan->wilayah->nama_kota ?? 'Tidak diketahui',
                'wilayah_id' => $perusahaan->wilayah_id,
                'dosen_scores' => []
            ];

            // Calculate dosen scores
            foreach ($allDosen as $dosen) {
                // 1. Calculate minat (interest) match score
                $minatScore = $this->calculateMinatMatchScore($mahasiswa->id_mahasiswa, $dosen->id_dosen);

                // 2. Calculate skill match score
                $skillScore = $this->calculateSkillMatchScore($mahasiswa->id_mahasiswa, $dosen->id_dosen);

                // 3. Calculate beban kerja score (inverse - fewer students = better)
                $currentBeban = $dosen->magang_bimbingan->count();
                $maxBeban = 10; // Maximum expected workload
                $bebanKerjaScore = 1 - min(1, $currentBeban / $maxBeban);

                // Get matched minat names for display
                $matchedMinat = [];
                if ($minatScore > 0) {
                    // Database query inside loop
                    $mahasiswaMinatIds = DB::table('t_minat_mahasiswa')
                        ->where('mahasiswa_id', $mahasiswa->id_mahasiswa)
                        ->pluck('minat_id')
                        ->toArray();

                    $dosenMinatIds = DB::table('t_minat_dosen')
                        ->where('dosen_id', $dosen->id_dosen)
                        ->pluck('minat_id')
                        ->toArray();

                    $matchingMinatIds = array_intersect($mahasiswaMinatIds, $dosenMinatIds);

                    if (!empty($matchingMinatIds)) {
                        $matchedMinat = DB::table('m_minat')
                            ->whereIn('minat_id', $matchingMinatIds)
                            ->pluck('nama_minat')
                            ->toArray();
                    }
                }

                // Get matched skill names for display
                $matchedSkills = [];
                if ($skillScore > 0) {
                    // Database query inside loop
                    $mahasiswaUserId = DB::table('m_mahasiswa')
                        ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                        ->value('id_user');

                    $mahasiswaSkillIds = DB::table('t_skill_mahasiswa')
                        ->where('user_id', $mahasiswaUserId)
                        ->pluck('skill_id')
                        ->toArray();

                    $dosenSkillIds = DB::table('t_skill_dosen')
                        ->where('id_dosen', $dosen->id_dosen)
                        ->pluck('skill_id')
                        ->toArray();

                    $matchingSkillIds = array_intersect($mahasiswaSkillIds, $dosenSkillIds);

                    if (!empty($matchingSkillIds)) {
                        $matchedSkills = DB::table('m_skill')
                            ->whereIn('skill_id', $matchingSkillIds)
                            ->pluck('nama')
                            ->toArray();
                    }
                }

                // Calculate total weighted score
                $totalScore =
                    $weights['minat'] * $minatScore +
                    $weights['skill'] * $skillScore +
                    $weights['beban_kerja'] * $bebanKerjaScore;

                // Add to dosen_scores array
                $mahasiswaData['dosen_scores'][] = [
                    'dosen_id' => $dosen->id_dosen,
                    'dosen_name' => $dosen->user->name ?? 'Tidak diketahui',
                    'nip' => $dosen->nip ?? '-',
                    'minat_score' => $minatScore,
                    'skill_score' => $skillScore,
                    'beban_kerja_score' => $bebanKerjaScore,
                    'total_score' => $totalScore,
                    'current_beban' => $currentBeban,
                    'matched_minat' => $matchedMinat,
                    'matched_skills' => $matchedSkills
                ];
            }

            // Sort by total score (highest first)
            usort($mahasiswaData['dosen_scores'], function ($a, $b) {
                return $b['total_score'] <=> $a['total_score'];
            });

            $matrixData[] = $mahasiswaData;
        }

        return $matrixData;
    }
}
