<?php

use App\Models\Account;
use App\Models\Transaction;

test('it_creates_transaction_successfully_with_type_payment_P', function () {
    $account = Account::factory()->create([
        'balance' => 1000.00,
    ]);

    $requestData = [
        'numero_conta' => $account->uuid,
        'forma_pagamento' => 'P',
        'valor' => 100.00,
    ];

    $response = $this->postJson('/api/transaction', $requestData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'numero_conta',
                'saldo',
            ],
        ]);

    $fee = Transaction::operationFee(
    Transaction::getFee($requestData['forma_pagamento']),
        $requestData['valor']
    );

    $this->assertDatabaseHas('transactions', [
        'account_uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => $requestData['forma_pagamento'],
        'value' => $requestData['valor'],
        'fee' => $fee,
        'total' => $requestData['valor'] + $fee,
    ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $account->uuid,
        'balance' => $account->balance - $requestData['valor'] - $fee,
    ]);
});

test('it_creates_transaction_successfully_with_type_payment_D', function () {
    $account = Account::factory()->create([
        'balance' => 1000.00,
    ]);

    $requestData = [
        'numero_conta' => $account->uuid,
        'forma_pagamento' => 'D',
        'valor' => 100.00,
    ];

    $response = $this->postJson('/api/transaction', $requestData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'numero_conta',
                'saldo',
            ],
        ]);

    $fee = Transaction::operationFee(
    Transaction::getFee($requestData['forma_pagamento']),
        $requestData['valor']
    );

    $this->assertDatabaseHas('transactions', [
        'account_uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => $requestData['forma_pagamento'],
        'value' => $requestData['valor'],
        'fee' => $fee,
        'total' => $requestData['valor'] + $fee,
    ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $account->uuid,
        'balance' => $account->balance - $requestData['valor'] - $fee,
    ]);
});

test('it_creates_transaction_successfully_with_type_payment_C', function () {
    $account = Account::factory()->create([
        'balance' => 1000.00,
    ]);

    $requestData = [
        'numero_conta' => $account->uuid,
        'forma_pagamento' => 'C',
        'valor' => 100.00,
    ];

    $response = $this->postJson('/api/transaction', $requestData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'numero_conta',
                'saldo',
            ],
        ]);

    $fee = Transaction::operationFee(
    Transaction::getFee($requestData['forma_pagamento']),
        $requestData['valor']
    );

    $this->assertDatabaseHas('transactions', [
        'account_uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => $requestData['forma_pagamento'],
        'value' => $requestData['valor'],
        'fee' => $fee,
        'total' => $requestData['valor'] + $fee,
    ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $account->uuid,
        'balance' => $account->balance - $requestData['valor'] - $fee,
    ]);
});

test('it_handles_insufficient_balance_correctly', function () {
    $account = Account::factory()->create([
        'balance' => 100.00,
    ]);

    $requestData = [
        'numero_conta' => $account->uuid,
        'forma_pagamento' => 'D',
        'valor' => 1000.00,
    ];

    $response = $this->postJson('/api/transaction', $requestData);

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Saldo insuficiente.',
        ]);

    $this->assertDatabaseMissing('transactions', [
        'account_uuid' => $account->uuid,
        'type' => 'debit',
        'type_payment' => $requestData['forma_pagamento'],
        'value' => $requestData['valor'],
    ]);
});
