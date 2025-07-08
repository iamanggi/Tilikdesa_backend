<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam email string required Alamat email pengguna. Contoh: john@example.com
 * @bodyParam password string required Kata sandi pengguna. Contoh: rahasia123
 */
class VerifikasiLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'verifikasi' => 'required|in:diterima,ditolak',
            'alasan_penolakan' => 'required_if:verifikasi,ditolak|string|nullable',
        ];
    }
}