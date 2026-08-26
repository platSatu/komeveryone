<?php

namespace App\Http\Requests\Kmd\AdminCabang;

use Illuminate\Foundation\Http\FormRequest;

class AbsensiRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk penyimpanan absensi kelas tutorial.
     *
     * Form absensi mengirim data dalam bentuk array asosiatif per peserta:
     * absensi[{userClassTutorialId}][userclasstutorial_id]
     * absensi[{userClassTutorialId}][user_id]
     * absensi[{userClassTutorialId}][kehadiran]
     * absensi[{userClassTutorialId}][status]
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'absensi' => ['required', 'array', 'min:1'],
            'absensi.*.userclasstutorial_id' => ['required', 'string'],
            'absensi.*.user_id' => ['required', 'string'],
            'absensi.*.kehadiran' => ['required', 'string', 'in:hadir,tidak_hadir'],
            'absensi.*.status' => ['required', 'string', 'in:normal,sakit,izin,alpha'],
        ];
    }

    /**
     * Pesan error kustom berbahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'absensi.required' => 'Data absensi tidak boleh kosong.',
            'absensi.array' => 'Format data absensi tidak valid.',
            'absensi.*.userclasstutorial_id.required' => 'Data peserta tidak lengkap.',
            'absensi.*.user_id.required' => 'Data peserta tidak lengkap.',
            'absensi.*.kehadiran.required' => 'Status kehadiran wajib dipilih untuk setiap peserta.',
            'absensi.*.kehadiran.in' => 'Status kehadiran tidak valid.',
            'absensi.*.status.required' => 'Keterangan absensi wajib dipilih untuk setiap peserta.',
            'absensi.*.status.in' => 'Keterangan absensi tidak valid.',
        ];
    }
}
