<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{

    public $timestamps = false;

    protected $fillable =[
        "supplier_id", "invoice_number", "status"
    ];


    public function purchase()
    {
    	return $this->belongsTo('App\Purchase');
    }
}
