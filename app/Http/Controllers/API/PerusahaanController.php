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
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $path = $request->file('csv_file')->getRealPath();

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak dapat diakses'
                ], 400);
            }

            $file = fopen($path, 'r');
            $header = fgetcsv($file); // Get header row

            // Expected headers
            $expectedHeaders = ['nama_perusahaan', 'alamat_perusahaan', 'wilayah', 'contact_person', 'email', 'instagram', 'website', 'deskripsi'];

            // Validate headers
            if ($header !== $expectedHeaders) {
                fclose($file);
                return response()->json([
                    'success' => false,
                    'message' => 'Format CSV tidak sesuai. Header yang diharapkan: ' . implode(', ', $expectedHeaders)
                ], 400);
            }

            $imported = 0;
            $errors = [];

            // Get all wilayah for mapping
            $allWilayah = \App\Models\Wilayah::pluck('wilayah_id', 'nama_kota')->toArray();

            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                // Skip if row doesn't have enough columns
                if (count($row) !== count($expectedHeaders)) {
                    continue;
                }

                $namaWilayah = trim($row[2]); // Get wilayah from CSV
                
                // Find wilayah_id based on nama_kota
                $wilayahId = array_key_exists($namaWilayah, $allWilayah) ? $allWilayah[$namaWilayah] : null;

                $data = [
                    'nama_perusahaan' => $row[0],
                    'alamat_perusahaan' => $row[1],
                    'wilayah_id' => $wilayahId,
                    'contact_person' => $row[3],
                    'email' => $row[4],
                    'instagram' => $row[5],
                    'website' => $row[6],
                    'deskripsi' => $row[7],
                    'nama_wilayah' => $namaWilayah // Save for error message
                ];

                // Validate data
                $validator = Validator::make($data, [
                    'nama_perusahaan' => 'required|string|max:255',
                    'alamat_perusahaan' => 'required|string',
                    'wilayah_id' => 'required|exists:m_wilayah,wilayah_id',
                    'contact_person' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'instagram' => 'nullable|string|max:255',
                    'website' => 'nullable|url|max:255',
                    'deskripsi' => 'nullable|string'
                ], [
                    'wilayah_id.required' => "Wilayah '{$namaWilayah}' tidak ditemukan",
                    'wilayah_id.exists' => "Wilayah '{$namaWilayah}' tidak ditemukan"
                ]);

                if ($validator->fails()) {
                    $errors[] = "Error pada baris data {$data['nama_perusahaan']}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                try {
                    // Create perusahaan
                    Perusahaan::create([
                        'nama_perusahaan' => $data['nama_perusahaan'],
                        'alamat_perusahaan' => $data['alamat_perusahaan'],
                        'wilayah_id' => $data['wilayah_id'],
                        'contact_person' => $data['contact_person'],
                        'email' => $data['email'],
                        'instagram' => $data['instagram'],
                        'website' => $data['website'],
                        'deskripsi' => $data['deskripsi']
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error pada data {$data['nama_perusahaan']}: " . $e->getMessage();
                }
            }

            fclose($file);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$imported} data perusahaan",
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($file)) {
                fclose($file);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()
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