<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryGroup extends Model
{
    //
    protected $fillable = ['branch_id', 'group_name', 'basic_salary', 'allowance', 'deductions'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
