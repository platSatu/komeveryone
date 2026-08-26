<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustomDbRule;

class ClassTutorialRequest extends FormRequest
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
        'user_id' => ['required', new CustomDbRule('mysql2', 'kmd_users', 'id', 'exists')],
        'pengajar_id' => ['required', new CustomDbRule('mysql2', 'kmd_users', 'id', 'exists')],
        'class_id' => ['required', new CustomDbRule('mysql2', 'kmd_classes', 'id', 'exists')],
        'modul_id' => ['required', new CustomDbRule('mysql2', 'kmd_moduls', 'id', 'exists')],
        'quota' => ['required', 'integer', 'min:1'],
        'description' => ['nullable', 'string', 'max:1000'],
        'status' => ['required', 'in:active,inactive'],
        'link_zoom' => ['nullable', 'url'],
    ];
}

    public function messages(): array
    {
        return [
            'user_id.required' => 'Peserta harus dipilih.',
            'pengajar_id.required' => 'Pengajar harus dipilih.',
            'class_id.required' => 'Kelas harus dipilih.',
            'modul_id.required' => 'Modul harus dipilih.',

            'quota.required' => 'Kuota wajib diisi.',
            'quota.integer' => 'Kuota harus berupa angka.',
            'status.in' => 'Status harus active atau inactive.',
            'link_zoom.url' => 'Link Zoom harus berupa URL yang valid.',
        ];
    }
}
