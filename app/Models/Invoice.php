<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    
    public $timestamps = false;

        protected $fillable = ["branch_id", "user_id", "account", "make", "vehicle_reg", "reference", "estimate_ref", "client_id", "account_number", "account_name", "bank_name", "branch_name", "swift_code", "invoice_number", "bill_amount", "job_card_no", "payment_status", "created_date"];

}
