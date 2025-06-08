<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get skills data for profile management
     */
    public function getSkills()
    {
        try {
            $user = auth()->user();
            
            // Get all available skills
            $allSkills = DB::table('m_skill')
                ->select('skill_id', 'nama as nama_skill')
                ->orderBy('nama')
                ->get();

            // Get user's current skills
            $userSkills = DB::table('t_skill_mahasiswa')
                ->join('m_skill', 't_skill_mahasiswa.skill_id', '=', 'm_skill.skill_id')
                ->where('t_skill_mahasiswa.user_id', $user->id_user)
                ->select('m_skill.skill_id', 'm_skill.nama as nama_skill')
                ->get();

            return response()->json([
                'success' => true,
                'allSkills' => $allSkills,
                'userSkills' => $userSkills
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load skills data'
            ], 500);
        }
    }

    /**
     * Update user skills
     */
    public function updateSkills(Request $request)
    {
        try {
            $user = auth()->user();
            $skills = $request->input('skills', []);

            // Delete existing skills
            DB::table('t_skill_mahasiswa')
                ->where('user_id', $user->id_user)
                ->delete();

            // Insert new skills
            if (!empty($skills)) {
                $skillData = [];
                foreach ($skills as $skillId) {
                    $skillData[] = [
                        'user_id' => $user->id_user,
                        'skill_id' => $skillId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('t_skill_mahasiswa')->insert($skillData);
            }

            // Get updated skills for response
            $updatedSkills = DB::table('t_skill_mahasiswa')
                ->join('m_skill', 't_skill_mahasiswa.skill_id', '=', 'm_skill.skill_id')
                ->where('t_skill_mahasiswa.user_id', $user->id_user)
                ->select('m_skill.skill_id', 'm_skill.nama as nama_skill')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Skills updated successfully',
                'skills' => $updatedSkills
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update skills'
            ], 500);
        }
    }

    /**
     * Get minat data for profile management
     */
    public function getMinat()
    {
        try {
            $user = auth()->user();
            
            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa data not found'
                ], 404);
            }

            // Get all available minat
            $allMinat = DB::table('m_minat')
                ->select('minat_id', 'nama_minat')
                ->orderBy('nama_minat')
                ->get();

            // Get user's current minat
            $userMinat = DB::table('t_minat_mahasiswa')
                ->join('m_minat', 't_minat_mahasiswa.minat_id', '=', 'm_minat.minat_id')
                ->where('t_minat_mahasiswa.mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->select('m_minat.minat_id', 'm_minat.nama_minat')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'all_minat' => $allMinat,
                    'user_minat' => $userMinat
                ]
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
     * Update user minat
     */
    public function updateMinat(Request $request)
    {
        try {
            $user = auth()->user();
            $minatIds = $request->input('minat', []);

            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa data not found'
                ], 404);
            }

            // Delete existing minat
            DB::table('t_minat_mahasiswa')
                ->where('mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->delete();

            // Insert new minat
            if (!empty($minatIds)) {
                $minatData = [];
                foreach ($minatIds as $minatId) {
                    $minatData[] = [
                        'mahasiswa_id' => $mahasiswa->id_mahasiswa,
                        'minat_id' => $minatId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('t_minat_mahasiswa')->insert($minatData);
            }

            // Get updated minat for response
            $updatedMinat = DB::table('t_minat_mahasiswa')
                ->join('m_minat', 't_minat_mahasiswa.minat_id', '=', 'm_minat.minat_id')
                ->where('t_minat_mahasiswa.mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->select('m_minat.minat_id', 'm_minat.nama_minat')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Minat updated successfully',
                'data' => [
                    'minat' => $updatedMinat
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating minat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update minat'
            ], 500);
        }
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'telp' => 'nullable|string|max:15',
                'ipk' => 'nullable|numeric|min:0|max:4',
                'alamat' => 'nullable|string|max:255',
                'wilayah_id' => 'nullable|integer|exists:m_wilayah,wilayah_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();

            // Update user data
            DB::table('m_user')
                ->where('id_user', $user->id_user)
                ->update([
                    'name' => $request->name,
                    'updated_at' => now()
                ]);

            // Update mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if ($mahasiswa) {
                DB::table('m_mahasiswa')
                    ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->update([
                        'telp' => $request->telp,
                        'ipk' => $request->ipk,
                        'alamat' => $request->alamat,
                        'wilayah_id' => $request->wilayah_id,
                        'updated_at' => now()
                    ]);
            }

            // Get updated data
            $updatedUser = DB::table('m_user')
                ->where('id_user', $user->id_user)
                ->first();

            $updatedMahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $updatedUser,
                'mahasiswa' => $updatedMahasiswa
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile'
            ], 500);
        }
    }

    /**
     * Update avatar
     */
    public function updateAvatar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            
            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa data not found'
                ], 404);
            }

            // Delete old foto if exists
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }

            // Store new foto
            $file = $request->file('foto');
            $filename = 'mahasiswa_' . $user->id_user . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('mahasiswa/foto', $filename, 'public');

            // Update database
            DB::table('m_mahasiswa')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->update([
                    'foto' => $path,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'foto_url' => asset('storage/' . $path)
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating avatar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update avatar'
            ], 500);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update password
            DB::table('m_user')
                ->where('id_user', $user->id_user)
                ->update([
                    'password' => Hash::make($request->password),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password'
            ], 500);
        }
    }

    /**
     * Check profile completion status
     */
    public function checkCompletion()
    {
        try {
            $user = auth()->user();
            
            // Reuse the existing checkProfileCompletion method from ViewController
            $viewController = new \App\Http\Controllers\API\Mahasiswa\ViewController();
            $completion = $viewController->checkProfileCompletion($user->id_user);

            return response()->json([
                'success' => true,
                'data' => $completion
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking profile completion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check profile completion'
            ], 500);
        }
    }
}
