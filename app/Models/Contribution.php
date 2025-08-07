<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = [
        'name', 'type', 'rate', 'description',
    ];


    public function employees()
{
    return $this->belongsToMany(Employee::class, 'assigned_employee_contributions')
                ->using(AssignedEmployeeContribution::class)
                ->withTimestamps();
}

}
