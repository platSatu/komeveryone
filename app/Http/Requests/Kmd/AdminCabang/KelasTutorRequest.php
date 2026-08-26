<?php

namespace App\Http\Requests\Kmd\AdminCabang;

use Illuminate\Foundation\Http\FormRequest;

class KelasTutorRequest extends FormRequest
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
            'pengajar_id' => 'required|string',
            'class_id' => 'required|string',
            'modul_id' => 'required|string',

            'topic' => 'nullable|string',
            'start_time' => 'required|date',
            'duration' => 'nullable|integer|min:1',

            'quota' => 'required|integer|min:1',
            'jml_ikut' => 'nullable|integer|min:0',
            'sisa_quota' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|string',
            
            'rayon_id' => 'nullable|uuid',
            'notifikasi' => 'nullable|boolean',
        ];
    }
}
