<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NssfDetail extends Model
{
    protected $fillable = ['employee_id', 'member_number'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
