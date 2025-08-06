<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'month',
        'user_id',
    ];

    /**
     * Get the employee associated with the advance.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who added the advance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
