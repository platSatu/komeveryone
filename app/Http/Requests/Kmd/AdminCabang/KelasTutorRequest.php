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
    /** kom_v02: pilihan "Akhir Modul" dikirim sebagai string kosong → simpan NULL */
    protected function prepareForValidation(): void
    {
        $this->merge(['video_id' => $this->filled('video_id') ? $this->input('video_id') : null]);
    }

    public function rules(): array
    {
        return [
            'pengajar_id' => 'required|string',
            'class_id' => 'required|string',
            'modul_id' => 'required|string',
            // kom_v02: titik tutorial. Kosong = Akhir Modul; diisi = setelah video (tengah modul)
            'video_id' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $ok = \App\Models\Kmd\KmdVideo::where('id', $value)
                        ->where('modul_id', $this->input('modul_id'))
                        ->where('has_tutorial', true)
                        ->exists();
                    if (! $ok) {
                        $fail(__('Titik kelas tutorial tidak sesuai dengan modul yang dipilih.'));
                    }
                },
            ],

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
