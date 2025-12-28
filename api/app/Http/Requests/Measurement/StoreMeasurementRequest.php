<?php

namespace App\Http\Requests\Measurement;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeasurementRequest extends FormRequest
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
            'weight' => 'required|numeric|min:1|max:999.99',
            'body_fat' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0|max:999.99',
            'chest' => 'nullable|numeric|min:0|max:999.99',
            'waist' => 'nullable|numeric|min:0|max:999.99',
            'hips' => 'nullable|numeric|min:0|max:999.99',
            'right_arm' => 'nullable|numeric|min:0|max:999.99',
            'left_arm' => 'nullable|numeric|min:0|max:999.99',
            'right_thigh' => 'nullable|numeric|min:0|max:999.99',
            'left_thigh' => 'nullable|numeric|min:0|max:999.99',
            'right_calf' => 'nullable|numeric|min:0|max:999.99',
            'left_calf' => 'nullable|numeric|min:0|max:999.99',
            'notes' => 'nullable|string',
            'measured_at' => 'required|date',
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
            'weight.required' => 'O peso é obrigatório.',
            'weight.numeric' => 'O peso deve ser um número.',
            'weight.min' => 'O peso deve ser no mínimo 1 kg.',
            'weight.max' => 'O peso deve ser no máximo 999.99 kg.',
            'body_fat.numeric' => 'O percentual de gordura deve ser um número.',
            'body_fat.min' => 'O percentual de gordura deve ser no mínimo 0%.',
            'body_fat.max' => 'O percentual de gordura deve ser no máximo 100%.',
            'muscle_mass.numeric' => 'A massa muscular deve ser um número.',
            'muscle_mass.min' => 'A massa muscular deve ser no mínimo 0.',
            'muscle_mass.max' => 'A massa muscular deve ser no máximo 999.99.',
            'chest.numeric' => 'A medida do peito deve ser um número.',
            'chest.min' => 'A medida do peito deve ser no mínimo 0.',
            'chest.max' => 'A medida do peito deve ser no máximo 999.99.',
            'waist.numeric' => 'A medida da cintura deve ser um número.',
            'waist.min' => 'A medida da cintura deve ser no mínimo 0.',
            'waist.max' => 'A medida da cintura deve ser no máximo 999.99.',
            'hips.numeric' => 'A medida do quadril deve ser um número.',
            'hips.min' => 'A medida do quadril deve ser no mínimo 0.',
            'hips.max' => 'A medida do quadril deve ser no máximo 999.99.',
            'right_arm.numeric' => 'A medida do braço direito deve ser um número.',
            'right_arm.min' => 'A medida do braço direito deve ser no mínimo 0.',
            'right_arm.max' => 'A medida do braço direito deve ser no máximo 999.99.',
            'left_arm.numeric' => 'A medida do braço esquerdo deve ser um número.',
            'left_arm.min' => 'A medida do braço esquerdo deve ser no mínimo 0.',
            'left_arm.max' => 'A medida do braço esquerdo deve ser no máximo 999.99.',
            'right_thigh.numeric' => 'A medida da coxa direita deve ser um número.',
            'right_thigh.min' => 'A medida da coxa direita deve ser no mínimo 0.',
            'right_thigh.max' => 'A medida da coxa direita deve ser no máximo 999.99.',
            'left_thigh.numeric' => 'A medida da coxa esquerda deve ser um número.',
            'left_thigh.min' => 'A medida da coxa esquerda deve ser no mínimo 0.',
            'left_thigh.max' => 'A medida da coxa esquerda deve ser no máximo 999.99.',
            'right_calf.numeric' => 'A medida da panturrilha direita deve ser um número.',
            'right_calf.min' => 'A medida da panturrilha direita deve ser no mínimo 0.',
            'right_calf.max' => 'A medida da panturrilha direita deve ser no máximo 999.99.',
            'left_calf.numeric' => 'A medida da panturrilha esquerda deve ser um número.',
            'left_calf.min' => 'A medida da panturrilha esquerda deve ser no mínimo 0.',
            'left_calf.max' => 'A medida da panturrilha esquerda deve ser no máximo 999.99.',
            'measured_at.required' => 'A data da medição é obrigatória.',
            'measured_at.date' => 'A data da medição deve ser válida.',
        ];
    }
}
