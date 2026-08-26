<?php

namespace App\Http\Requests\Kmd\AdminCabang;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustomDbRule;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'reviews' => ['required', 'array', 'min:1'],
        'reviews.*.userclasstutorial_id' => [
            'required',
            'string',
            new CustomDbRule('mysql', 'kmd_user_class_tutorial', 'id', 'exists'),
        ],
        'reviews.*.user_id' => [
            'required',
            'string',
            new CustomDbRule('mysql', 'users', 'id', 'exists'),
        ],
        'reviews.*.pengajar_id' => [
            'required',
            'string',
            new CustomDbRule('mysql', 'users', 'id', 'exists'),
        ],
        'reviews.*.isi_pengajaran' => [
            'required',
            'string',
            'in:sangat_baik,baik,cukup,kurang,sangat_kurang',
        ],
        'reviews.*.kebenaran_teologis' => [
            'required',
            'string',
            'in:sangat_baik,baik,cukup,kurang,sangat_kurang',
        ],
        'reviews.*.pengajar_sampaikan_seluruh_materi' => [
            'required',
            'string',
            'in:ya,tidak,sebagian',
        ],
        'reviews.*.pengajar_sampaikan_materi_utama' => [
            'required',
            'string',
            'in:ya,tidak,sebagian',
        ],
        'reviews.*.pembukaan_materi' => [
            'required',
            'string',
            'in:sangat_baik,baik,cukup,kurang,sangat_kurang',
        ],
        'reviews.*.kesinambungan_materi' => [
            'required',
            'string',
            'in:sangat_baik,baik,cukup,kurang,sangat_kurang',
        ],
        'reviews.*.kesaksian_pribadi' => [
            'required',
            'string',
            'in:sangat_baik,baik,cukup,kurang,sangat_kurang',
        ],
        'reviews.*.materi_dipahami' => [
            'required',
            'string',
            'in:sangat_mudah,mudah,cukup,sulit,sangat_sulit',
        ],
        'reviews.*.sesuai_dengan_materi' => [
            'required',
            'string',
            'in:sangat_sesuai,sesuai,cukup_sesuai,kurang_sesuai,tidak_sesuai',
        ],
    ];
}

    public function messages(): array
    {
        return [
            'reviews.required' => 'Data review wajib diisi.',
            'reviews.array' => 'Format data review tidak valid.',
            'reviews.min' => 'Minimal harus ada 1 data review.',
            
            // User Class Tutorial ID
            'reviews.*.userclasstutorial_id.required' => 'ID kelas tutorial wajib diisi.',
            'reviews.*.userclasstutorial_id.exists' => 'Data kelas tutorial tidak ditemukan.',
            
            // User ID
            'reviews.*.user_id.required' => 'ID user wajib diisi.',
            'reviews.*.user_id.exists' => 'Data user tidak ditemukan.',
            
            // Pengajar ID
            'reviews.*.pengajar_id.required' => 'ID pengajar wajib diisi.',
            'reviews.*.pengajar_id.exists' => 'Data pengajar tidak ditemukan.',
            
            // Isi Pengajaran
            'reviews.*.isi_pengajaran.required' => 'Penilaian isi pengajaran wajib dipilih.',
            'reviews.*.isi_pengajaran.in' => 'Penilaian isi pengajaran tidak valid.',
            
            // Kebenaran Teologis
            'reviews.*.kebenaran_teologis.required' => 'Penilaian kebenaran teologis wajib dipilih.',
            'reviews.*.kebenaran_teologis.in' => 'Penilaian kebenaran teologis tidak valid.',
            
            // Pengajar Sampaikan Seluruh Materi
            'reviews.*.pengajar_sampaikan_seluruh_materi.required' => 'Penilaian penyampaian seluruh materi wajib dipilih.',
            'reviews.*.pengajar_sampaikan_seluruh_materi.in' => 'Penilaian penyampaian seluruh materi tidak valid.',
            
            // Pengajar Sampaikan Materi Utama
            'reviews.*.pengajar_sampaikan_materi_utama.required' => 'Penilaian penyampaian materi utama wajib dipilih.',
            'reviews.*.pengajar_sampaikan_materi_utama.in' => 'Penilaian penyampaian materi utama tidak valid.',
            
            // Pembukaan Materi
            'reviews.*.pembukaan_materi.required' => 'Penilaian pembukaan materi wajib dipilih.',
            'reviews.*.pembukaan_materi.in' => 'Penilaian pembukaan materi tidak valid.',
            
            // Kesinambungan Materi
            'reviews.*.kesinambungan_materi.required' => 'Penilaian kesinambungan materi wajib dipilih.',
            'reviews.*.kesinambungan_materi.in' => 'Penilaian kesinambungan materi tidak valid.',
            
            // Kesaksian Pribadi
            'reviews.*.kesaksian_pribadi.required' => 'Penilaian kesaksian pribadi wajib dipilih.',
            'reviews.*.kesaksian_pribadi.in' => 'Penilaian kesaksian pribadi tidak valid.',
            
            // Materi Dipahami
            'reviews.*.materi_dipahami.required' => 'Penilaian pemahaman materi wajib dipilih.',
            'reviews.*.materi_dipahami.in' => 'Penilaian pemahaman materi tidak valid.',
            
            // Sesuai Dengan Materi
            'reviews.*.sesuai_dengan_materi.required' => 'Penilaian kesesuaian materi wajib dipilih.',
            'reviews.*.sesuai_dengan_materi.in' => 'Penilaian kesesuaian materi tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'reviews.*.isi_pengajaran' => 'isi pengajaran',
            'reviews.*.kebenaran_teologis' => 'kebenaran teologis',
            'reviews.*.pengajar_sampaikan_seluruh_materi' => 'penyampaian seluruh materi',
            'reviews.*.pengajar_sampaikan_materi_utama' => 'penyampaian materi utama',
            'reviews.*.pembukaan_materi' => 'pembukaan materi',
            'reviews.*.kesinambungan_materi' => 'kesinambungan materi',
            'reviews.*.kesaksian_pribadi' => 'kesaksian pribadi',
            'reviews.*.materi_dipahami' => 'pemahaman materi',
            'reviews.*.sesuai_dengan_materi' => 'kesesuaian materi',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Clean up data if needed
        if ($this->has('reviews')) {
            $reviews = $this->input('reviews');
            
            foreach ($reviews as $key => $review) {
                // Trim string values
                foreach ($review as $field => $value) {
                    if (is_string($value)) {
                        $reviews[$key][$field] = trim($value);
                    }
                }
            }
            
            $this->merge(['reviews' => $reviews]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional custom validation if needed
            if ($this->has('reviews')) {
                foreach ($this->input('reviews') as $index => $review) {
                    // Check if all required fields are not empty after trimming
                    $requiredFields = [
                        'isi_pengajaran',
                        'kebenaran_teologis',
                        'pengajar_sampaikan_seluruh_materi',
                        'pengajar_sampaikan_materi_utama',
                        'pembukaan_materi',
                        'kesinambungan_materi',
                        'kesaksian_pribadi',
                        'materi_dipahami',
                        'sesuai_dengan_materi'
                    ];
                    
                    foreach ($requiredFields as $field) {
                        if (empty(trim($review[$field] ?? ''))) {
                            $validator->errors()->add(
                                "reviews.{$index}.{$field}",
                                "Field {$field} tidak boleh kosong."
                            );
                        }
                    }
                }
            }
        });
    }
}
