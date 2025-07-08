<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam email string required Alamat email pengguna. Contoh: john@example.com
 * @bodyParam password string required Kata sandi pengguna. Contoh: rahasia123
 */
class StoreLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id' => 'required|exists:kategoris,id',
            'deskripsi' => 'required|string|min:10',
            'alamat' => 'required|string|min:5',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
