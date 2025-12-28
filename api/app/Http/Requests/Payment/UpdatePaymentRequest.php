<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'amount' => 'sometimes|required|numeric|min:0.01|max:99999999.99',
            'due_date' => 'sometimes|required|date',
            'paid_at' => 'nullable|date',
            'status' => 'sometimes|in:pending,paid,overdue,cancelled',
            'payment_method' => 'nullable|in:cash,pix,credit_card,debit_card,bank_transfer',
            'notes' => 'nullable|string',
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
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.min' => 'O valor deve ser no mínimo R$ 0,01.',
            'amount.max' => 'O valor deve ser no máximo R$ 99.999.999,99.',
            'due_date.required' => 'A data de vencimento é obrigatória.',
            'due_date.date' => 'A data de vencimento deve ser válida.',
            'paid_at.date' => 'A data de pagamento deve ser válida.',
            'status.in' => 'O status deve ser: pendente, pago, atrasado ou cancelado.',
            'payment_method.in' => 'O método de pagamento deve ser: dinheiro, pix, cartão de crédito, cartão de débito ou transferência bancária.',
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
