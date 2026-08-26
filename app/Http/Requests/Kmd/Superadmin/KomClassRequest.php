<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Kmd\KmdClass;

class KomClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ];

        // For update, exclude current record from unique validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            try {
                // Decrypt the ID from route parameter
                $encryptedId = $this->route('encryptedId') ?? $this->route('id');
                $realId = KmdClass::decryptId($encryptedId);
                $rules['name'][] = 'unique:kmd_classes,name,' . $realId;
            } catch (\Exception $e) {
                // If decryption fails, just add unique rule without exclusion
                $rules['name'][] = 'unique:kmd_classes,name';
            }
        } else {
            $rules['name'][] = 'unique:kmd_classes,name';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kelas wajib diisi.',
            'name.unique' => 'Nama kelas sudah ada.',
            'name.max' => 'Nama kelas maksimal 255 karakter.',
            'name.string' => 'Nama kelas harus berupa teks.',
            'status.required' => 'Status wajib dipilih.',
            'status.boolean' => 'Status harus berupa true atau false.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama kelas',
            'status' => 'status',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convert string status to boolean if needed
        if ($this->has('status')) {
            $status = $this->input('status');
            
            if (is_string($status)) {
                $this->merge([
                    'status' => filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
                ]);
            }
        }

        // Trim whitespace from name
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->input('name'))
            ]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional custom validation if needed
            if ($this->has('name') && strlen(trim($this->input('name'))) === 0) {
                $validator->errors()->add('name', 'Nama kelas tidak boleh kosong atau hanya berisi spasi.');
            }
        });
    }
}
