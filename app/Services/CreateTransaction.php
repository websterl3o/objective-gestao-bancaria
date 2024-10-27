<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\InsufficientBalance;
use App\Http\Requests\Api\TransactionCreateRequest;

class CreateTransaction
{
    protected Account $account;
    protected TransactionCreateRequest $request;

    public function __construct(Account $account, TransactionCreateRequest $request)
    {
        $this->account = $account;
        $this->request = $request;
    }

    public function handle(): Transaction
    {
        DB::beginTransaction();

        $transaction = Transaction::prepareTransaction(
            $this->account->uuid,
            $this->request->type,
            $this->request->type_payment,
            $this->request->value
        );

        if ($this->account->canAddTransaction($transaction->total)) {
            Log::error("Erro na tentativa de criar transação na conta {$this->account->uuid}, por motivo de saldo insuficiente.", [$this->account->toArray(), $transaction->toArray()]);
            throw new InsufficientBalance("Erro na tentativa de criar transação na conta {$this->account->uuid}, por motivo de saldo insuficiente.");
        }

        $transaction->save();
        $transaction->updateBalance();

        DB::commit();

        return $transaction;
    }
}
