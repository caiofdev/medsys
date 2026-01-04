<?php

namespace App\Presentation\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUpdateRequest extends FormRequest
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
        $adminId = $this->route('admin');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->admin->user_id ?? null)
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'cpf' => [
                'required',
                'string',
                'size:11',
                Rule::unique('users', 'cpf')->ignore($this->admin->user_id ?? null)
            ],
            'rg' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'is_master' => ['required', 'in:yes,no'],
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
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.max' => 'A imagem não pode ser maior que 2MB.',
            'is_master.required' => 'É necessário definir se é administrador master.',
            'is_master.in' => 'O valor deve ser "sim" ou "não".',
        ];
    }
}
