<?php

use App\Models\Account;

test('it_create_account_successfully', function () {
    $requestData = [
        'numero_conta' => '123456',
        'saldo' => 1000.00,
    ];

    $response = $this->postJson('/api/account', $requestData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'numero_conta',
                'saldo',
            ],
        ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $requestData['numero_conta'],
        'balance' => $requestData['saldo'],
    ]);
});

test('it_create_account_successfully_with_saldo_0', function () {
    $requestData = [
        'numero_conta' => '123456',
        'saldo' => 0,
    ];

    $response = $this->postJson('/api/account', $requestData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'numero_conta',
                'saldo',
            ],
        ]);

    $this->assertDatabaseHas('accounts', [
        'uuid' => $requestData['numero_conta'],
        'balance' => $requestData['saldo'],
    ]);
});

test('it_try_create_account_but_not_accept_saldo_negative', function () {
    $requestData = [
        'numero_conta' => '123456',
        'saldo' => -1000.00,
    ];

    $response = $this->postJson('/api/account', $requestData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'balance',
        ]);

    $this->assertDatabaseMissing('accounts', [
        'uuid' => $requestData['numero_conta'],
        'balance' => $requestData['saldo'],
    ]);
});

test('it_try_create_account_but_not_accept_numero_conta_empty', function () {
    $requestData = [
        'numero_conta' => '',
        'saldo' => 1000.00,
    ];

    $response = $this->postJson('/api/account', $requestData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'uuid',
        ]);

    $this->assertDatabaseMissing('accounts', [
        'uuid' => $requestData['numero_conta'],
        'balance' => $requestData['saldo'],
    ]);
});

test('it_try_create_account_but_not_accept_saldo_empty', function () {
    $requestData = [
        'numero_conta' => '123456',
        'saldo' => '',
    ];

    $response = $this->postJson('/api/account', $requestData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'balance',
        ]);

    $this->assertDatabaseMissing('accounts', [
        'uuid' => $requestData['numero_conta'],
        'balance' => $requestData['saldo'],
    ]);
});

test('it_try_create_account_but_not_accept_saldo_string', function () {
    $requestData = [
        'numero_conta' => '123456',
        'saldo' => 'string',
    ];

    $response = $this->postJson('/api/account', $requestData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'balance',
        ]);

    $this->assertDatabaseMissing('accounts', [
        'uuid' => $requestData['numero_conta'],
        'balance' => $requestData['saldo'],
    ]);
});

test('it_try_create_account_but_not_accept_numero_conta_duplicate', function () {
    $account = Account::factory()->create();
    $requestData = [
        'numero_conta' => $account->uuid,
        'saldo' => 1000.00,
    ];

    $this->postJson('/api/account', $requestData);

    $response = $this->postJson('/api/account', $requestData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'uuid',
        ]);

    $this->assertDatabaseCount('accounts', 1);
});

test('it_get_account_successfully', function () {
    $account = Account::factory()->create();

    $response = $this->get("/api/account/{$account->uuid}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'numero_conta',
                'saldo',
            ],
        ]);
});

test('it_try_get_account_but_not_found', function () {
    $response = $this->get('/api/account/123456');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Conta não encontrada.',
        ]);
});
