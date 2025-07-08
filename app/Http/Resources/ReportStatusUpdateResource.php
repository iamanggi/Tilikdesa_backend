<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportStatusUpdateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'catatan' => $this->note,
            'tanggal' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
