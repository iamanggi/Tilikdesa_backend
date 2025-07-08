<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kategori' => $this->category->name ?? null,
            'deskripsi' => $this->description,
            'gambar' => asset('storage/' . $this->image),
            'status' => $this->status,
            'lokasi' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude
            ],
            'pelapor' => $this->user->name ?? 'Anonim',
            'tanggal_laporan' => $this->created_at->format('Y-m-d H:i'),
            'status_update' => ReportStatusUpdateResource::collection($this->statusUpdates),
        ];
    }
}
