<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    protected $fillable = ['user_id', 'branch_id', 'debt_id', 'amount', 'paid_date', 'notes'];

    protected $casts = [
        'amount'    => 'decimal:2',
        'paid_date' => 'date',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}