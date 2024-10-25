<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TransactionCreateRequest;
use App\Http\Resources\Api\TransactionShowResource;

class TransactionController extends Controller
{
    public function create(TransactionCreateRequest $request)
    {
        $account = Account::where('uuid', $request->uuid)->first();

        if ($account->canAddTransaction($request->value, $request->type_payment)) {
            return response()->json(['message' => 'Saldo insuficiente.'], 400);
        }

        $transaction = $account->createTransaction($request->all());

        return new TransactionShowResource($transaction);
    }
}
