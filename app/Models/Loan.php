<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'employee_id', 'type', 'principal',
        'monthly_deduction', 'balance', 'start_date', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

public function loanRepayments()
{
    return $this->hasMany(LoanRepayment::class);
}

}
