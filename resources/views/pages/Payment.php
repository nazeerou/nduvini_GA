<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable =[
        "supplier_id", "invoice_number", "amount", "paid_amount", "status"
    ];


    public function purchase()
    {
    	return $this->belongsTo('App\Purchase');
    }
}
