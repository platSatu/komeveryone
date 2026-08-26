<?php

namespace App\Http\Requests\Kmd\AdminCabang;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustomDbRule;

class AbsensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

//     public function rules(): array
// {
//     return [
//         'absensi' => ['required', 'array', 'min:1'],
//         'absensi.*.userclasstutorial_id' => ['required', 'uuid', new CustomDbRule('mysql', 'kmd_user_class_tutorial', 'id', 'exists')],
//         'absensi.*.user_id' => ['required', 'uuid', new CustomDbRule('mysql', 'kmd_users', 'id', 'exists')],
//         'absensi.*.kehadiran' => ['required', 'in:hadir,tidak_hadir'],
//         'absensi.*.status' => ['required', 'string', 'in:normal,sakit,izin,alpha'],
//     ];
// }
public function rules(): array
{
    return [
        'absensi' => ['required', 'array', 'min:1'],

        'absensi.*.userclasstutorial_id' => [
            'required',
            'uuid',
            'exists:kmd_user_class_tutorial,id',
        ],

        'absensi.*.user_id' => [
            'required',
            'uuid',
            'exists:kmd_users,id',
        ],

        'absensi.*.kehadiran' => [
            'required',
            'in:hadir,tidak_hadir',
        ],

        'absensi.*.status' => [
            'required',
            'in:normal,sakit,izin,alpha',
        ],
    ];
}



    public function messages(): array
    {
        return [
            'absensi.required' => 'Data absensi wajib diisi.',
            'absensi.array' => 'Format data absensi tidak valid.',
            'absensi.min' => 'Minimal harus ada 1 data absensi.',
            'absensi.*.userclasstutorial_id.required' => 'ID kelas tutorial wajib diisi.',
            'absensi.*.userclasstutorial_id.exists' => 'Data kelas tutorial tidak ditemukan.',
            'absensi.*.user_id.required' => 'ID user wajib diisi.',
            'absensi.*.user_id.exists' => 'Data user tidak ditemukan.',
            'absensi.*.kehadiran.required' => 'Status kehadiran wajib dipilih.',
            'absensi.*.kehadiran.in' => 'Status kehadiran harus hadir atau tidak hadir.',
            'absensi.*.status.required' => 'Keterangan status wajib dipilih.',
            'absensi.*.status.in' => 'Keterangan status tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'absensi.*.kehadiran' => 'status kehadiran',
            'absensi.*.status' => 'keterangan',
        ];
    }
}
