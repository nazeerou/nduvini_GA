<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'employee_id', 'contract_type_id', 'contract_duration',
        'start_date', 'end_date', 'salary_group_id', 'cv_file', 'nida_file', 'contract_file', 
        'termination_date', 'termination_reason', 'is_termination', 'termination_type'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
