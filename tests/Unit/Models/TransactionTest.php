<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Exceptions\InsufficientBalance;

test('it_calculates_operation_fee_correctly_for_valid_percentage_and_amount', function () {
    $value = 100;
    $fee = 0.03;

    $result = Transaction::operationFee($fee, $value);

    $this->assertEquals(3, $result);
});

test('it_returns_zero_when_percentage_is_zero', function () {
    $value = 100;
    $fee = 0;

    $result = Transaction::operationFee($fee, $value);

    $this->assertEquals(0, $result);
});

test('it_returns_correct_fee_when_amount_is_zero', function () {
    $value = 0;
    $fee = 0.03;

    $result = Transaction::operationFee($fee, $value);

    $this->assertEquals(0, $result);
});

test('it_calculates_fee_for_large_amounts_and_valid_percentage', function () {
    $value = 1000;
    $fee = 0.05;

    $result = Transaction::operationFee($fee, $value);

    $this->assertEquals(50, $result);
});

test('it_throws_exception_when_percentage_is_negative', function () {
    $value = 100;
    $fee = -0.03;

    $this->expectException(InvalidArgumentException::class, 'Os valores não podem ser negativos.');

    Transaction::operationFee($fee, $value);
});

test('it_throws_exception_when_amount_is_negative', function () {
    $value = -100;
    $fee = 0.03;

    $this->expectException(InvalidArgumentException::class, 'Os valores não podem ser negativos.');

    Transaction::operationFee($fee, $value);
});

test('it_handles_percentage_greater_than_100', function () {
    $value = 100;
    $fee = 1.01;

    $this->expectException(InvalidArgumentException::class, 'A porcentagem não pode ser maior que 100%.');

    Transaction::operationFee($fee, $value);
});

test('it_debits_account_balance_when_transaction_type_is_debit', function () {
    $account = Account::factory()->create(['balance' => 100]);
    $transaction = Transaction::prepareTransaction($account->uuid, 'debit', 'D', 10);

    $transaction->updateBalance();

    $this->assertEquals(89.7, $account->fresh()->balance);
});

test('it_does_not_update_balance_when_transaction_type_is_not_debit', function () {
    $account = Account::factory()->create(['balance' => 100]);
    $transaction = Transaction::prepareTransaction($account->uuid, 'credit', 'D', 100);

    $transaction->updateBalance();

    $this->assertEquals(100, $account->fresh()->balance);
});

test('it_throws_exception_when_account_has_insufficient_balance_for_debit', function () {
    $account = Account::factory()->create(['balance' => 100]);
    $transaction = Transaction::prepareTransaction($account->uuid, 'debit', 'D', 100);

    $this->expectException(InsufficientBalance::class, "Erro na tentativa de criar transação na conta {$account->uuid}, por motivo de saldo insuficiente.");

    $transaction->updateBalance();

    $this->assertEquals(100, $account->fresh()->balance);
});

test('it_creates_transaction_with_correct_fee_and_total_for_valid_inputs', function () {
    $value = 100;
    $type_payment = 'D';
    $transaction = Transaction::prepareTransaction('uuid', 'debit', $type_payment, $value);

    $this->assertEquals(Transaction::operationFee(Transaction::getFee($type_payment), $value), $transaction->fee);
    $this->assertEquals(($transaction->value + $transaction->fee), $transaction->total);
});

test('it_calculates_fee_correctly_based_on_type_payment', function () {
    $value = 100;
    $type_payment = 'C';
    $transaction = Transaction::prepareTransaction('uuid', 'debit', $type_payment, $value);

    $this->assertEquals(Transaction::operationFee(Transaction::getFee($type_payment), $value), $transaction->fee);
});

test('it_returns_transaction_with_correct_total_value', function () {
    $value = 100;
    $type_payment = 'D';
    $transaction = Transaction::prepareTransaction('uuid', 'debit', $type_payment, $value);

    $this->assertEquals(($transaction->value + $transaction->fee), $transaction->total);
});

test('it_throws_exception_when_type_payment_is_invalid', function () {
    $value = 100;
    $type_payment = 'X';

    $this->expectException(InvalidArgumentException::class, 'Tipo de pagamento inválido.');

    Transaction::prepareTransaction('uuid', 'debit', $type_payment, $value);
});

test('it_fails_when_value_is_negative', function () {
    $value = -100;
    $type_payment = 'D';

    $this->expectException(InvalidArgumentException::class, 'Os valores não podem ser negativos.');

    Transaction::prepareTransaction('uuid', 'debit', $type_payment, $value);
});

test('it_returns_zero_fee_when_type_payment_has_no_fee', function () {
    $value = 100;
    $type_payment = 'P';
    $transaction = Transaction::prepareTransaction('uuid', 'debit', $type_payment, $value);

    $this->assertEquals(0, $transaction->fee);
});

test('it_handles_large_transaction_values_correctly', function () {
    $value = 1000;
    $type_payment = 'C';
    $transaction = Transaction::prepareTransaction('uuid', 'debit', $type_payment, $value);

    $this->assertEquals(Transaction::operationFee(Transaction::OPERATION_FEE[$type_payment], $value), $transaction->fee);
    $this->assertEquals(($transaction->value + $transaction->fee), $transaction->total);
});

test('it_returns_zero_fee_for_type_payment_P', function () {
    $type_payment = 'P';

    $result = Transaction::getFee($type_payment);

    $this->assertEquals(Transaction::OPERATION_FEE[$type_payment], $result);
});

test('it_returns_correct_fee_for_type_payment_D', function () {
    $type_payment = 'D';

    $result = Transaction::getFee($type_payment);

    $this->assertEquals(Transaction::OPERATION_FEE[$type_payment], $result);
});

test('it_returns_correct_fee_for_type_payment_C', function () {
    $type_payment = 'C';

    $result = Transaction::getFee($type_payment);

    $this->assertEquals(Transaction::OPERATION_FEE[$type_payment], $result);
});

test('it_throws_exception_for_invalid_type_payment', function () {
    $type_payment = 'X';

    $this->expectException(InvalidArgumentException::class, 'Tipo de pagamento inválido.');

    Transaction::getFee($type_payment);
});

test('it_throws_exception_when_type_payment_is_empty', function () {
    $type_payment = '';

    $this->expectException(InvalidArgumentException::class, 'Tipo de pagamento inválido.');

    Transaction::getFee($type_payment);
});
