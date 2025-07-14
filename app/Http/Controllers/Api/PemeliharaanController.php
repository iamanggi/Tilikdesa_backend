<?php

namespace App\Http\Controllers\Api;

use App\Models\Pemeliharaan;
use App\Models\Lokasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PemeliharaanController extends BaseController
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'admin']);
    }

    public function index(): JsonResponse
    {
        try {
            $pemeliharaans = Pemeliharaan::latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pemeliharaan berhasil diambil',
                'data' => $pemeliharaans,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching pemeliharaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pemeliharaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function create(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Form create tersedia',
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching create form data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data form',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'nama_fasilitas' => 'required|string|max:255',
                'tgl_pemeliharaan' => 'required|date',
                'status' => 'required|in:pending,progress,completed',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');

                if (!$foto->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File foto tidak valid',
                    ], 422);
                }

                $filename = time() . '_' . $foto->getClientOriginalName();
                $data['foto'] = $foto->storeAs('pemeliharaan_foto', $filename, 'public');
            }

            $pemeliharaan = Pemeliharaan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data pemeliharaan berhasil ditambahkan',
                'data' => $pemeliharaan,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating pemeliharaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data pemeliharaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Pemeliharaan $pemeliharaan): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Detail pemeliharaan berhasil diambil',
                'data' => $pemeliharaan,
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing pemeliharaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pemeliharaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Pemeliharaan $pemeliharaan): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Data untuk form edit berhasil diambil',
                'data' => $pemeliharaan,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching edit form data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data form edit',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Pemeliharaan $pemeliharaan): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'nama_fasilitas' => 'required|string|max:255',
                'tgl_pemeliharaan' => 'required|date',
                'status' => 'required|in:pending,progress,completed',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            if ($request->hasFile('foto')) {
                if ($pemeliharaan->foto && Storage::disk('public')->exists($pemeliharaan->foto)) {
                    Storage::disk('public')->delete($pemeliharaan->foto);
                }

                $foto = $request->file('foto');
                $filename = time() . '_' . $foto->getClientOriginalName();
                $data['foto'] = $foto->storeAs('pemeliharaan_foto', $filename, 'public');
            }

            $pemeliharaan->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data pemeliharaan berhasil diperbarui',
                'data' => $pemeliharaan,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating pemeliharaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data pemeliharaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Pemeliharaan $pemeliharaan): JsonResponse
    {
        try {
            if ($pemeliharaan->foto && Storage::disk('public')->exists($pemeliharaan->foto)) {
                Storage::disk('public')->delete($pemeliharaan->foto);
            }

            $pemeliharaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data pemeliharaan berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting pemeliharaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data pemeliharaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
