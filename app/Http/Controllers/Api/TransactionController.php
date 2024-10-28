<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\Transaction;
use App\Services\CreateTransaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TransactionCreateRequest;
use App\Http\Resources\Api\TransactionShowResource;

class TransactionController extends Controller
{
    public function create(TransactionCreateRequest $request)
    {
        $account = Account::where('uuid', $request->uuid)->first();

        $transaction = Transaction::prepareTransaction(
            $account->uuid,
            $request->type,
            $request->type_payment,
            $request->value
        );

        if (! $account->canAddTransaction($transaction->total)) {
            return response()->json(['message' => 'Saldo insuficiente.'], 400);
        }

        $createTransaction = new CreateTransaction($account , $transaction);

        $transaction = $createTransaction->handle();

        return new TransactionShowResource($transaction);
    }
}
