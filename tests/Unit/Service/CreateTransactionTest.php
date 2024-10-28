<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Services\CreateTransaction;
use App\Exceptions\InsufficientBalance;

test('it_creates_transaction_successfully_when_account_has_sufficient_balance_with_type_payment_P', function () {
    $account = Account::factory()->create([
        'balance' => 1000,
    ]);

    $requestData = [
        'uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => 'P',
        'value' => 100.0,
    ];

    $transaction = Transaction::prepareTransaction(
        $account->uuid,
        $requestData['type'],
        $requestData['type_payment'],
        $requestData['value']
    );

    $createTransaction = new CreateTransaction($account, $transaction);

    $fee = Transaction::operationFee(
    Transaction::getFee($requestData['type_payment']),
        $requestData['value']
    );

    $total = $requestData['value'] + $fee;

    $transaction = $createTransaction->handle();

    $this->assertDatabaseHas('transactions', [
        'account_uuid' => $account->uuid,
        'type' => $requestData['type'],
        'type_payment' => $requestData['type_payment'],
        'value' => $requestData['value'],
        'fee' => $fee,
        'total' => $total,
    ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $account->uuid,
        'balance' => $account->balance - $requestData['value'] - $fee,
    ]);
});

test('it_creates_transaction_successfully_when_account_has_sufficient_balance_with_type_payment_D', function () {
    $account = Account::factory()->create([
        'balance' => 1000,
    ]);

    $requestData = [
        'uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => 'D',
        'value' => 100.0,
    ];

    $transaction = Transaction::prepareTransaction(
        $account->uuid,
        $requestData['type'],
        $requestData['type_payment'],
        $requestData['value']
    );

    $createTransaction = new CreateTransaction($account, $transaction);

    $fee = Transaction::operationFee(
    Transaction::getFee($requestData['type_payment']),
        $requestData['value']
    );

    $total = $requestData['value'] + $fee;

    $transaction = $createTransaction->handle();

    $this->assertDatabaseHas('transactions', [
        'account_uuid' => $account->uuid,
        'type' => $requestData['type'],
        'type_payment' => $requestData['type_payment'],
        'value' => $requestData['value'],
        'fee' => $fee,
        'total' => $total,
    ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $account->uuid,
        'balance' => $account->balance - $requestData['value'] - $fee,
    ]);
});

test('it_creates_transaction_successfully_when_account_has_sufficient_balance_with_type_payment_C', function () {
    $account = Account::factory()->create([
        'balance' => 1000,
    ]);

    $requestData = [
        'uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => 'C',
        'value' => 100.0,
    ];

    $transaction = Transaction::prepareTransaction(
        $account->uuid,
        $requestData['type'],
        $requestData['type_payment'],
        $requestData['value']
    );

    $createTransaction = new CreateTransaction($account, $transaction);

    $fee = Transaction::operationFee(
    Transaction::getFee($requestData['type_payment']),
        $requestData['value']
    );

    $total = $requestData['value'] + $fee;

    $transaction = $createTransaction->handle();

    $this->assertDatabaseHas('transactions', [
        'account_uuid' => $account->uuid,
        'type' => $requestData['type'],
        'type_payment' => $requestData['type_payment'],
        'value' => $requestData['value'],
        'fee' => $fee,
        'total' => $total,
    ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $account->uuid,
        'balance' => $account->balance - $requestData['value'] - $fee,
    ]);
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

    $transaction = Transaction::prepareTransaction(
        $account->uuid,
        $requestData['type'],
        $requestData['type_payment'],
        $requestData['value']
    );

    $createTransaction = new CreateTransaction($account, $transaction);

    $this->expectException(InsufficientBalance::class);

    $createTransaction->handle();

    $this->assertDatabaseMissing('transactions', [
        'account_uuid' => $account->uuid,
        'type' => $requestData['type'],
        'type_payment' => $requestData['type_payment'],
        'value' => $requestData['value'],
    ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $account->uuid,
        'balance' => $account->balance,
    ]);
});
