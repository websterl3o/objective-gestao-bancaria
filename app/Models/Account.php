<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

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

    public function debit($balance)
    {
        $this->balance -= $balance;
        $this->save();
    }

    public function canAddTransaction($value, $type_payment): bool
    {
        return $this->balance < ($value + $this->operationFee(Transaction::OPERATION_FEE[$type_payment], $value));
    }
}
