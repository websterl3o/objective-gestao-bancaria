<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    CONST OPERATION_FEE = [
        'P' => 0,
        'D' => 0.03,
        'C' => 0.05
    ];

    protected $fillable = [
        'uuid',
        'balance'
    ];

    protected $casts = [
        'balance' => 'double'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_uuid', 'uuid');
    }

    public function operationFee($fee, $balance): float
    {
        return $balance * $fee;
    }

    public function createTransaction($data)
    {
        $fee = self::OPERATION_FEE[$data['type_payment']];

        $data['fee'] = $this->operationFee($fee, $data['value']);
        $data['total'] = $data['value'] + $data['fee'];

        $transaction = $this->transactions()->create($data);

        if ($data['type'] === 'debit') {
            $this->withdraw($data['value']);
            return $transaction;
        }

        $this->deposit($data['value']);

        return $transaction;
    }

    public function deposit($balance)
    {
        $this->balance += $balance;
        $this->save();
    }

    public function withdraw($balance)
    {
        $this->balance -= $balance;
        $this->save();
    }

    public function canAddTransaction($value, $type_payment): bool
    {
        return $this->balance < ($value + $this->operationFee($this::OPERATION_FEE[$type_payment], $value));
    }
}
