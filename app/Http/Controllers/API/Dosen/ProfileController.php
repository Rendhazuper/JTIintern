<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller 
{
    public function getProfileData()
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // Get skills data
            $skills = DB::table('t_skill_dosen')
                ->join('m_skill', 'm_skill.skill_id', '=', 't_skill_dosen.skill_id')
                ->where('t_skill_dosen.id_dosen', $dosen->id_dosen)
                ->select('m_skill.skill_id', 'm_skill.nama')
                ->get();

            // Get minat data
            $minat = DB::table('t_minat_dosen')
                ->join('m_minat', 'm_minat.minat_id', '=', 't_minat_dosen.minat_id')
                ->where('t_minat_dosen.dosen_id', $dosen->id_dosen)
                ->select('m_minat.minat_id', 'm_minat.nama_minat')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'userData' => $user,
                    'dosenData' => $dosen,
                    'skills' => $skills,
                    'minat' => $minat,
                    'jumlahBimbingan' => DB::table('m_magang')->where('id_dosen', $dosen->id_dosen)->count(),
                    'profileCompletion' => [
                        'is_complete' => !empty($dosen->nip) && !empty($dosen->no_hp) 
                            && !empty($dosen->alamat) && $skills->count() > 0,
                        'missing_fields' => $this->getMissingFields($dosen, $skills)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getProfileData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load profile data'
            ], 500);
        }
    }

    /**
     * Get minat data for dosen profile
     */
    public function getMinat()
    {
        try {
            // Get authenticated user
            $user = Auth::user();
            
            // Get dosen data
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // Get all available minat
            $allMinat = DB::table('m_minat')
                ->select('minat_id', 'nama_minat')
                ->orderBy('nama_minat')
                ->get();

            // Get dosen's current minat
            $dosenMinat = DB::table('t_minat_dosen')
                ->join('m_minat', 't_minat_dosen.minat_id', '=', 'm_minat.minat_id')
                ->where('t_minat_dosen.dosen_id', $dosen->id_dosen)
                ->select('m_minat.minat_id', 'm_minat.nama_minat')
                ->get();

            return response()->json([
                'success' => true,
                'all_minat' => $allMinat,
                'dosen_minat' => $dosenMinat
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting minat: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load minat data'
            ], 500);
        }
    }

    /**
     * Update dosen minat
     */
    public function updateMinat(Request $request)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();
            $minatIds = $request->input('minat_ids', []);

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Delete existing minat
            DB::table('t_minat_dosen')
                ->where('dosen_id', $dosen->id_dosen)
                ->delete();

            // Insert new minat
            if (!empty($minatIds)) {
                $minatData = array_map(function($minatId) use ($dosen) {
                    return [
                        'dosen_id' => $dosen->id_dosen,
                        'minat_id' => $minatId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $minatIds);
                
                DB::table('t_minat_dosen')->insert($minatData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Minat berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating minat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update minat'
            ], 500);
        }
    }

    // Add new method for getting skills
    public function getSkills()
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // Get all available skills
            $allSkills = DB::table('m_skill')
                ->select('skill_id', 'nama')
                ->orderBy('nama')
                ->get();

            // Get dosen's current skills
            $dosenSkills = DB::table('t_skill_dosen')
                ->join('m_skill', 't_skill_dosen.skill_id', '=', 'm_skill.skill_id')
                ->where('t_skill_dosen.id_dosen', $dosen->id_dosen)
                ->select('m_skill.skill_id', 'm_skill.nama')
                ->get();

            return response()->json([
                'success' => true,
                'all_skills' => $allSkills,
                'dosen_skills' => $dosenSkills
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load skills data'
            ], 500);
        }
    }

    // Add new method for updating skills
    public function updateSkills(Request $request)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();
            $skillIds = $request->input('skill_ids', []);

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Delete existing skills
            DB::table('t_skill_dosen')
                ->where('id_dosen', $dosen->id_dosen)
                ->delete();

            // Insert new skills
            if (!empty($skillIds)) {
                $skillData = array_map(function($skillId) use ($dosen) {
                    return [
                        'id_dosen' => $dosen->id_dosen,
                        'skill_id' => $skillId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $skillIds);
                
                DB::table('t_skill_dosen')->insert($skillData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Skills berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update skills'
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                throw new \Exception('Data dosen tidak ditemukan');
            }

            // Update user name if changed
            if ($request->filled('name') && $request->name !== $user->name) {
                $userModel = User::where('id_user', $user->id_user)->first();
                if ($userModel) {
                    $userModel->name = $request->name;
                    $userModel->save();
                }
            }

            // Update dosen data
            $dosen->no_hp = $request->no_hp;
            $dosen->alamat = $request->alamat;
            $dosen->save();

            // Update skills
            if ($request->has('skill_ids')) {
                $skillIds = json_decode($request->skill_ids);
                DB::table('t_skill_dosen')->where('id_dosen', $dosen->id_dosen)->delete();
                if (!empty($skillIds)) {
                    $skillData = array_map(function($skillId) use ($dosen) {
                        return [
                            'id_dosen' => $dosen->id_dosen,
                            'skill_id' => $skillId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }, $skillIds);
                    DB::table('t_skill_dosen')->insert($skillData);
                }
            }

            // Update minat
            if ($request->has('minat_ids')) {
                $minatIds = json_decode($request->minat_ids);
                DB::table('t_minat_dosen')->where('dosen_id', $dosen->id_dosen)->delete();
                if (!empty($minatIds)) {
                    $minatData = array_map(function($minatId) use ($dosen) {
                        return [
                            'dosen_id' => $dosen->id_dosen,
                            'minat_id' => $minatId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }, $minatIds);
                    DB::table('t_minat_dosen')->insert($minatData);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating profile: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getMissingFields($dosen, $skills)
    {
        $missingFields = [];
        
        if (empty($dosen->nip)) $missingFields[] = 'NIP';
        if (empty($dosen->pendidikan_terakhir)) $missingFields[] = 'Pendidikan Terakhir';
        if (empty($dosen->no_hp)) $missingFields[] = 'Nomor Telepon';
        if (empty($dosen->alamat)) $missingFields[] = 'Alamat';
        if (empty($dosen->bidang_keahlian)) $missingFields[] = 'Bidang Keahlian';
        if ($skills->count() == 0) $missingFields[] = 'Skills';
        
        return $missingFields;
    }
}
