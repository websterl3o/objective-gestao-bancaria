<?php

use App\Models\Account;
use Illuminate\Support\Facades\Log;
use App\Exceptions\InsufficientBalance;

test('it_returns_true_when_account_has_sufficient_balance_for_transaction', function () {
    $account = Account::factory()->create(['balance' => 100]);

    $this->assertTrue($account->canAddTransaction(50));
});

test('it_returns_false_when_account_does_not_have_sufficient_balance_for_transaction', function () {
    $account = Account::factory()->create(['balance' => 100]);

    $this->assertFalse($account->canAddTransaction(150));
});

test('it_allows_transaction_when_balance_is_exactly_equal_to_debit', function () {
    $account = Account::factory()->create(['balance' => 100]);

    $this->assertTrue($account->canAddTransaction(100));
});

test('it_decreases_balance_by_debit_amount_when_sufficient_balance_exists', function () {
    $account = Account::factory()->create(['balance' => 100]);

    $account->debit(50);

    $this->assertEquals(50, $account->balance);
});

test('it_allows_debit_when_balance_is_equal_to_debit_amount', function () {
    $account = Account::factory()->create(['balance' => 100]);

    $account->debit(100);

    $this->assertEquals(0, $account->balance);
});

test('it_correctly_updates_balance_after_multiple_debits', function () {
    $account = Account::factory()->create(['balance' => 100]);

    $account->debit(50);
    $account->debit(25);
    $account->debit(25);

    $this->assertEquals(0, $account->balance);
});

test('it_fails_to_debit_when_balance_is_insufficient', function () {
    $account = Account::factory()->create(['balance' => 100]);
    $balanceExpected = -50;

    $account->debit(50);
    $this->assertEquals(50, $account->balance);

    $account->debit(50);
    $this->assertEquals(0, $account->balance);

    $this->expectException(InsufficientBalance::class, "Erro na tentativa de criar transação na conta {$account->uuid}, por motivo de saldo insuficiente.");

    $account->debit(50);

    $this->assertEquals(0, $account->balance);
});
