<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    public $timestamps = true;

    protected $fillable = [
        "reason"
    ];

}
