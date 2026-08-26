<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustomDbRule;

class KmdClassRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', new CustomDbRule('mysql2', 'kmd_classes', 'name', 'exists')],
            'status' => ['required', 'in:active,inactive'],
            // field lain tanpa modul_id tetap bisa divalidasi dengan CustomDbRule
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus bernilai salah satu dari: active atau inactive.',
            'name.required' => 'Nama kelas wajib diisi.',
            'name.string' => 'Nama kelas harus berupa teks.',
            'name.max' => 'Nama kelas maksimal 255 karakter.',
        ];
    }


}
