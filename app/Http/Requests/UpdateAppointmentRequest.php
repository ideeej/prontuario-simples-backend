<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'patient_id' => 'nullable|exists:patients,id',
            'therapy_session_id' => 'nullable|exists:therapy_sessions,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable',
            'notes' => 'nullable',
        ];
    }
}
