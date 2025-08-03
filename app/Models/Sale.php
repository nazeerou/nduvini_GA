<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //
    public $timestamps = false;

    protected $fillable =[
        "product_id", "branch_id", "qty", "vehicle_reg", "make", "model", "chassis", "milleage", "selling_price", "reference", "vat_amount", "qty_balance", "lpo_number", "tax_invoice", "delivery_note", "bill_no", "client_name", "mobile", "created_date", "total_sales", "status", "paid_amount"
    ];


    public function product()
    {
    	return $this->belongsTo('App\Product');
    }

}
