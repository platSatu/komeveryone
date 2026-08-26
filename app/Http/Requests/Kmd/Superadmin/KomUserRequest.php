<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use App\Rules\CustomDbRule;

class KomUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $id = null;

        if ($isUpdate && $this->route('id')) {
            try {
                $id = Crypt::decrypt($this->route('id'));
            } catch (\Exception $e) {
                $id = null;
            }
        }

        return [
            'users' => $isUpdate ? ['nullable', 'array'] : ['required', 'array', 'min:1'],

            'users.*.name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],

            'users.*.email' => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                new CustomDbRule('mysql2', 'kmd_users', 'email', 'unique', $id),
            ],

            'users.*.password' => [
                $isUpdate ? 'nullable' : 'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'users.*.role' => [$isUpdate ? 'sometimes' : 'required', 'string', 'in:peserta,pengajar,admin_cabang,superadmin'],

            'users.*.phone' => ['nullable', 'string', 'max:20'],

            'users.*.asal_gereja' => ['nullable', 'string', 'max:255'],

            'users.*.tahun_lahir' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],

            'users.*.image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            'users.*.status' => ['nullable', 'in:active,inactive'],

            'users.*.cabang_id' => [
                $isUpdate ? 'sometimes' : 'nullable',
                new CustomDbRule('mysql2', 'kmd_cabangs', 'id', 'exists'),
            ],
        ];
    }



    public function messages(): array
    {
        return [
            'users.required' => 'Data pengguna wajib diisi.',
            'users.array' => 'Data pengguna harus berupa array.',
            'users.min' => 'Minimal harus ada satu pengguna.',

            'users.*.name.required' => 'Nama wajib diisi.',
            'users.*.name.max' => 'Nama maksimal 255 karakter.',

            'users.*.email.required' => 'Email wajib diisi.',
            'users.*.email.email' => 'Format email tidak valid.',
            'users.*.email.unique' => 'Email sudah digunakan oleh pengguna lain.',

            'users.*.password.required' => 'Password wajib diisi.',
            'users.*.password.min' => 'Password minimal 6 karakter.',
            'users.*.password.confirmed' => 'Konfirmasi password tidak cocok.',

            'users.*.role.required' => 'Peran wajib dipilih.',
            'users.*.role.in' => 'Peran yang dipilih tidak valid.',

            'users.*.phone.max' => 'Nomor telepon maksimal 20 karakter.',

            'users.*.asal_gereja.max' => 'Asal gereja maksimal 255 karakter.',

            'users.*.tahun_lahir.integer' => 'Tahun lahir harus berupa angka.',
            'users.*.tahun_lahir.min' => 'Tahun lahir tidak valid.',
            'users.*.tahun_lahir.max' => 'Tahun lahir tidak boleh lebih dari tahun sekarang.',

            'users.*.image.image' => 'File harus berupa gambar.',
            'users.*.image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'users.*.image.max' => 'Ukuran gambar maksimal 2MB.',

            'users.*.status.in' => 'Status harus active atau inactive.',

            // 'users.*.cabang_id.required' => 'Rayon wajib dipilih.',
            // 'users.*.cabang_id.exists' => 'Rayon tidak ditemukan.',
        ];
    }

    // Jika kamu masih mau validasi konfirmasi password manual, bisa dipertahankan,
    // tapi Laravel sudah otomatis validasi 'confirmed' untuk nested array ini
    // Kalau mau tetap pakai, ini contoh cara pakai:
    /*
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $users = $this->input('users', []);
            foreach ($users as $index => $user) {
                if (isset($user['password']) && isset($user['password_confirmation'])) {
                    if ($user['password'] !== $user['password_confirmation']) {
                        $validator->errors()->add("users.$index.password_confirmation", 'Konfirmasi password tidak cocok.');
                    }
                }
            }
        });
    }
    */
}
