<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustomDbRule;

class KmdAnswerRequest extends FormRequest
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
        // Validasi untuk create (multiple answers)
        return [
            'questions_id' => 'required|array|min:1',
            'questions_id.*' => ['required', new CustomDbRule('mysql2', 'kmd_questions', 'id', 'exists')],
            'answer' => 'required|array|min:1',
            'answer.*' => 'required|string',
            'is_true' => 'required|array',
            'is_true.*' => 'boolean',
            'status' => 'required|array',
            'status.*' => 'in:active,inactive',
        ];
    } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
        // Validasi untuk edit (single answer)
        return [
            'questions_id' => ['required', new CustomDbRule('mysql2', 'kmd_questions', 'id', 'exists')],
            'answer' => 'required|string',
            'is_true' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ];
    }

    // Default fallback (misal GET request)
    return [];
}




    public function messages(): array
    {
        return [
            'questions_id.required' => 'Pertanyaan harus dipilih.',
            'questions_id.exists' => 'Pertanyaan tidak ditemukan.',
            'answer.required' => 'Jawaban tidak boleh kosong.',
            'answer.max' => 'Jawaban maksimal 1000 karakter.',
            'is_true.required' => 'Status kebenaran wajib diisi.',
            'is_true.boolean' => 'Nilai is_true harus berupa true atau false.',
            'status.required' => 'Status harus diisi.',
            'status.in' => 'Status harus bernilai active atau inactive.',
        ];
    }

}
