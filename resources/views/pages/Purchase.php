<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public $table = 'purchases';

    public $timestamps = false;

    protected $fillable = [
        "product_id", "make", "supplier_id", "model", "purchase_price", "vat_type", "vat_amount", "quantity", "invoice_number", "total_purchase", "invoice_file", "payment", "status", "created_date"
    ];

    public function brand()
    {
    	return $this->hasMany('App/Brand');
    	
    }

    public function product()
    {
    	return $this->hasMany('App/Poduct');
    	
    }
}
