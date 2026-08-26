<?php

namespace App\Http\Requests\Kmd\Superadmin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CustomDbRule;

class KmdCertificateRequest extends FormRequest
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
        'user_id' => ['required', new CustomDbRule('mysql2', 'kmd_users', 'id', 'exists')],
        'no_certificate' => ['required', 'string', 'max:255'],
        'date_publish' => ['required', 'date'],
        'status' => ['required', 'in:active,inactive'],
    ];
}

}
