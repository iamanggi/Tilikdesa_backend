<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam email string required Alamat email pengguna. Contoh: john@example.com
 * @bodyParam password string required Kata sandi pengguna. Contoh: rahasia123
 */
class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'laporan_id' => 'required|exists:laporans,id',
            'bintang' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:255',
        ];
    }
}
