<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

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
}
