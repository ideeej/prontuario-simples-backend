<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
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
            'name' => 'nullable',
            'username' => 'nullable',
            'email' => 'nullable|email',
            'phone_number' => 'nullable',
            'birth_date' => 'nullable|date',
            'address' => 'nullable',
            'document' => 'nullable',
            'notes' => 'nullable',
        ];
    }
}
