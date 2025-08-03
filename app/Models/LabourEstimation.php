<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabourEstimation extends Model
{
    //
    public $timestamps = false;

    protected $fillable =[
         "labour_name", "branch_id", "user_id", "total_amount", "charge", "qty", "rate", "estimate_reference", "created_date"
    ];


    public function product()
    {
    	return $this->belongsTo('App\Product');
    }

}
