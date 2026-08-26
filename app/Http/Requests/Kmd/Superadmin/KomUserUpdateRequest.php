<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use App\Rules\CustomDbRule;

class KomUserUpdateRequest extends FormRequest
{
    /**
     * Authorize the request.
     */
    public function authorize(): bool
    {
        // Ganti dengan logic otorisasi sesuai kebutuhan jika perlu
        return true;
    }

    /**
     * Validation rules for updating a user.
     */
    public function rules(): array
    {
        $id = null;

        // Ambil id yang didekripsi dari route
        if ($this->route('id')) {
            try {
                $id = Crypt::decrypt($this->route('id'));
            } catch (\Exception $e) {
                $id = null;
            }
        }

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                // CustomDbRule -> cek unique pada koneksi mysql2, tabel kmd_users, kolom email
                new CustomDbRule('mysql2', 'kmd_users', 'email', 'unique', $id),
            ],

            // Jika password diisi, validasi min & confirmed
            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],

            'role' => ['sometimes', 'string', 'in:peserta,pengajar,admin_cabang,admin_kom,superadmin'],

            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],

            'asal_gereja' => ['sometimes', 'nullable', 'string', 'max:255'],

            'tahun_lahir' => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:' . date('Y')],

            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            'status' => ['sometimes', 'nullable', 'in:active,inactive'],

            'cabang_id' => [
                'sometimes',
                'nullable',
                new CustomDbRule('mysql2', 'kmd_cabangs', 'id', 'exists'),
            ],

        ];
    }


    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.in' => 'Peran yang dipilih tidak valid.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'asal_gereja.max' => 'Asal gereja maksimal 255 karakter.',
            'tahun_lahir.integer' => 'Tahun lahir harus berupa angka.',
            'tahun_lahir.min' => 'Tahun lahir tidak valid.',
            'tahun_lahir.max' => 'Tahun lahir tidak boleh lebih dari tahun sekarang.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'status.in' => 'Status harus active atau inactive.',
            'cabang_id.exists' => 'Rayon tidak ditemukan.',
        ];
    }
}
