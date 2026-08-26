<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\CustomDbRule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Jika ingin, bisa hapus prepareForValidation karena user_id wajib
    // protected function prepareForValidation()
    // {
    //     if ($this->input('user_id') === '') {
    //         $this->merge(['user_id' => null]);
    //     }
    // }

    public function rules(): array
    {
        $id = $this->route('id'); // ID buku jika update, bisa null jika create
        return [
            'user_id' => ['nullable'],
            'no_books' => [
                'required',
                // Validasi unique di koneksi mysql2 dengan ignore ID saat update
                new CustomDbRule('mysql2', 'kmd_books', 'no_books', 'unique', $id),
            ],
            'certificate_id' => ['nullable', 'exists:certificates,id'], // Asumsikan certificates di koneksi default
            'class_id' => [
                'nullable',
                // Validasi exists di koneksi mysql2
                new CustomDbRule('mysql2', 'kmd_classes', 'id', 'exists'),
            ],
            'date_publish' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Pemilik buku wajib dipilih.',
            'user_id.exists' => 'Pemilik buku tidak valid.',
            'no_books.required' => 'Nomor buku wajib diisi.',
            'no_books.unique' => 'Nomor buku sudah digunakan.',
            'certificate_id.exists' => 'Sertifikat tidak valid.',
            'class_id.exists' => 'Kelas tidak valid.',
            'status.in' => 'Status harus active atau inactive.',
        ];
    }
}
