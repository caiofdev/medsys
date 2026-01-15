<?php

namespace App\Presentation\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class FinishConsultationRequest extends FormRequest
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
            'appointment_id' => ['required', 'exists:appointments,id'],
            'symptoms' => ['required', 'string'],
            'diagnosis' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'appointment_id.required' => 'O agendamento é obrigatório.',
            'appointment_id.exists' => 'O agendamento selecionado não existe.',
            
            'symptoms.required' => 'Os sintomas são obrigatórios.',
            'diagnosis.required' => 'O diagnóstico é obrigatório.',
        ];
    }
}