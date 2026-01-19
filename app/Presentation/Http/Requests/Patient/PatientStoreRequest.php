<?php

namespace App\Presentation\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class PatientStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:patients,email', 'max:255'],
            'cpf' => ['required', 'string', 'size:11', 'unique:patients,cpf'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female,other'],
            'birth_date' => ['required', 'date', 'before:today'],
            'emergency_contact' => ['required', 'string', 'max:20'],
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
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'phone.required' => 'O telefone é obrigatório.',
            'gender.required' => 'O gênero é obrigatório.',
            'gender.in' => 'O gênero deve ser: masculino, feminino ou outro.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'emergency_contact.required' => 'O contato de emergência é obrigatório.',
        ];
    }
}