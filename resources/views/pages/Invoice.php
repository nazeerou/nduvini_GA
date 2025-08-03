<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    
    public $timestamps = false;

    protected $fillable = ["supplier_id", "invoice_number"];

    public function purchases()
    {
    	return $this->hasMany('App/purchase');
    	
    }
}
