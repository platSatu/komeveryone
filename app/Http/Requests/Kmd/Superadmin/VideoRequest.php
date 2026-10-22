<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustomDbRule;

class VideoRequest extends FormRequest
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
        // kom_v02: format jam:menit:detik (mis. 00:18:46) — dipakai untuk cek "ditonton sampai habis"
        'durations' => ['nullable', 'string', 'max:50', 'regex:/^(\d{1,2}:)?\d{1,2}:\d{2}$/'],
        'sort_video' => ['nullable', 'integer'],
        'url_video' => ['nullable', 'url'],
        'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,wmv,flv,mkv'], // sesuaikan mime types jika perlu
        'modul_id' => ['required', new CustomDbRule('mysql2', 'kmd_moduls', 'id', 'exists')],
        'status' => ['required', 'in:active,inactive'],
        'has_tutorial' => ['nullable', 'boolean'], // kom_v02: kelas tutorial setelah video ini
    ];
}

    public function messages(): array
    {
        return [
            'name.required' => 'Nama video wajib diisi.',
            'modul_id.required' => 'Modul wajib dipilih.',
            'status.in' => 'Status harus active atau inactive.',
            'durations.regex' => 'Format durasi harus jam:menit:detik, contoh 00:18:46.',
        ];
    }
}
