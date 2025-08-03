<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPayment extends Model
{
    public $timestamps = false;

    protected $fillable =[
         "branch_id", "client_id", "bill_amount", "bill_no", "paid_amount", "created_date"
    ];

    public function sale()
    {
    	return $this->belongsTo('App\Sale');
    }
}
