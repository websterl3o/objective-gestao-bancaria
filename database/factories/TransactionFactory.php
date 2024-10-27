<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $accont_uuid = Account::factory()->create()->uuid;
        $value = $this->faker->randomFloat(2, 0, 1000);
        $type_payment = $this->faker->randomElement(['P', 'D', 'C']);
        $fee = Transaction::operationFee(Transaction::OPERATION_FEE[$type_payment], $value);
        $total = $value + $fee;

        return [
            'account_uuid' => $accont_uuid,
            'type' => 'debit',
            'type_payment' => $type_payment,
            'value' => $value,
            'fee' => $fee,
            'total' => $total,
        ];
    }

    /**
     * Recebendo value como parâmetro cria usando
     */
}
