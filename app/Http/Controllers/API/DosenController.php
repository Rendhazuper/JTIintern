<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Magang;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Exception;


class DosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::with(['user', 'wilayah'])->get();
        $data = $dosen->map(function ($item) {
            return [
                'id_dosen' => $item->id_dosen,
                'nama_dosen' => $item->user->name ?? '-',
                'email' => $item->user->email ?? '-',
                'wilayah' => $item->wilayah->nama_kota ?? '-', // pastikan relasi wilayah ada
                'nip' => $item->nip ?? '-',
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'wilayah_id' => 'required|integer|exists:m_wilayah,wilayah_id',
            'nip' => 'required|unique:m_dosen,nip',
        ]);

        // Email dan password sama dengan NIP
        $nip = $request->nip;
        $email = $nip . '@gmail.com';

        // Buat user baru
        $user = User::create([
            'name' => $request->nama_dosen,
            'email' => $email,
            'password' => bcrypt($nip),
            'role' => 'dosen'
        ]);

        // Buat dosen baru
        Dosen::create([
            'user_id' => $user->id_user,
            'wilayah_id' => $request->wilayah_id,
            'nip' => $nip
        ]);

        return response()->json(['success' => true]);
    }
    public function show($id)
    {
        $dosen = Dosen::with(['user', 'wilayah'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [
                'id_dosen' => $dosen->id_dosen,
                'nama_dosen' => $dosen->user->name ?? '-',
                'email' => $dosen->user->email ?? '-',
                'wilayah_id' => $dosen->wilayah_id,
                'wilayah' => $dosen->wilayah->nama_kota ?? '-',
                'nip' => $dosen->nip ?? '-',
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'wilayah_id' => 'required|integer|exists:m_wilayah,wilayah_id',
            'nip' => 'required|unique:m_dosen,nip,' . $id . ',id_dosen',
        ]);

        $dosen = Dosen::findOrFail($id);
        $user = $dosen->user;

        // Update user (nama)
        $user->name = $request->nama_dosen;
        $user->save();

        // Update dosen
        $dosen->wilayah_id = $request->wilayah_id;
        $dosen->nip = $request->nip;
        $dosen->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $dosen = Dosen::findOrFail($id);
        $user = $dosen->user;
        $dosen->delete();
        if ($user) $user->delete();
        return response()->json(['success' => true]);
    }

    public function withPerusahaan()
    {
        try {
            // Remove this line:
            // DB::statement("SET SESSION query_cache_type = OFF");

            // Keep everything else the same
            $dosens = Dosen::with(['user', 'wilayah'])->get();

            $dosens = $dosens->map(function ($dosen) {
                try {
                    // Load magangBimbingan relationship manually
                    $bimbingan = Magang::where('id_dosen', $dosen->id_dosen)
                        ->where(function ($query) {
                            $query->where('status', '!=', 'ditolak')
                                ->orWhereNull('status');
                        })
                        ->get(['id_magang', 'id_mahasiswa', 'id_lowongan', 'status', 'created_at']);

                    // Log count for debugging
                    Log::info("Dosen ID {$dosen->id_dosen} has {$bimbingan->count()} bimbingan");

                    // Create consistent property names
                    $dosen->magangBimbingan = $bimbingan;
                    $dosen->magang_bimbingan = $bimbingan;
                } catch (\Exception $e) {
                    Log::error('Error loading magangBimbingan for dosen ID ' . $dosen->id_dosen . ': ' . $e->getMessage());
                    $dosen->magangBimbingan = [];
                    $dosen->magang_bimbingan = [];
                }

                return $dosen;
            });

            return response()->json([
                'success' => true,
                'data' => $dosens,
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            Log::error('Error in withPerusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function withDetails()
    {
        try {
            // Get dosen with relevant relationships
            $dosens = Dosen::with([
                'user',
                'wilayah',
                'skills.skill'
            ])->get();

            $dosens = $dosens->map(function ($dosen) {
                try {
                    // Load magangBimbingan relationship manually
                    $bimbingan = Magang::where('id_dosen', $dosen->id_dosen)
                        ->where(function ($query) {
                            $query->where('status', '!=', 'ditolak')
                                ->orWhereNull('status');
                        })
                        ->get(['id_magang', 'id_mahasiswa', 'id_lowongan', 'status', 'created_at']);

                    // Create consistent property names
                    $dosen->magangBimbingan = $bimbingan;
                    $dosen->magang_bimbingan = $bimbingan;
                } catch (\Exception $e) {
                    Log::error('Error loading magangBimbingan: ' . $e->getMessage());
                    $dosen->magangBimbingan = [];
                    $dosen->magang_bimbingan = [];
                }

                return $dosen;
            });

            return response()->json([
                'success' => true,
                'data' => $dosens,
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            Log::error('Error in withDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeAssignments($id)
    {
        try {
            // Log the request for debugging
            Log::info('Removing assignments for dosen ID: ' . $id);

            // Find all magang records where id_dosen matches $id and reset them
            $count = Magang::where('id_dosen', $id)->update(['id_dosen' => null]);

            Log::info('Removed ' . $count . ' assignments for dosen ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Assignments removed successfully',
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing assignments: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignMahasiswa(Request $request, $id)
    {
        try {
            // Validate request
            $request->validate([
                'magang_ids' => 'required|array',
                'magang_ids.*' => 'integer|exists:m_magang,id_magang'
            ]);

            // Log the assignment request
            Log::info('Assigning magang to dosen', [
                'dosen_id' => $id,
                'magang_ids' => $request->magang_ids
            ]);

            // Update the magang records with the dosen ID
            $count = Magang::whereIn('id_magang', $request->magang_ids)
                ->update(['id_dosen' => $id]);

            Log::info("Successfully assigned $count magang to dosen ID: $id");

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned $count magang",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning mahasiswa: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        // Validate request
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $content = file_get_contents($path);

            // Detect delimiter (comma or semicolon)
            $delimiter = ',';
            if (strpos($content, ';') !== false) {
                $delimiter = ';';
            }

            $file = fopen($path, 'r');

            // Handle UTF-8 BOM
            $firstRow = fgets($file, 4);
            if (strpos($firstRow, "\xEF\xBB\xBF") === 0) {
                rewind($file);
                fread($file, 3);
            } else {
                rewind($file);
            }

            // Read header
            $header = fgetcsv($file, 0, $delimiter);

            if (!$header) {
                fclose($file);
                return response()->json([
                    'success' => false,
                    'message' => 'Format file tidak valid atau file kosong'
                ], 400);
            }

            // Map headers to expected field names (case insensitive)
            $header = array_map('strtolower', array_map('trim', $header));

            // Define header mappings
            $headerMap = [
                'nama' => 'nama_dosen',
                'nama dosen' => 'nama_dosen',
                'nama_dosen' => 'nama_dosen',
                'nip' => 'nip',
                'wilayah' => 'wilayah',
                'wilayah_id' => 'wilayah_id'
            ];

            // Map column indices
            $columnMap = [];
            foreach ($header as $index => $columnName) {
                if (isset($headerMap[$columnName])) {
                    $fieldName = $headerMap[$columnName];
                    $columnMap[$fieldName] = $index;
                }
            }

            // Verify required fields
            $requiredFields = ['nama_dosen', 'nip'];
            $missingColumns = [];

            foreach ($requiredFields as $field) {
                if (!isset($columnMap[$field])) {
                    $missingColumns[] = $field;
                }
            }

            // Either wilayah or wilayah_id must be present
            if (!isset($columnMap['wilayah']) && !isset($columnMap['wilayah_id'])) {
                $missingColumns[] = 'wilayah/wilayah_id';
            }

            if (count($missingColumns) > 0) {
                fclose($file);
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak memiliki kolom wajib: ' . implode(', ', $missingColumns)
                ], 400);
            }

            // Get all wilayah for mapping
            $allWilayahById = \App\Models\Wilayah::pluck('nama_kota', 'wilayah_id')->toArray();
            $allWilayahByName = \App\Models\Wilayah::pluck('wilayah_id', 'nama_kota')->toArray();
            $allWilayahByNameLower = array_change_key_case($allWilayahByName, CASE_LOWER);

            $imported = 0;
            $errors = [];
            $rowNumber = 1; // Start with row 1 (after header)

            DB::beginTransaction();

            while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
                $rowNumber++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Extract data
                    $data = [];
                    $data['nama_dosen'] = isset($columnMap['nama_dosen']) && isset($row[$columnMap['nama_dosen']])
                        ? trim($row[$columnMap['nama_dosen']]) : null;
                    $data['nip'] = isset($columnMap['nip']) && isset($row[$columnMap['nip']])
                        ? trim($row[$columnMap['nip']]) : null;

                    // Handle wilayah_id
                    if (isset($columnMap['wilayah_id']) && isset($row[$columnMap['wilayah_id']]) && !empty($row[$columnMap['wilayah_id']])) {
                        // Direct wilayah_id provided
                        $wilayahId = trim($row[$columnMap['wilayah_id']]);
                        if (!isset($allWilayahById[$wilayahId])) {
                            $errors[] = "Error pada baris {$rowNumber}: Wilayah ID '{$wilayahId}' tidak ditemukan";
                            continue;
                        }
                        $data['wilayah_id'] = $wilayahId;
                    } elseif (isset($columnMap['wilayah']) && isset($row[$columnMap['wilayah']]) && !empty($row[$columnMap['wilayah']])) {
                        // Wilayah name provided, need to find ID
                        $wilayahName = trim($row[$columnMap['wilayah']]);

                        if (isset($allWilayahByName[$wilayahName])) {
                            $data['wilayah_id'] = $allWilayahByName[$wilayahName];
                        } elseif (isset($allWilayahByNameLower[strtolower($wilayahName)])) {
                            $data['wilayah_id'] = $allWilayahByNameLower[strtolower($wilayahName)];
                        } else {
                            $errors[] = "Error pada baris {$rowNumber}: Wilayah '{$wilayahName}' tidak ditemukan";
                            continue;
                        }
                    } else {
                        $errors[] = "Error pada baris {$rowNumber}: Wilayah tidak valid atau kosong";
                        continue;
                    }

                    // Check if NIP already exists
                    $existingDosen = Dosen::where('nip', $data['nip'])->first();
                    if ($existingDosen) {
                        $errors[] = "Error pada baris {$rowNumber}: NIP '{$data['nip']}' sudah terdaftar";
                        continue;
                    }

                    // Generate email based on NIP (to match store method)
                    $email = $data['nip'] . '@gmail.com';

                    // Check if email already exists
                    $existingUser = User::where('email', $email)->first();
                    if ($existingUser) {
                        $errors[] = "Error pada baris {$rowNumber}: Email '{$email}' sudah digunakan";
                        continue;
                    }

                    // Create a user account for the dosen (using NIP as password)
                    $user = User::create([
                        'name' => $data['nama_dosen'],
                        'email' => $email,
                        'password' => bcrypt($data['nip']), // Use NIP as password to match store method
                        'role' => 'dosen',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Create dosen record with user_id
                    Dosen::create([
                        'user_id' => $user->id_user, // Note: using id_user, not id
                        'nip' => $data['nip'],
                        'wilayah_id' => $data['wilayah_id'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error pada baris {$rowNumber}: " . $e->getMessage();
                }
            }

            fclose($file);

            if ($imported > 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengimpor {$imported} data dosen" . (count($errors) > 0 ? " (dengan " . count($errors) . " error)" : ""),
                    'imported' => $imported,
                    'errors' => $errors
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Tidak ada data yang berhasil diimpor",
                    'errors' => $errors
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data dosen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Get filtered data
            $query = DB::table('m_dosen')
                ->leftJoin('m_user', 'm_dosen.user_id', '=', 'm_user.id_user')
                ->leftJoin('m_wilayah', 'm_dosen.wilayah_id', '=', 'm_wilayah.wilayah_id')
                ->leftJoin(
                    DB::raw('(SELECT id_dosen, COUNT(*) as bimbingan_count FROM m_magang WHERE status != "ditolak" OR status IS NULL GROUP BY id_dosen) m'),
                    'm_dosen.id_dosen',
                    '=',
                    'm.id_dosen'
                )
                ->select(
                    'm_dosen.id_dosen',
                    'm_dosen.nip',
                    'm_user.name as nama_dosen',
                    'm_user.email',
                    'm_wilayah.nama_kota as wilayah',
                    DB::raw('COALESCE(m.bimbingan_count, 0) as jumlah_bimbingan')
                );

            $dosen = $query->orderBy('m_user.name')->get();

            // Get current timestamp
            $timestamp = Carbon::now()->format('d-m-Y_H-i-s');

            // Load the view for PDF
            $pdf = PDF::loadView('exports.dosen-pdf', [
                'dosen' => $dosen,
                'timestamp' => Carbon::now()->format('d F Y H:i:s'),
                'total' => $dosen->count()
            ]);

            // Set paper to landscape for better table viewing
            $pdf->setPaper('a4', 'landscape');

            // Return the PDF for download
            return $pdf->download("data_dosen_{$timestamp}.pdf");
        } catch (Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengeksport PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
