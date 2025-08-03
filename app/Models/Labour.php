<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Labour extends Model
{
    
    public $timestamps = false;

    protected $fillable = ["branch_id", "labour", "rate", "charge" ];

}
