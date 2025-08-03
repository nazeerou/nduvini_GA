<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{

    protected $fillable =[
        "current_year", "previous_year", "first_date", "second_date"
    ];

}
