<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students')->where('tenant_id', tenant('id')),
            ],
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'height' => 'nullable|numeric|min:0|max:999.99',
            'medical_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'trainer_id' => 'nullable|exists:users,id',
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
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'phone.max' => 'O telefone deve ter no máximo 20 caracteres.',
            'birth_date.date' => 'A data de nascimento deve ser válida.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'gender.in' => 'O gênero deve ser: masculino, feminino ou outro.',
            'height.numeric' => 'A altura deve ser um número.',
            'height.min' => 'A altura deve ser maior que 0.',
            'height.max' => 'A altura deve ser menor que 999.99.',
            'trainer_id.exists' => 'O treinador selecionado não existe.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que trainer_id pertence ao mesmo tenant
            if ($this->filled('trainer_id')) {
                $user = \App\Models\User::find($this->trainer_id);
                if (!$user) {
                    $validator->errors()->add('trainer_id', 'O treinador selecionado não pertence ao seu tenant.');
                }
            }
        });
    }
}
