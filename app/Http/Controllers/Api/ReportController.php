<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar laporan.
     * - Admin: lihat semua laporan
     * - Masyarakat: lihat laporan miliknya
     */
    public function index(Request $request)
    {
        $query = Report::query();

        if ($request->user()->role === 'masyarakat') {
            $query->where('id_user', $request->user()->id_user);
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * Simpan laporan baru.
     */
    public function store(Request $request)
    {
        // Log request data for debugging
        Log::info('Report store request', [
            'all_data' => $request->all(),
            'files' => $request->file(),
            'has_photo' => $request->hasFile('photo')
        ]);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi',
            ], 401);
        }

        // Upload foto
        $photoPath = null;
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('reports', $filename, 'public');

                Log::info('Photo uploaded successfully', [
                    'filename' => $filename,
                    'path' => $photoPath
                ]);
            } catch (\Exception $e) {
                Log::error('Photo upload failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupload foto: ' . $e->getMessage()
                ], 500);
            }
        } else {
            Log::error('No photo file found in request');
            return response()->json([
                'success' => false,
                'message' => 'Foto harus diupload'
            ], 422);
        }

        $reportData = [
            'id_user' => $user->id_user,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'photos' => [$photoPath], // simpan sebagai array
            'status' => 'baru',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ];

        try {
            $report = Report::create($reportData);

            Log::info('Report created successfully', ['report_id' => $report->id]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'data' => $report
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create report', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail laporan berdasarkan ID.
     */
    public function show($id)
    {
        $report = Report::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Hapus laporan.
     */
    public function destroy(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        // Validasi kepemilikan laporan
        if ($report->id_user !== $request->user()->id_user && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki izin untuk menghapus laporan ini'
            ], 403);
        }

        if ($report->photos && is_array($report->photos)) {
            foreach ($report->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dihapus'
        ]);
    }

    /**
     * Update status laporan oleh admin.
     */
    public function updateStatus(Request $request, $id)
    {
        // Hanya admin yang boleh update
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:baru,diproses,selesai,ditolak',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $report = Report::findOrFail($id);

        $report->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status laporan berhasil diperbarui',
            'data' => $report->fresh()
        ]);
    }

//     public function adminIndex()
// {
//     $reports = \App\Models\Report::with('user', 'category')->latest()->get();

//     $data = $reports->map(function ($report) {
//         return [
//             'id' => $report->id,
//             'id_user' => $report->id_user,
//             'user_name' => $report->user?->name,
//             'category_id' => $report->category_id,
//             'category_name' => $report->category?->name,
//             'title' => $report->title,
//             'description' => $report->description,
//             'status' => $report->status,
//             'latitude' => $report->latitude,
//             'longitude' => $report->longitude,
//             'address' => $report->address,
//             'photos' => $report->photos,
//             'created_at' => $report->created_at?->toDateTimeString(),
//             'updated_at' => $report->updated_at?->toDateTimeString(),
//         ];
//     });

//     return response()->json([
//         'success' => true,
//         'data' => $data,
//     ]);
// }

}
