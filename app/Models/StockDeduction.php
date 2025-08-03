<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockDeduction extends Model
{
    
    public $timestamps = false;

    protected $fillable = [
        "branch_id", "product_id", "reasons", "created_date"
    ];

}
