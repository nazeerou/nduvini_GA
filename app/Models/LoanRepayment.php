<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'branch_id',
        'employee_id',
        'amount',
        'deduction_date',
    ];

    protected $dates = [
        'deduction_date',
    ];

    // Relationships
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
