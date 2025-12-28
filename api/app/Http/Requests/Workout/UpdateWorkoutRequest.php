<?php

namespace App\Http\Requests\Workout;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkoutRequest extends FormRequest
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
            'student_id' => 'sometimes|required|exists:students,id',
            'name' => 'sometimes|required|string|min:3|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|in:strength,cardio,flexibility,mixed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'sometimes|boolean',
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
            'student_id.required' => 'O aluno é obrigatório.',
            'student_id.exists' => 'O aluno selecionado não existe.',
            'name.required' => 'O nome do treino é obrigatório.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'category.required' => 'A categoria é obrigatória.',
            'category.in' => 'A categoria deve ser: força, cardio, flexibilidade ou misto.',
            'starts_at.date' => 'A data de início deve ser válida.',
            'ends_at.date' => 'A data de término deve ser válida.',
            'ends_at.after' => 'A data de término deve ser posterior à data de início.',
            'is_active.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que student_id pertence ao mesmo tenant
            if ($this->filled('student_id')) {
                $student = \App\Models\Student::find($this->student_id);
                if (!$student) {
                    $validator->errors()->add('student_id', 'O aluno selecionado não pertence ao seu tenant.');
                }
            }
        });
    }
}
