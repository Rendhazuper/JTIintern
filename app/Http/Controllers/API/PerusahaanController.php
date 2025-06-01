<?php
// filepath: d:\laragon\www\JTIintern\app\Http\Controllers\API\PerusahaanController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class PerusahaanController extends Controller
{
    public function index()
    {
        return view('pages.data_perusahaan');
    }

    public function showDetail($id)
    {
        return view('pages.detail_perusahaan', ['id' => $id]);
    }

    public function getPerusahaanData()
    {
        try {
            $perusahaan = Perusahaan::with(['wilayah', 'lowongan'])->get();

            return response()->json([
                'success' => true,
                'data' => $perusahaan->map(function ($p) {
                    return [
                        'perusahaan_id' => $p->perusahaan_id,
                        'nama_perusahaan' => $p->nama_perusahaan,
                        'alamat_perusahaan' => $p->alamat_perusahaan,
                        'wilayah' => $p->wilayah->nama_kota ?? 'Tidak Diketahui',
                        'wilayah_id' => $p->wilayah_id,
                        'contact_person' => $p->contact_person,
                        'email' => $p->email,
                        'instagram' => $p->instagram,
                        'website' => $p->website,
                        'deskripsi' => $p->deskripsi,
                        'gmaps' => $p->gmaps,
                        'lowongan_count' => $p->lowongan->count(),
                        'logo' => $p->logo,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching perusahaan data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetailPerusahaan($id)
    {
        try {
            $perusahaan = Perusahaan::with(['wilayah', 'lowongan'])->where('perusahaan_id', $id)->first();

            if (!$perusahaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perusahaan tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching detail perusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail perusahaan.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'wilayah_id' => 'required|exists:m_wilayah,wilayah_id',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'gmaps' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        try {
            // Buat data perusahaan
            $perusahaanData = $request->except('logo');

            // Tangani upload logo jika ada
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $logoPath = $request->file('logo')->store('perusahaan_logos', 'public');
                $perusahaanData['logo'] = $logoPath;
            }

            $perusahaan = Perusahaan::create($perusahaanData);

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil ditambahkan!',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding perusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'wilayah_id' => 'required|exists:m_wilayah,wilayah_id',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'gmaps' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        try {
            $perusahaan = Perusahaan::findOrFail($id);
            $perusahaanData = $request->except(['logo', '_method']);

            // Tangani upload logo jika ada
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                // Hapus logo lama jika ada
                if ($perusahaan->logo) {
                    $oldLogoPath = str_replace('storage/', '', $perusahaan->logo);
                    if (Storage::disk('public')->exists($oldLogoPath)) {
                        Storage::disk('public')->delete($oldLogoPath);
                    }
                }

                // Upload logo baru
                $logoPath = $request->file('logo')->store('perusahaan_logos', 'public');
                $perusahaanData['logo'] = $logoPath;
            }

            $perusahaan->update($perusahaanData);

            return response()->json([
                'success' => true,
                'message' => 'Data perusahaan berhasil diperbarui!',
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating perusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $perusahaan = Perusahaan::with(['wilayah', 'lowongan'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Cari perusahaan
            $perusahaan = Perusahaan::findOrFail($id);

            // Mulai transaksi database
            DB::beginTransaction();

            // 1. Hapus semua lowongan terkait
            $perusahaan->lowongan()->delete();

            // 2. Hapus file logo jika ada
            if ($perusahaan->logo && !empty($perusahaan->logo)) {
                $logoPath = str_replace('storage/', '', $perusahaan->logo);
                if (Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
            }

            // 3. Hapus perusahaan
            $perusahaan->delete();

            // Commit transaksi
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data perusahaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            Log::error('Error deleting perusahaan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function import(Request $request)
    {
        // Validate the request
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xls,xlsx|max:2048'
        ]);

        try {
            $file = $request->file('csv_file');
            $extension = $file->getClientOriginalExtension();

            // Process file based on extension
            if (in_array($extension, ['xls', 'xlsx'])) {
                return $this->importExcel($file);
            } else {
                return $this->importCSV($file);
            }
        } catch (\Exception $e) {
            Log::error('Error in import: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import data from CSV file
     */
    private function importCSV($file)
    {
        $path = $file->getRealPath();
        $content = file_get_contents($path);

        // Detect delimiter (look for both semicolons and commas)
        $delimiter = ','; // default
        if (strpos($content, ';') !== false) {
            // File contains semicolons
            $delimiter = ';';
        }

        $file = fopen($path, 'r');

        // Handle potential UTF-8 BOM characters in CSV
        $firstRow = fgets($file, 4);
        if (strpos($firstRow, "\xEF\xBB\xBF") === 0) {
            // File has UTF-8 BOM, rewind and skip it
            rewind($file);
            fread($file, 3);
        } else {
            // No BOM, rewind to start
            rewind($file);
        }

        // Read header with the detected delimiter
        $header = fgetcsv($file, 0, $delimiter);

        if (!$header) {
            fclose($file);
            return response()->json([
                'success' => false,
                'message' => 'Format file tidak valid atau file kosong'
            ], 400);
        }

        // Transform headers to lowercase for case-insensitive matching
        $header = array_map('strtolower', $header);
        $header = array_map('trim', $header);

        // Map header columns to expected field names
        $headerMap = [
            'nama perusahaan' => 'nama_perusahaan',
            'nama_perusahaan' => 'nama_perusahaan',
            'alamat perusahaan' => 'alamat_perusahaan',
            'alamat_perusahaan' => 'alamat_perusahaan',
            'wilayah' => 'wilayah',
            'wilayah_id' => 'wilayah_id',
            'contact person' => 'contact_person',
            'contact_person' => 'contact_person',
            'email' => 'email',
            'instagram' => 'instagram',
            'website' => 'website',
            'deskripsi' => 'deskripsi',
            'deskripsi perusahaan' => 'deskripsi',
            'gmaps' => 'gmaps',
            'google maps' => 'gmaps',
            'link maps' => 'gmaps'
        ];

        // Find column indexes for each field
        $columnMap = [];
        foreach ($header as $index => $columnName) {
            if (isset($headerMap[$columnName])) {
                $fieldName = $headerMap[$columnName];
                $columnMap[$fieldName] = $index;
            }
        }

        // Check required columns
        $requiredFields = ['nama_perusahaan', 'contact_person', 'email'];
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

        // The rest of your importCSV function remains the same, except use the detected delimiter
        $imported = 0;
        $errors = [];
        $rowNumber = 1; // Start with row 1 (after header)

        // Get all wilayah for mapping
        $allWilayahById = \App\Models\Wilayah::pluck('nama_kota', 'wilayah_id')->toArray();
        $allWilayahByName = \App\Models\Wilayah::pluck('wilayah_id', 'nama_kota')->toArray();
        $allWilayahByNameLower = array_change_key_case($allWilayahByName, CASE_LOWER);

        DB::beginTransaction();

        // Process all rows with the correct delimiter
        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            // Extract data
            $data = [];
            $data['nama_perusahaan'] = isset($columnMap['nama_perusahaan']) && isset($row[$columnMap['nama_perusahaan']])
                ? trim($row[$columnMap['nama_perusahaan']]) : null;
            $data['alamat_perusahaan'] = isset($columnMap['alamat_perusahaan']) && isset($row[$columnMap['alamat_perusahaan']])
                ? trim($row[$columnMap['alamat_perusahaan']]) : null;
            $data['contact_person'] = isset($columnMap['contact_person']) && isset($row[$columnMap['contact_person']])
                ? trim($row[$columnMap['contact_person']]) : null;
            $data['email'] = isset($columnMap['email']) && isset($row[$columnMap['email']])
                ? trim($row[$columnMap['email']]) : null;
            $data['instagram'] = isset($columnMap['instagram']) && isset($row[$columnMap['instagram']])
                ? trim($row[$columnMap['instagram']]) : null;
            $data['website'] = isset($columnMap['website']) && isset($row[$columnMap['website']])
                ? trim($row[$columnMap['website']]) : null;
            $data['deskripsi'] = isset($columnMap['deskripsi']) && isset($row[$columnMap['deskripsi']])
                ? trim($row[$columnMap['deskripsi']]) : null;
            $data['gmaps'] = isset($columnMap['gmaps']) && isset($row[$columnMap['gmaps']])
                ? trim($row[$columnMap['gmaps']]) : null;

            // Handle wilayah_id - try both wilayah and wilayah_id columns
            if (isset($columnMap['wilayah_id']) && isset($row[$columnMap['wilayah_id']]) && !empty($row[$columnMap['wilayah_id']])) {
                // Direct wilayah_id provided
                $wilayahId = trim($row[$columnMap['wilayah_id']]);
                // Check if ID exists
                if (!isset($allWilayahById[$wilayahId])) {
                    $errors[] = "Error pada baris {$rowNumber}: Wilayah ID '{$wilayahId}' tidak ditemukan";
                    continue;
                }
                $data['wilayah_id'] = $wilayahId;
            } elseif (isset($columnMap['wilayah']) && isset($row[$columnMap['wilayah']]) && !empty($row[$columnMap['wilayah']])) {
                // Wilayah name provided, need to find ID
                $wilayahName = trim($row[$columnMap['wilayah']]);

                // Try exact match first
                if (isset($allWilayahByName[$wilayahName])) {
                    $data['wilayah_id'] = $allWilayahByName[$wilayahName];
                }
                // Try case-insensitive match
                elseif (isset($allWilayahByNameLower[strtolower($wilayahName)])) {
                    $data['wilayah_id'] = $allWilayahByNameLower[strtolower($wilayahName)];
                }
                // No match found
                else {
                    $errors[] = "Error pada baris {$rowNumber}: Wilayah '{$wilayahName}' tidak ditemukan";
                    continue;
                }
            } else {
                $errors[] = "Error pada baris {$rowNumber}: Wilayah tidak valid atau kosong";
                continue;
            }

            // Validate data
            $validator = Validator::make($data, [
                'nama_perusahaan' => 'required|string|max:255',
                'wilayah_id' => 'required|exists:m_wilayah,wilayah_id',
                'contact_person' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'alamat_perusahaan' => 'nullable|string',
                'instagram' => 'nullable|string|max:255',
                'website' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'gmaps' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                $errors[] = "Error pada baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                continue;
            }

            try {
                // Create perusahaan
                Perusahaan::create([
                    'nama_perusahaan' => $data['nama_perusahaan'],
                    'alamat_perusahaan' => $data['alamat_perusahaan'] ?? '',
                    'wilayah_id' => $data['wilayah_id'],
                    'contact_person' => $data['contact_person'],
                    'email' => $data['email'],
                    'instagram' => $data['instagram'] ?? '',
                    'website' => $data['website'] ?? '',
                    'deskripsi' => $data['deskripsi'] ?? '',
                    'gmaps' => $data['gmaps'] ?? ''
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Error pada baris {$rowNumber} ({$data['nama_perusahaan']}): " . $e->getMessage();
            }
        }

        fclose($file);

        if ($imported > 0) {
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$imported} data perusahaan" . (count($errors) > 0 ? " (dengan " . count($errors) . " error)" : ""),
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
    }

    /**
     * Import data from Excel file
     */
    private function importExcel($file)
    {
        // If you've installed PhpSpreadsheet, use this implementation
        // If not, install with: composer require phpoffice/phpspreadsheet

        try {
            // Require PhpSpreadsheet
            if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                return response()->json([
                    'success' => false,
                    'message' => 'PhpSpreadsheet library tidak terinstal. Gunakan file CSV atau instal library.'
                ], 500);
            }

            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(
                pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION) === 'xlsx' ? 'Xlsx' : 'Xls'
            );
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // First row is the header
            $header = array_map('strtolower', array_map('trim', $rows[0]));

            // Map header columns to expected field names (same as CSV import)
            $headerMap = [
                'nama perusahaan' => 'nama_perusahaan',
                'nama_perusahaan' => 'nama_perusahaan',
                'alamat perusahaan' => 'alamat_perusahaan',
                'alamat_perusahaan' => 'alamat_perusahaan',
                'wilayah' => 'wilayah',
                'wilayah_id' => 'wilayah_id',
                'contact person' => 'contact_person',
                'contact_person' => 'contact_person',
                'email' => 'email',
                'instagram' => 'instagram',
                'website' => 'website',
                'deskripsi' => 'deskripsi',
                'deskripsi perusahaan' => 'deskripsi',
                'gmaps' => 'gmaps',
                'google maps' => 'gmaps',
                'link maps' => 'gmaps'
            ];

            // Find column indexes for each field
            $columnMap = [];
            foreach ($header as $index => $columnName) {
                if (isset($headerMap[$columnName])) {
                    $fieldName = $headerMap[$columnName];
                    $columnMap[$fieldName] = $index;
                }
            }

            // Check required columns
            $requiredFields = ['nama_perusahaan', 'contact_person', 'email'];
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
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak memiliki kolom wajib: ' . implode(', ', $missingColumns)
                ], 400);
            }

            // Remove header row
            array_shift($rows);

            $imported = 0;
            $errors = [];

            // Get all wilayah for mapping
            $allWilayahById = \App\Models\Wilayah::pluck('nama_kota', 'wilayah_id')->toArray();
            $allWilayahByName = \App\Models\Wilayah::pluck('wilayah_id', 'nama_kota')->toArray();
            $allWilayahByNameLower = array_change_key_case($allWilayahByName, CASE_LOWER);

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because index 0 was header and we're 1-indexed in display

                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Extract data
                $data = [];
                $data['nama_perusahaan'] = isset($columnMap['nama_perusahaan']) && isset($row[$columnMap['nama_perusahaan']])
                    ? trim($row[$columnMap['nama_perusahaan']]) : null;
                $data['alamat_perusahaan'] = isset($columnMap['alamat_perusahaan']) && isset($row[$columnMap['alamat_perusahaan']])
                    ? trim($row[$columnMap['alamat_perusahaan']]) : null;
                $data['contact_person'] = isset($columnMap['contact_person']) && isset($row[$columnMap['contact_person']])
                    ? trim($row[$columnMap['contact_person']]) : null;
                $data['email'] = isset($columnMap['email']) && isset($row[$columnMap['email']])
                    ? trim($row[$columnMap['email']]) : null;
                $data['instagram'] = isset($columnMap['instagram']) && isset($row[$columnMap['instagram']])
                    ? trim($row[$columnMap['instagram']]) : null;
                $data['website'] = isset($columnMap['website']) && isset($row[$columnMap['website']])
                    ? trim($row[$columnMap['website']]) : null;
                $data['deskripsi'] = isset($columnMap['deskripsi']) && isset($row[$columnMap['deskripsi']])
                    ? trim($row[$columnMap['deskripsi']]) : null;
                $data['gmaps'] = isset($columnMap['gmaps']) && isset($row[$columnMap['gmaps']])
                    ? trim($row[$columnMap['gmaps']]) : null;

                // Handle wilayah_id - similar to CSV import
                if (isset($columnMap['wilayah_id']) && isset($row[$columnMap['wilayah_id']]) && !empty($row[$columnMap['wilayah_id']])) {
                    $wilayahId = trim((string)$row[$columnMap['wilayah_id']]);
                    if (!isset($allWilayahById[$wilayahId])) {
                        $errors[] = "Error pada baris {$rowNumber}: Wilayah ID '{$wilayahId}' tidak ditemukan";
                        continue;
                    }
                    $data['wilayah_id'] = $wilayahId;
                } elseif (isset($columnMap['wilayah']) && isset($row[$columnMap['wilayah']]) && !empty($row[$columnMap['wilayah']])) {
                    $wilayahName = trim((string)$row[$columnMap['wilayah']]);

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

                // Validate data (same as CSV import)
                $validator = Validator::make($data, [
                    'nama_perusahaan' => 'required|string|max:255',
                    'wilayah_id' => 'required|exists:m_wilayah,wilayah_id',
                    'contact_person' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'alamat_perusahaan' => 'nullable|string',
                    'instagram' => 'nullable|string|max:255',
                    'website' => 'nullable|string|max:255',
                    'deskripsi' => 'nullable|string',
                    'gmaps' => 'nullable|string'
                ]);

                if ($validator->fails()) {
                    $errors[] = "Error pada baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                try {
                    // Create perusahaan
                    Perusahaan::create([
                        'nama_perusahaan' => $data['nama_perusahaan'],
                        'alamat_perusahaan' => $data['alamat_perusahaan'] ?? '',
                        'wilayah_id' => $data['wilayah_id'],
                        'contact_person' => $data['contact_person'],
                        'email' => $data['email'],
                        'instagram' => $data['instagram'] ?? '',
                        'website' => $data['website'] ?? '',
                        'deskripsi' => $data['deskripsi'] ?? '',
                        'gmaps' => $data['gmaps'] ?? ''
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error pada baris {$rowNumber} ({$data['nama_perusahaan']}): " . $e->getMessage();
                }
            }

            if ($imported > 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengimpor {$imported} data perusahaan" . (count($errors) > 0 ? " (dengan " . count($errors) . " error)" : ""),
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saat memproses file Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Get filtered data
            $query = DB::table('m_perusahaan')
                ->leftJoin('m_wilayah', 'm_perusahaan.wilayah_id', '=', 'm_wilayah.wilayah_id')
                ->leftJoin(
                    // Changed id_perusahaan to perusahaan_id in subquery
                    DB::raw('(SELECT perusahaan_id, COUNT(*) as lowongan_count FROM m_lowongan GROUP BY perusahaan_id) l'),
                    'm_perusahaan.perusahaan_id',
                    '=',
                    'l.perusahaan_id' // Changed id_perusahaan to perusahaan_id
                )
                ->select(
                    'm_perusahaan.*',
                    'm_wilayah.nama_kota',
                    DB::raw('COALESCE(l.lowongan_count, 0) as lowongan_count')
                );

            // Apply filters if any
            if ($request->filled('wilayah')) {
                $query->where('m_perusahaan.wilayah_id', '=', $request->wilayah);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('m_perusahaan.nama_perusahaan', 'like', "%{$searchTerm}%")
                        ->orWhere('m_perusahaan.alamat_perusahaan', 'like', "%{$searchTerm}%");
                });
            }

            $perusahaan = $query->orderBy('m_perusahaan.nama_perusahaan')->get();

            // Get current timestamp
            $timestamp = Carbon::now()->format('d-m-Y_H-i-s');

            // Load the view for PDF
            $pdf = PDF::loadView('exports.perusahaan-pdf', [
                'perusahaan' => $perusahaan,
                'timestamp' => Carbon::now()->format('d F Y H:i:s'),
                'total' => $perusahaan->count()
            ]);

            // Set paper to landscape for better table viewing
            $pdf->setPaper('a4', 'landscape');

            // Generate PDF content
            $content = $pdf->output();

            // Return response with proper headers
            return response($content)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="data_perusahaan_' . $timestamp . '.pdf"')
                ->header('Content-Length', strlen($content));
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengeksport PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
