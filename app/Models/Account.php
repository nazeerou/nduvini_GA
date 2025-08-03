<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
    protected $fillable =[
        "branch_id",  "account_no", "name", "initial_balance", "total_balance", "note", "is_default", "is_active"
    ];
}

