<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use App\Exceptions\InsufficientBalance;
use App\Http\Requests\Api\TransactionCreateRequest;

class CreateTransaction
{
    protected Account $account;
    protected TransactionCreateRequest $request;
    protected array $data;

    public function __construct(Account $account, TransactionCreateRequest $request)
    {
        $this->account = $account;
        $this->request = $request;
    }

    public function handle(): Transaction
    {
        $this->fillData();

        if ($this->account->canAddTransaction($this->request->value, $this->request->type_payment)) {
            Log::error("Erro na tentativa de criar transação na conta {$this->account->uuid}, por motivo de saldo insuficiente.", [$this->account->toArray(), $this->data]);
            throw new InsufficientBalance("Erro na tentativa de criar transação na conta {$this->account->uuid}, por motivo de saldo insuficiente.");
        }

        $fee = Transaction::OPERATION_FEE[$this->request->type_payment];

        $this->data['fee'] = Transaction::operationFee($fee, $this->data['value']);
        $this->data['total'] = $this->data['value'] + $this->data['fee'];

        $transaction = Transaction::create($this->data);
        $transaction->updateBalance();

        return $transaction;
    }

    protected function fillData(): void
    {
        $this->data = $this->request->all();
    }
}
