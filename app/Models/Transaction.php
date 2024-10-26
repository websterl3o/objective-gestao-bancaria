<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
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

    public static function operationFee($fee, $value): float
    {
        return $value * $fee;
    }

    public function updateBalance(): void
    {
        if ($this->type === 'debit') {
            $this->account->debit($this->total);
            return;
        }
    }
}
