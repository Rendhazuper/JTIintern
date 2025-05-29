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
            // Step 1: Get dosen's user_id for skill matching
            $dosenUserId = DB::table('m_dosen')
                ->where('id_dosen', $dosenId)
                ->value('user_id');

            if (!$dosenUserId) {
                Log::warning("Could not find user_id for dosen ID: {$dosenId}");
                return 0;
            }

            // Step 2: Get student skills
            $mahasiswaSkills = DB::table('t_skill_mahasiswa as sm')
                ->join('m_skill as s', 'sm.skill_id', '=', 's.skill_id')
                ->where('sm.user_id', $mahasiswaId)
                ->pluck('s.skill_id')
                ->toArray();

            // Step 3: Get lecturer skills - use user_id from dosen
            $dosenSkills = DB::table('t_skill_dosen as sd')
                ->join('m_skill as s', 'sd.skill_id', '=', 's.skill_id')
                ->where('sd.user_id', $dosenUserId)  // Use user_id not id_dosen
                ->pluck('s.skill_id')
                ->toArray();

            // Log skill information for debugging
            Log::info("Mahasiswa ID {$mahasiswaId} has " . count($mahasiswaSkills) . " skills");
            Log::info("Dosen ID {$dosenId} (User ID: {$dosenUserId}) has " . count($dosenSkills) . " skills");

            if (empty($mahasiswaSkills) || empty($dosenSkills)) {
                return 0;
            }

            // Step 4: Calculate skill match percentage
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
            // Ambil bobot kriteria dari database
            $weights = SAWKriteria::getAllWeights();
            if (empty($weights)) {
                // Default weights jika tidak ada di database
                $weights = [
                    'wilayah' => 0.6,
                    'skill' => 0.4
                ];
            }

            // Dapatkan semua dosen aktif dengan skills dan wilayah
            $dosen = Dosen::with(['user', 'skills.skill', 'wilayah'])
                ->get();

            // Dapatkan semua magang aktif dengan perusahaan dan mahasiswa
            $aktivMagang = Magang::with([
                'mahasiswa.user',
                'mahasiswa.skills.skill',
                'lowongan.perusahaan.wilayah'
            ])
                ->where('status', 'aktif')
                ->get();

            // Buat matrix keputusan untuk visualisasi
            $detailedMatrix = [];

            foreach ($aktivMagang as $magang) {
                $mahasiswaData = [
                    'id_magang' => $magang->id_magang,
                    'mahasiswa_name' => $magang->mahasiswa->user->name ?? 'Tidak diketahui',
                    'perusahaan_name' => $magang->lowongan->perusahaan->nama_perusahaan ?? 'Tidak diketahui',
                    'wilayah_id' => $magang->lowongan->perusahaan->wilayah_id ?? null,
                    'wilayah_name' => $magang->lowongan->perusahaan->wilayah->nama_wilayah ?? 'Tidak diketahui',
                    'current_dosen_id' => $magang->id_dosen,
                    'mahasiswa_skills' => $magang->mahasiswa->skills->map(function ($skill) {
                        return [
                            'id' => $skill->skill->id_skill ?? null,
                            'name' => $skill->skill->nama_skill ?? 'Tidak diketahui'
                        ];
                    })->toArray(),
                    'dosen_scores' => []
                ];

                // Hitung skor untuk setiap dosen
                foreach ($dosen as $d) {
                    $wilayahScore = ($d->wilayah_id == $mahasiswaData['wilayah_id']) ? 1 : 0;

                    $dosenSkills = $d->skills->pluck('id_skill')->toArray();
                    $mahasiswaSkills = $magang->mahasiswa->skills->pluck('id_skill')->toArray();
                    $matchedSkills = array_intersect($dosenSkills, $mahasiswaSkills);

                    $skillScore = empty($mahasiswaSkills) ?
                        0.5 : count($matchedSkills) / max(1, count($mahasiswaSkills));

                    $totalScore = ($wilayahScore * $weights['wilayah']) +
                        ($skillScore * $weights['skill']);

                    $mahasiswaData['dosen_scores'][] = [
                        'id_dosen' => $d->id_dosen,
                        'dosen_name' => $d->user->name ?? 'Tidak diketahui',
                        'nip' => $d->nip ?? '-',
                        'wilayah_score' => $wilayahScore,
                        'skill_score' => $skillScore,
                        'total_score' => $totalScore,
                        'is_current' => $d->id_dosen == $magang->id_dosen,
                        'matched_skills' => array_values(array_filter($d->skills->map(function ($skill) use ($mahasiswaSkills) {
                            if (in_array($skill->id_skill, $mahasiswaSkills)) {
                                return $skill->skill->nama_skill ?? 'Tidak diketahui';
                            }
                            return null;
                        })->toArray()))
                    ];
                }

                // Urutkan dosen berdasarkan score
                usort($mahasiswaData['dosen_scores'], function ($a, $b) {
                    return $b['total_score'] <=> $a['total_score'];
                });

                $detailedMatrix[] = $mahasiswaData;
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
            // Define weights for SAW
            $weights = [
                'wilayah' => 0.30,
                'skill' => 0.70,
            ];

            // Get type from request (active or pending)
            $type = $request->input('type', 'active');

            // Query for magang entries based on type
            $query = Magang::with([
                'mahasiswa.user',
                'lowongan.perusahaan.wilayah'
            ]);

            if ($type === 'active') {
                // Only get magang entries without dosen (unassigned active internships)
                $query->whereNull('id_dosen')
                    ->where('status', 'aktif');
            } else {
                // Get pending magang entries (not yet active, but registered)
                $query->where('status', 'diterima')
                    ->orWhere('status', 'pending');
            }

            $magangs = $query->get();

            if ($magangs->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'weights' => $weights,
                    'message' => 'Tidak ada magang yang perlu ditugaskan'
                ]);
            }

            // Rest of your existing code for generating matrix data...
            $allDosen = Dosen::with(['user', 'wilayah'])->get();
            $matrixData = [];

            foreach ($magangs as $magang) {
                // Your existing code for building matrix data...
            }

            return response()->json([
                'success' => true,
                'data' => $matrixData,
                'weights' => $weights,
                'type' => $type
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMatrix: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat matrix keputusan: ' . $e->getMessage()
            ], 500);
        }
    }
}
