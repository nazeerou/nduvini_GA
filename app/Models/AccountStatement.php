<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountStatement extends Model
{
    //
    public $timestamps = false;

    protected $fillable =[
        "branch_id", "account_id", "reference", "name", "debit", "credit", "initial_balance", "balance",  "created_at", "updated_at"
    ];
}
