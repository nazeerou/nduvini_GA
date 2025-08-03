<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{

    public $timestamps = false;

    protected $fillable =[
        "prod_id", "branch_id", "sale_price", "client_id", "status"
    ];


    public function brand()
    {
    	return $this->belongsTo('App\Brand');
    }

}
