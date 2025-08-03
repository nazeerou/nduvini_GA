<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable =[
        "product_id", "branch_id", "part_number", "brand_id", "model", "purchase_unit", "sale_unit", "quantity", "purchasing_price", "total_purchase", "total_sale", "selling_price", "description", "maximum_stock" ,"alert_qty", "is_active"
    ];


    public function brand()
    {
    	return $this->belongsTo('App\Brand');
    }

}
