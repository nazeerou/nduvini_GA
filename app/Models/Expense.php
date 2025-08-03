<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    public $timestamps = false;

    protected $fillable =[
        "branch_id", "reference_no", "voucher_no", "expense_category_id", "account_id", "user_id", "amount", "note", "paid_to",  "created_date"  
    ];

    public function expenseCategory() {
    	return $this->belongsTo('App\ExpenseCategory');
    }
}
