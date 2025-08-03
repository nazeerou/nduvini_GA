<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustmentHistory extends Model
{

    protected $fillable = [
                    "product_id", "reason_id", "qty", "qty_in_stock"
    ];

    public function adjustment()
    {
    	return $this->hasMany('App/Adjustment');
    	
    }
}
