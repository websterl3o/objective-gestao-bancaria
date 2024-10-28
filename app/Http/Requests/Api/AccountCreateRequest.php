<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AccountCreateRequest extends FormRequest
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
            'uuid' => 'required|string|unique:accounts,uuid',
            'balance' => 'required|numeric|min:0',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'uuid' => (string) $this->numero_conta,
            'balance' => $this->saldo,
        ]);

        $this->getInputSource()->remove('numero_conta');
        $this->getInputSource()->remove('saldo');
    }

    public function messages(): array
    {
        return [
            'uuid.required' => 'O campo número da conta é obrigatório.',
            'uuid.unique' => 'O número da conta já está em uso.',
            'balance.required' => 'O campo saldo é obrigatório.',
            'balance.numeric' => 'O campo saldo deve ser um número.',
            'balance.min' => 'O campo saldo deve ser maior ou igual a 0.',
        ];
    }
}
