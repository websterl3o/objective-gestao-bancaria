<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Services\CreateTransaction;
use App\Exceptions\InsufficientBalance;
use App\Http\Requests\Api\TransactionCreateRequest;

test('it_creates_transaction_successfully_when_account_has_sufficient_balance', function () {
    $account = Account::factory()->create([
        'balance' => 1000,
    ]);

    $requestData = [
        'uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => 'D',
        'value' => 100.0,
    ];

    $request = new TransactionCreateRequest();
    $request->merge($requestData);

    $createTransaction = new CreateTransaction($account, $request);

    $fee = Transaction::operationFee(
    Transaction::getFee($requestData['type_payment']),
        $requestData['value']
    );

    $total = $requestData['value'] + $fee;

    $transaction = $createTransaction->handle();

    expect($transaction->account_uuid)->toBe($requestData['uuid']);
    expect($transaction->type)->toBe($requestData['type']);
    expect($transaction->type_payment)->toBe($requestData['type_payment']);
    expect($fee)->toBe($transaction->fee);
    expect($total)->toBe($transaction->total);
    expect($transaction->value)->toBe($requestData['value']);
});

test('it_throws_insufficient_balance_exception_when_balance_is_insufficient', function () {
    $account = Account::factory()->create([
        'balance' => 100,
    ]);

    $requestData = [
        'uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => 'D',
        'value' => 1000.0,
    ];

    $request = new TransactionCreateRequest();
    $request->merge($requestData);

    $createTransaction = new CreateTransaction($account, $request);

    $this->expectException(InsufficientBalance::class);

    $createTransaction->handle();

    $this->assertDatabaseMissing('transactions', [
        'account_uuid' => $account->uuid,
        'type' => $requestData['type'],
        'type_payment' => $requestData['type_payment'],
        'value' => $requestData['value'],
    ]);
});
