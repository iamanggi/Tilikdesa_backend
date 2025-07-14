<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'village' => 'nullable|string',
            'sub_district' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Custom body parameters for Scribe documentation.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'nama' => [
                'description' => 'Nama lengkap pengguna.',
                'example' => 'Anggi Puspita',
            ],
            'phone' => [
                'description' => 'Nomor telepon pengguna.',
                'example' => '08123456789',
            ],
            'address' => [
                'description' => 'Alamat lengkap pengguna.',
                'example' => 'Jl. Merdeka No. 45',
            ],
            'village' => [
                'description' => 'Nama desa.',
                'example' => 'Desa Sukamaju',
            ],
            'sub_district' => [
                'description' => 'Nama kecamatan.',
                'example' => 'Kecamatan Suka Jadi',
            ],
            'password' => [
                'description' => 'Password baru pengguna (jika ingin mengganti).',
                'example' => 'rahasia123',
            ],
            'password_confirmation' => [
                'description' => 'Konfirmasi password baru.',
                'example' => 'rahasia123',
            ],
            'photo' => [
                'description' => 'File foto profil pengguna.',
                'type' => 'file',
            ],
        ];
    }
}
