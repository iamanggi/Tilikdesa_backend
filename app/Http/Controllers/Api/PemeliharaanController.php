<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePemeliharaanRequest;
use App\Models\Pemeliharaan;
use App\Models\Lokasi;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller as BaseController;

class PemeliharaanController extends BaseController
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     * Menampilkan semua data pemeliharaan
     */
    public function index(): JsonResponse
    {
        $pemeliharaans = Pemeliharaan::with(['lokasi', 'laporan'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pemeliharaan berhasil diambil',
            'data' => $pemeliharaans,
        ]);
    }

    /**
     * Data untuk form create
     */
    public function create(): JsonResponse
    {
        $lokasis = Lokasi::all();
        $laporans = Report::all();

        return response()->json([
            'success' => true,
            'message' => 'Data untuk form create berhasil diambil',
            'data' => compact('lokasis', 'laporans'),
        ]);
    }

    /**
     * Menyimpan data pemeliharaan baru
     */
    public function store(StorePemeliharaanRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $data['foto'] = $foto->store('pemeliharaan_foto', 'public');
            }

            $pemeliharaan = Pemeliharaan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data pemeliharaan berhasil ditambahkan',
                'data' => $pemeliharaan->load(['lokasi', 'laporan']),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data pemeliharaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan detail satu pemeliharaan
     */
    public function show(Pemeliharaan $pemeliharaan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail pemeliharaan berhasil diambil',
            'data' => $pemeliharaan->load(['lokasi', 'laporan']),
        ]);
    }

    /**
     * Data untuk form edit
     */
    public function edit(Pemeliharaan $pemeliharaan): JsonResponse
    {
        $lokasis = Lokasi::all();
        $laporans = Report::all();

        return response()->json([
            'success' => true,
            'message' => 'Data untuk form edit berhasil diambil',
            'data' => [
                'pemeliharaan' => $pemeliharaan->load(['lokasi', 'laporan']),
                'lokasis' => $lokasis,
                'laporans' => $laporans,
            ],
        ]);
    }

    /**
     * Update data pemeliharaan
     */
    public function update(StorePemeliharaanRequest $request, Pemeliharaan $pemeliharaan): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($pemeliharaan->foto && Storage::disk('public')->exists($pemeliharaan->foto)) {
                    Storage::disk('public')->delete($pemeliharaan->foto);
                }

                $data['foto'] = $request->file('foto')->store('pemeliharaan_foto', 'public');
            }

            $pemeliharaan->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data pemeliharaan berhasil diperbarui',
                'data' => $pemeliharaan->load(['lokasi', 'laporan']),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data pemeliharaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus data pemeliharaan
     */
    public function destroy(Pemeliharaan $pemeliharaan): JsonResponse
    {
        try {
            // Hapus file foto dari storage jika ada
            if ($pemeliharaan->foto && Storage::disk('public')->exists($pemeliharaan->foto)) {
                Storage::disk('public')->delete($pemeliharaan->foto);
            }

            $pemeliharaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data pemeliharaan berhasil dihapus',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data pemeliharaan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
