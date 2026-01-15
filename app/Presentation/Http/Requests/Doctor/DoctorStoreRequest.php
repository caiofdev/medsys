<?php

namespace App\Presentation\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class DoctorStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf'],
            'rg' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'crm' => ['required', 'string', 'unique:doctors,crm', 'max:20'],
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
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser um endereço válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter exatamente 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A foto deve ser um arquivo do tipo: jpeg, png, jpg, gif.',
            'photo.max' => 'A foto não pode ser maior que 2MB.',
            
            'crm.required' => 'O CRM é obrigatório.',
            'crm.unique' => 'Este CRM já está cadastrado.',
            
            'birth_date.date' => 'A data de nascimento deve ser uma data válida.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
        ];
    }
}