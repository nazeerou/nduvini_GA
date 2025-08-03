<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{

    public $table = 'item_models';

    public $timestamps = false;

    protected $fillable = [
        "product_id", "brand_id"
    ];

    public function brand()
    {
    	return $this->hasMany('App/Brand');
    	
    }
}
