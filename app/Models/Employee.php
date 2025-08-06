<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

     protected $fillable = [
        'firstname', 'middlename', 'surname', 'email', 'mobile','joining_date', 'salary_group_id','contract_type_id',
        'department_id', 'designation_id', 'contract_type', 'photo', 'nida_number',
        'contract_duration', 'salary_group_id', 'created_by',
        'cv_file', 'nida_file', 'contract_file', 'branch_id',
    ];

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    
    public function documents() {
        return $this->hasMany(EmployeeDocument::class);
    }
    
    public function termination() {
        return $this->hasOne(EmployeeTermination::class);
    }

    public function department()
{
    return $this->belongsTo(Department::class);
}

public function designation()
{
    return $this->belongsTo(Designation::class);
}

public function salaryGroup()
{
    return $this->belongsTo(SalaryGroup::class);
}

public function bankAccount()
{
    return $this->hasMany(BankAccount::class);
}

public function nssfdetails()
{
    return $this->hasOne(NssfDetail::class); // not hasMany
}
    
}
