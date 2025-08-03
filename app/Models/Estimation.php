<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    //
    public $timestamps = false;

    protected $fillable =[
        "product_id", "branch_id", "user_id", "qty", "account_no", "account_prefix", "vehicle_reg", "customer_name", "temesa_fee",  "profoma_invoice", "total_discount", "valid_estimate_date", "make", "model", "chassis", "milleage", "registration_year", "selling_price", "discount", "reference", "vat_amount",  "lpo_number",  "job_card_no", "client_name", "created_date", "total_sales", "status", "paid_amount"
    ];


    public function product()
    {
    	return $this->belongsTo('App\Product');
    }

}
