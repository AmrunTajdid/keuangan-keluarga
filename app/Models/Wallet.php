<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'account_number',
        'initial_balance',
        'color',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'wallet_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(Transaction::class, 'to_wallet_id');
    }

    public function getCurrentBalanceAttribute()
    {
        $income = $this->transactions()->where('type', 'income')->sum('amount');
        $expense = $this->transactions()->where('type', 'expense')->sum('amount');
        $transfersOut = $this->transactions()->where('type', 'transfer')->sum('amount');
        $transfersIn = $this->incomingTransfers()->where('type', 'transfer')->sum('amount');

        return (float) $this->initial_balance + (float) $income - (float) $expense - (float) $transfersOut + (float) $transfersIn;
    }
}
