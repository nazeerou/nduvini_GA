<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    protected $table = 'account_transactions';

    protected $fillable = [
        'branch_id',
        'account_id',
        'expense_category_id',
        'transaction_date',
        'transaction_type',
        'particulars',
        'voucher_type',
        'voucher_no',
        'reference_type',
        'reference_id',
        'debit',
        'credit',
        'description',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];
}
