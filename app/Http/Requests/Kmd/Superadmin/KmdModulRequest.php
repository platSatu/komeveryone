<?php

namespace App\Http\Requests\Kmd\Superadmin;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Exists;
use App\Rules\CustomDbRule;

class KmdModulRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'order' => ['required', 'integer', 'min:1'],
        'kom_class_id' => [
            'required',
            'uuid',
            new CustomDbRule('mysql2', 'kmd_classes', 'id', 'exists'),
            // Jika Anda memang menggunakan ExistsInMysql2, ganti sesuai nama class Anda
            // new ExistsInMysql2('kmd_classes', 'id'),
        ],
        'status' => ['nullable', 'string', 'in:active,inactive'],
    ];
}


    public function messages(): array
    {
        return [
            'name.required' => 'Judul modul wajib diisi.',
            'name.string' => 'Judul modul harus berupa teks.',
            'name.max' => 'Judul modul tidak boleh lebih dari 255 karakter.',

            'description.string' => 'Deskripsi harus berupa teks.',

            'order.required' => 'Urutan modul wajib diisi.',
            'order.integer' => 'Urutan modul harus berupa angka.',
            'order.min' => 'Urutan modul minimal harus 1.',

            'kom_class_id.required' => 'Kelas KOM wajib dipilih.',
            'kom_class_id.exists' => 'Kelas yang dipilih tidak ditemukan.',

            'status.string' => 'Status harus berupa teks.',
        ];
    }


}
