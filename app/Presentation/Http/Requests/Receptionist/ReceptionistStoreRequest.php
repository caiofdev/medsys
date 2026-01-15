<?php

namespace App\Presentation\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;

class ReceptionistStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'birth_date' => ['required', 'date', 'before:today'],
            'register_number' => ['required', 'string', 'max:20', 'unique:receptionists,registration_number'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
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
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'register_number.required' => 'O número de registro é obrigatório.',
            'register_number.unique' => 'Este número de registro já está cadastrado.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A imagem deve ser nos formatos: jpeg, png, jpg ou gif.',
            'photo.max' => 'A imagem não pode exceder 2MB.',
        ];
    }
}