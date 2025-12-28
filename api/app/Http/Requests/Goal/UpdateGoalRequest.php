<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoalRequest extends FormRequest
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
            'title' => 'sometimes|required|string|min:3|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|in:weight_loss,muscle_gain,performance,other',
            'target_value' => 'nullable|numeric',
            'current_value' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'starts_at' => 'sometimes|required|date',
            'target_date' => 'nullable|date|after:starts_at',
            'completed_at' => 'nullable|date',
            'status' => 'sometimes|in:active,completed,cancelled',
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
            'title.required' => 'O título é obrigatório.',
            'title.min' => 'O título deve ter no mínimo 3 caracteres.',
            'title.max' => 'O título deve ter no máximo 255 caracteres.',
            'type.required' => 'O tipo da meta é obrigatório.',
            'type.in' => 'O tipo deve ser: perda de peso, ganho muscular, performance ou outro.',
            'target_value.numeric' => 'O valor alvo deve ser um número.',
            'current_value.numeric' => 'O valor atual deve ser um número.',
            'unit.max' => 'A unidade deve ter no máximo 50 caracteres.',
            'starts_at.required' => 'A data de início é obrigatória.',
            'starts_at.date' => 'A data de início deve ser válida.',
            'target_date.date' => 'A data alvo deve ser válida.',
            'target_date.after' => 'A data alvo deve ser posterior à data de início.',
            'completed_at.date' => 'A data de conclusão deve ser válida.',
            'status.in' => 'O status deve ser: ativa, concluída ou cancelada.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Se status for 'completed' e completed_at não foi fornecido, setar automaticamente
            if ($this->input('status') === 'completed' && !$this->filled('completed_at')) {
                $this->merge(['completed_at' => now()->format('Y-m-d')]);
            }
        });
    }
}
