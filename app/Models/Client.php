<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $timestamps = false;

    protected $fillable =[
       "branch_id", "client_name", "abbreviation", "phone", "address", "place", "email", "tin", "vrn"
    ];

}
