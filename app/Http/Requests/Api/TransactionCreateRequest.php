<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TransactionCreateRequest extends FormRequest
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
            'uuid' => 'required|exists:accounts,uuid',
            'type' => 'required|in:debit,credit',
            'type_payment' => 'required|in:P,C,D',
            'value' => 'required|numeric',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'uuid' => (string) $this->numero_conta,
            'type' => 'debit',
            'type_payment' => (string) $this->forma_pagamento,
            'value' => $this->valor,
        ]);

        $this->getInputSource()->remove('numero_conta');
        $this->getInputSource()->remove('forma_pagamento');
        $this->getInputSource()->remove('valor');
    }

    public function messages(): array
    {
        return [
            'uuid.required' => 'O campo número da conta é obrigatório.',
            'uuid.exists' => 'Conta não encontrada.',
            'type.required' => 'O campo tipo é obrigatório.',
            'type.in' => 'O campo tipo deve ser debit ou credit.',
            'type_payment.required' => 'O campo forma de pagamento é obrigatório.',
            'type_payment.in' => 'O campo forma de pagamento deve ser P (Pix), C (Cartão de Credito) ou D (Cartão de Debito).',
            'balance.required' => 'O campo saldo é obrigatório.',
            'balance.numeric' => 'O campo saldo deve ser um número.',
        ];
    }
}
