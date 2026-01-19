<?php

namespace App\Presentation\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patientId = $this->route('patient');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('patients', 'email')->ignore($patientId)],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'medical_history' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'phone.required' => 'O telefone é obrigatório.',
            'gender.in' => 'O gênero deve ser: masculino, feminino ou outro.',
        ];
    }
}