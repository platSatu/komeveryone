<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;

class KmdQuestionRequest extends FormRequest
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
    public function rules()
{
    if ($this->isMethod('post')) {
        // Validasi untuk store (create) - multiple questions (array)
        return [
            'questions' => ['required', 'array', 'min:1'],
            'questions.*' => ['required', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ];
    } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
        // Validasi untuk update (edit) - single question (string)
        return [
            'questions' => ['required', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    // Default fallback (misal GET request)
    return [];
}


public function messages()
{
    return [
        'questions.required' => 'Pertanyaan wajib diisi.',
        'questions.array' => 'Pertanyaan harus berupa array.',
        'questions.*.required' => 'Setiap pertanyaan wajib diisi.',
        'questions.*.string' => 'Setiap pertanyaan harus berupa teks.',
        'questions.*.max' => 'Pertanyaan tidak boleh lebih dari 1000 karakter.',
        'questions.string' => 'Pertanyaan harus berupa teks.',
        'questions.max' => 'Pertanyaan tidak boleh lebih dari 1000 karakter.',

        'status.required' => 'Status wajib dipilih.',
        'status.in' => 'Status harus bernilai "active" atau "inactive".',
    ];
}


}
