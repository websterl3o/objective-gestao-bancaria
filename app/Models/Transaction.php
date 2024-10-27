<?php

namespace App\Models;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    CONST OPERATION_FEE = [
        'P' => 0,
        'D' => 0.03,
        'C' => 0.05
    ];

    protected $fillable = [
        'account_uuid',
        'type',
        'type_payment',
        'value',
        'fee',
        'total',
    ];

    protected $casts = [
        'value' => 'double',
        'fee' => 'double',
        'total' => 'double',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_uuid', 'uuid');
    }

    public static function operationFee(float $fee, float $value): float
    {
        if ($fee < 0 || $value < 0) {
            throw new InvalidArgumentException('Os valores não podem ser negativos.');
        }

        if ($fee > 1) {
            throw new InvalidArgumentException('A porcentagem não pode ser maior que 100%.');
        }

        return $value * $fee;
    }

    public function updateBalance(): void
    {
        if ($this->type === 'debit') {
            $this->account->debit($this->total);
            return;
        }
    }

    public static function getFee(string $type_payment): float
    {
        if (!array_key_exists($type_payment, self::OPERATION_FEE)) {
            throw new InvalidArgumentException('Tipo de pagamento inválido.');
        }

        return self::OPERATION_FEE[$type_payment];
    }

    public static function prepareTransaction(string $account_uuid, string $type, string $type_payment, float $value): self
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Os valores não podem ser negativos.');
        }

        $fee = self::operationFee(self::getFee($type_payment), $value);
        $total = $value + $fee;

        return new self([
            'account_uuid' => $account_uuid,
            'type' => $type,
            'type_payment' => $type_payment,
            'value' => $value,
            'fee' => $fee,
            'total' => $total,
        ]);
    }
}
