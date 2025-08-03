<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    public $timestamps = false;

    protected $fillable = [
       "business_name", "nickname", "type", "logo_file", "address"
    ];

}
