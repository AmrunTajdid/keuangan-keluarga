<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'month',
        'year',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getSpentAmountAttribute()
    {
        return (float) Transaction::where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $this->month)
            ->whereYear('transaction_date', $this->year)
            ->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return (float) $this->amount - $this->spent_amount;
    }

    public function getUsagePercentageAttribute()
    {
        if ($this->amount <= 0) return 0;
        return round(($this->spent_amount / (float) $this->amount) * 100, 1);
    }
}
