<?php

namespace App\Http\Requests\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseRequest extends FormRequest
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
            'name' => 'sometimes|required|string|min:3|max:255',
            'muscle_group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url|max:255',
            'sets' => 'sometimes|required|integer|min:1|max:20',
            'reps' => 'sometimes|required|string|max:50',
            'rest' => 'sometimes|required|string|max:50',
            'load' => 'nullable|string|max:50',
            'tempo' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'order' => 'sometimes|integer|min:0',
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
            'name.required' => 'O nome do exercício é obrigatório.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'muscle_group.max' => 'O grupo muscular deve ter no máximo 255 caracteres.',
            'video_url.url' => 'A URL do vídeo deve ser válida.',
            'video_url.max' => 'A URL do vídeo deve ter no máximo 255 caracteres.',
            'sets.required' => 'O número de séries é obrigatório.',
            'sets.integer' => 'O número de séries deve ser um número inteiro.',
            'sets.min' => 'O número de séries deve ser no mínimo 1.',
            'sets.max' => 'O número de séries deve ser no máximo 20.',
            'reps.required' => 'O número de repetições é obrigatório.',
            'reps.max' => 'O número de repetições deve ter no máximo 50 caracteres.',
            'rest.required' => 'O tempo de descanso é obrigatório.',
            'rest.max' => 'O tempo de descanso deve ter no máximo 50 caracteres.',
            'load.max' => 'A carga deve ter no máximo 50 caracteres.',
            'tempo.max' => 'O tempo deve ter no máximo 50 caracteres.',
            'order.integer' => 'A ordem deve ser um número inteiro.',
            'order.min' => 'A ordem deve ser maior ou igual a 0.',
        ];
    }
}
