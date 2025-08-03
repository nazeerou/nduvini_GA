<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // protected $table = 'roles'; 

    protected $fillable =[
        "name", "description", "is_active"
    ];


    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

}
