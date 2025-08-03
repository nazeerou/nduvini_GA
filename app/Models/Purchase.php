<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public $table = 'purchases';

    public $timestamps = false;

    protected $fillable = [
        "product_id", "make", "supplier_id", "branch_id", "paid_amount", "lpo_number", "lpo_file", "model", "purchase_price", "part_number", "vat_type", "vat_amount", "quantity", "invoice_number", "total_purchase", "invoice_file", "payment", "status", "created_date"
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
