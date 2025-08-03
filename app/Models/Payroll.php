<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'branch_id', 'month', 'basic_salary', 'allowance', 'deduction', 
    'description', 'net_salary', 'reference', 'is_paid'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function contributions()
{
    return $this->hasMany(PayrollContribution::class);
}

}
