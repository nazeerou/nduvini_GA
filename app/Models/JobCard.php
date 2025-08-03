<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCard extends Model
{
    //
    public $timestamps = false;

    protected $fillable =[
         "branch_id", "user_id", "job_card_reference", "job_card_no", "job_card_ID",  "client_id", "vehicle_reg", "estimate_reference", "amount", "created_date", "status"
    ];


}
