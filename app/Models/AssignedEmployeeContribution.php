<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AssignedEmployeeContribution extends Pivot
{
    protected $table = 'assigned_employee_contributions';

    protected $fillable = [
        'employee_id',
        'contribution_id',
    ];

    // Optional: define relationships if needed
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function contribution()
    {
        return $this->belongsTo(Contribution::class);
    }
}
