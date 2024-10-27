<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use App\Exceptions\InsufficientBalance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;

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

    public function debit($value)
    {
        if (($this->balance - $value) <= 0) {
            throw new InsufficientBalance("Erro na tentativa de criar transação na conta {$this->uuid}, por motivo de saldo insuficiente.");
        }

        $this->balance -= $value;
        $this->save();
    }

    public function canAddTransaction($value): bool
    {
        return $this->balance >= $value;
    }
}
