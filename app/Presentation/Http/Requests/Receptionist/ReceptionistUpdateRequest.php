<?php

namespace App\Presentation\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReceptionistUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $receptionist = $this->route('receptionist');
        $userId = $receptionist->user_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['required', 'string', 'max:20'],
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
            'phone.required' => 'O telefone é obrigatório.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A imagem deve ser nos formatos: jpeg, png, jpg ou gif.',
            'photo.max' => 'A imagem não pode exceder 2MB.',
        ];
    }
}