<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    public $timestamps = false;

    protected $fillable =[
        "supplier_id", "branch_id", "invoice_number", "amount", "paid_amount", "created_date"
    ];


    public function purchase()
    {
    	return $this->belongsTo('App\Purchase');
    }
}
