<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountHistory extends Model
{
    public $timestamps = false;

    protected $fillable =[
        "account_id", "amount", "branch_id", "created_date", 
    ];
}
