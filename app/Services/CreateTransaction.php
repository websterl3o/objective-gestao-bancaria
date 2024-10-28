<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InsufficientBalance;

class CreateTransaction
{
    protected Account $account;
    protected Transaction $transaction;

    public function __construct(Account $account, Transaction $transaction)
    {
        $this->account = $account;
        $this->transaction = $transaction;
    }

    public function handle(): Transaction
    {
        DB::beginTransaction();

        if (! $this->account->canAddTransaction($this->transaction->total)) {
            throw new InsufficientBalance("Erro na tentativa de criar transação na conta {$this->account->uuid}, por motivo de saldo insuficiente.");
        }

        $this->transaction->save();
        $this->transaction->updateBalance();

        DB::commit();

        return $this->transaction;
    }
}
