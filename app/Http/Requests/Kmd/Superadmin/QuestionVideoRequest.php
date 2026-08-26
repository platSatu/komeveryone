<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustomDbRule;

class QuestionVideoRequest extends FormRequest
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
            'videos_id' => ['required', 'array', 'min:1'],
            'videos_id.*' => ['required', new CustomDbRule('mysql2', 'kmd_videos', 'id', 'exists')],
            'questions_id' => ['required', 'array', 'min:1'],
            'questions_id.*' => ['required', new CustomDbRule('mysql2', 'kmd_questions', 'id', 'exists')],
            'status' => ['required', 'array', 'min:1'],
            'status.*' => ['required', 'in:active,inactive,pending'],
        ];
    }


}
