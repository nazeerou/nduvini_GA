<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Contract;
use App\Models\SalaryGroup;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeStatus;
use App\Models\ContractType;
use Illuminate\Support\Str;
use App\Models\Bank;
use Illuminate\Http\Request;
use DB;
use Auth;
use Illuminate\Support\Facades\Log;


class EmployeeController extends Controller
{
    public function index()
    {
        $salaryGroups = SalaryGroup::where('branch_id', Auth::user()->branch_id)->get();

        $branches = Branch::select('*')->get();

        $departments = Department::all();
        $designations = Designation::all();
        $statuses = EmployeeStatus::all();
        $contract_types = ContractType::all();

        $employees = DB::table('employees')
                    ->select('employees.id', 'employees.name', 'employees.email', 'photo', 'departments.name as department_name',
                     'employees.mobile', 'employees.joining_date', 'designations.name as position', 'contract_types.name as contract_name',
                     'salary_groups.group_name', 'branches.branch_name', 'salary_groups.id as salary_id',
                      'salary_groups.group_name', 'branches.id as branch_id')
                     ->leftJoin('salary_groups', 'salary_groups.id', 'employees.salary_group_id')
                     ->leftJoin('branches', 'branches.id', 'employees.branch_id')
                     ->leftJoin('designations', 'designations.id', 'employees.designation_id')
                     ->leftJoin('departments', 'departments.id', 'employees.department_id')
                     ->leftJoin('contracts', 'contracts.employee_id', 'employees.id')
                     ->leftJoin('contract_types', 'contract_types.id', 'contracts.contract_type_id')
                    //  ->where('employees.branch_id', Auth::user()->branch_id)
                     ->get();

        $employee = Employee::where('employees.branch_id', Auth::user()->branch_id)->first();
        
        return view('employees.index', compact('employees', 'salaryGroups', 
        'branches', 'employee', 'departments', 'designations', 'statuses', 'contract_types'));
    }

    public function create()
    {
        return view('employees.create');
    }
    
    public function saveEmployee(Request $request) 
    {

        // return $request;

        $request->validate([
            // 'name' => 'required|string|max:255',
            // 'mobile' => 'nullable|string|max:20',
            // 'email' => 'nullable|email|max:255',
            // 'designation_id' => 'nullable|exists:designations,id',
            // 'department_id' => 'nullable|exists:departments,id',
            // 'branch_id' => 'nullable|exists:branches,id',
            // 'nida' => 'nullable|string|max:100',
    
            // 'cv_file' => 'nullable|file|mimes:pdf,doc,docx',
            // 'nida_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            // 'contract_file' => 'nullable|file|mimes:pdf',
        ]);
    
        
        try {
            $safeName = Str::slug($request->name);
            $timestamp = now()->format('Ymd_His');
    
            $cvPath = null;
            $nidaPath = null;
            $contractPath = null;
    
            if ($request->hasFile('cv_file')) {
                $ext = $request->file('cv_file')->getClientOriginalExtension();
                $filename = "cv_{$safeName}_{$timestamp}." . $ext;
                $cvPath = $request->file('cv_file')->storeAs('documents/cv', $filename, 'public');
            }
    
            if ($request->hasFile('nida_file')) {
                $ext = $request->file('nida_file')->getClientOriginalExtension();
                $filename = "nida_{$safeName}_{$timestamp}." . $ext;
                $nidaPath = $request->file('nida_file')->storeAs('documents/nida', $filename, 'public');
            }
    
            if ($request->hasFile('contract_file')) {
                $ext = $request->file('contract_file')->getClientOriginalExtension();
                $filename = "contract_{$safeName}_{$timestamp}." . $ext;
                $contractPath = $request->file('contract_file')->storeAs('documents/contract', $filename, 'public');
            }
    
            $employee = Employee::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'nida_number' => $request->nida_number,
                'salary_group_id' => $request->salary_group_id,
                'designation_id' => $request->designation_id,
                'department_id' => $request->department_id,
                'branch_id' => $request->branch_id,
                'contract_type_id' => $request->contract_type_id,
                'joining_date' => $request->start_date,
                'cv_file' => $cvPath,
                'nida_file' => $nidaPath,
                'contract_file' => $contractPath,
                'created_by' => Auth::id()
            ]);
    
            try {
                $contract = new Contract([
                    'employee_id' => $employee->id,
                    'salary_group_id' => $request->salary_group_id,
                    'contract_type_id' => $request->contract_type_id,
                    'contract_duration' => $request->contract_duration,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'contract_file' => $contractPath,
                    'cv_file' => $cvPath,
                    'nida_file' => $nidaPath
                ]);
                $contract->save();
    
                return redirect()->back()->with('message', 'Employee added successfully.');
            } catch (\Exception $e) {
                // If contract fails, delete the employee and uploaded files
                $employee->delete();
                if ($cvPath) Storage::disk('public')->delete($cvPath);
                if ($nidaPath) Storage::disk('public')->delete($nidaPath);
                if ($contractPath) Storage::disk('public')->delete($contractPath);
    
                Log::error('Failed to save contract: ' . $e->getMessage());
    
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to save employee contract: ' . $e->getMessage());
            }
    
        } catch (\Exception $e) {
            Log::error('Failed to save employee: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save employee: ' . $e->getMessage());
        }
    }
    

    
    public function filter(Request $request)
    {
        $query = Employee::with(['department', 'designation', 'branch', 'contract']);
    
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
    
        if ($request->contract_type_id) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('contract_type_id', $request->contract_type_id);
            });
        }
    
        $employees = $query->get();
            
        return view('employees.partials.employee_table', compact('employees'))->render();
    }
    

    public function edit(Employee $employee)
{
    return view('employees.edit', compact('employee'));
}

public function update(Request $request, Employee $employee)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:employees,email,' . $employee->id,
        'position' => 'required',
        'basic_salary' => 'required|numeric',
    ]);

    $employee->update($request->all());
    return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
}

public function destroy(Employee $employee)
{
    $employee->delete();
    return redirect()->route('employees.index')->with('success', 'Employee deleted.');
}


public function getEmployeeDetails($id)
    {
        $banks = Bank::all();

        $employee = Employee::with([
            'department',
            'designation',
            'salaryGroup',
            'contract',
            'contractType',
            'bankAccount'
        ])->findOrFail($id);
        
        return view('employees.employee-details', compact('employee', 'banks'));
    }
}


