<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    

    protected $fillable =[
        "branch_id", "supplier_name" ,"phone"
    ];


    public function product()
    {
    	return $this->belongsTo('App\Product');
    }
}
