<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollContribution extends Model
{
    protected $fillable = ['payroll_id', 'branch_id', 'contribution_id', 'name'];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
    
    public function contribution()
    {
        return $this->belongsTo(Contribution::class, 'contribution_id');
    }
    
    
}
