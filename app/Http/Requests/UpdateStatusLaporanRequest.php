<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam email string required Alamat email pengguna. Contoh: john@example.com
 * @bodyParam password string required Kata sandi pengguna. Contoh: rahasia123
 */
class UpdateStatusLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:baru,diverifikasi,diproses,selesai',
            'catatan' => 'nullable|string',
        ];
    }
}
