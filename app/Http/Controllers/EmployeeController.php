<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Branch;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Contribution;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeStatus;
use App\Models\SalaryGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Main listing page.
     */
    public function index()
    {
        $salaryGroups   = SalaryGroup::where('branch_id', Auth::user()->branch_id)->get();
        $branches       = Branch::orderBy('branch_name')->get();
        $departments    = Department::orderBy('name')->get();
        $designations   = Designation::orderBy('name')->get();
        $statuses       = EmployeeStatus::all();
        $contractTypes  = ContractType::orderBy('name')->get();

        // Default to current user's branch so behavior matches previous screen
        $employees = $this->buildEmployeeQuery(
            new Request(['branch_id' => Auth::user()->branch_id])
        );

        return view('employees.index', compact(
            'employees',
            'salaryGroups',
            'branches',
            'departments',
            'designations',
            'statuses',
            'contractTypes'
        ));
    }

    /**
     * Show create form (fallback — main creation is in modal).
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a new employee + contract.
     */
    public function saveEmployee(Request $request)
    {
        $request->validate([
            'firstname'        => 'required|string|max:100',
            'middlename'       => 'nullable|string|max:100',
            'surname'          => 'required|string|max:100',
            'email'            => 'nullable|email|max:150',
            'mobile'           => 'nullable|string|max:30',
            'nida_number'      => 'nullable|string|max:50',
            'salary_group_id'  => 'nullable|exists:salary_groups,id',
            'designation_id'   => 'nullable|exists:designations,id',
            'department_id'    => 'nullable|exists:departments,id',
            'branch_id'        => 'nullable|exists:branches,id',
            'contract_type_id' => 'nullable|exists:contract_types,id',
            'start_date'       => 'nullable|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'cv_file'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'nida_file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'contract_file'    => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $storedFiles = [];
        try {
            $safeName  = Str::slug(trim($request->firstname . '-' . $request->surname));
            $timestamp = now()->format('Ymd_His');

            $cvPath       = $this->storeFile($request, 'cv_file',       'documents/cv',       'cv',       $safeName, $timestamp, $storedFiles);
            $nidaPath     = $this->storeFile($request, 'nida_file',     'documents/nida',     'nida',     $safeName, $timestamp, $storedFiles);
            $contractPath = $this->storeFile($request, 'contract_file', 'documents/contract', 'contract', $safeName, $timestamp, $storedFiles);

            DB::beginTransaction();

            $employee = Employee::create([
                'firstname'        => $request->firstname,
                'middlename'       => $request->middlename,
                'surname'          => $request->surname,
                'mobile'           => $request->mobile,
                'email'            => $request->email,
                'nida_number'      => $request->nida_number,
                'salary_group_id'  => $request->salary_group_id,
                'designation_id'   => $request->designation_id,
                'department_id'    => $request->department_id,
                'branch_id'        => $request->branch_id,
                'contract_type_id' => $request->contract_type_id,
                'joining_date'     => $request->start_date,
                'cv_file'          => $cvPath,
                'nida_file'        => $nidaPath,
                'contract_file'    => $contractPath,
                'created_by'       => Auth::id(),
            ]);

            Contract::create([
                'employee_id'       => $employee->id,
                'salary_group_id'   => $request->salary_group_id,
                'contract_type_id'  => $request->contract_type_id,
                'contract_duration' => $request->contract_duration,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'contract_file'     => $contractPath,
                'cv_file'           => $cvPath,
                'nida_file'         => $nidaPath,
            ]);

            DB::commit();

            return redirect()->back()->with('message', 'Employee added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->deleteFiles($storedFiles);
            Log::error('Failed to save employee: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->withInput()
                ->with('error', 'Failed to save employee: ' . $e->getMessage());
        }
    }

    /**
     * Update employee + contract.
     */
    public function updateEmployee(Request $request, $id)
    {
        $request->validate([
            'firstname'        => 'required|string|max:100',
            'surname'          => 'required|string|max:100',
            'email'            => 'nullable|email|max:150',
            'start_date'       => 'nullable|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'cv_file'          => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'nida_file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'contract_file'    => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $newFiles = [];
        try {
            $employee  = Employee::findOrFail($id);
            $safeName  = Str::slug(trim($request->firstname . '-' . $request->surname));
            $timestamp = now()->format('Ymd_His');

            $cvPath       = $this->replaceFile($request, 'cv_file',       $employee->cv_file,       'documents/cv',       'cv',       $safeName, $timestamp, $newFiles);
            $nidaPath     = $this->replaceFile($request, 'nida_file',     $employee->nida_file,     'documents/nida',     'nida',     $safeName, $timestamp, $newFiles);
            $contractPath = $this->replaceFile($request, 'contract_file', $employee->contract_file, 'documents/contract', 'contract', $safeName, $timestamp, $newFiles);

            DB::beginTransaction();

            $employee->update([
                'firstname'        => $request->firstname,
                'middlename'       => $request->middlename,
                'surname'          => $request->surname,
                'mobile'           => $request->mobile,
                'email'            => $request->email,
                'nida_number'      => $request->nida_number,
                'salary_group_id'  => $request->salary_group_id,
                'designation_id'   => $request->designation_id,
                'department_id'    => $request->department_id,
                'branch_id'        => $request->branch_id,
                'contract_type_id' => $request->contract_type_id,
                'joining_date'     => $request->start_date,
                'cv_file'          => $cvPath,
                'nida_file'        => $nidaPath,
                'contract_file'    => $contractPath,
            ]);

            $contract = Contract::firstOrNew(['employee_id' => $employee->id]);
            $contract->fill([
                'salary_group_id'   => $request->salary_group_id,
                'contract_type_id'  => $request->contract_type_id,
                'contract_duration' => $request->contract_duration,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'contract_file'     => $contractPath,
                'cv_file'           => $cvPath,
                'nida_file'         => $nidaPath,
            ])->save();

            DB::commit();

            return redirect()->back()->with('message', 'Employee updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->deleteFiles($newFiles);
            Log::error('Failed to update employee: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->withInput()
                ->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    /**
     * AJAX filter — returns partial table rows.
     */
    public function filter(Request $request)
    {
        $employees = $this->buildEmployeeQuery($request);

        return view('employees.partials.employee_table', compact('employees'))->render();
    }

    /**
     * Generate PDF for filtered employees.
     */
    public function generatePdf(Request $request)
    {
        $employees = $this->buildEmployeeQuery($request);

        $branchName = 'All Branches';
        if ($request->filled('branch_id') && ($b = Branch::find($request->branch_id))) {
            $branchName = $b->branch_name;
        }

        $contractName = 'All Contract Types';
        if ($request->filled('contract_type_id') && ($c = ContractType::find($request->contract_type_id))) {
            $contractName = $c->name;
        }

        $pdf = Pdf::loadView('employees.pdf.employee_list', [
            'employees'    => $employees,
            'branchName'   => $branchName,
            'contractName' => $contractName,
            'generatedAt'  => now()->format('d M, Y H:i A'),
            'generatedBy'  => Auth::user()->name ?? 'System',
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Employees_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Edit form (dedicated page fallback).
     */
   public function edit(Employee $employee)
{
    $employee->load([
        'department',
        'designation',
        'salaryGroup',
        'contract',
        'contractType',
    ]);

    $departments    = Department::orderBy('name')->get();
    $designations   = Designation::orderBy('name')->get();
    $contractTypes  = ContractType::orderBy('name')->get();
    $contract_types = $contractTypes; // keep both spellings for backwards compatibility
    $branches       = Branch::orderBy('branch_name')->get();

    $salaryGroups   = SalaryGroup::when(
        Auth::user()->branch_id,
        fn ($q) => $q->where('branch_id', Auth::user()->branch_id)
    )->orderBy('group_name')->get();

    return view('employees.edit', compact(
        'employee',
        'departments',
        'designations',
        'contractTypes',
        'contract_types',   // ← the missing variable
        'branches',
        'salaryGroups'
    ));
}

    /**
     * Simple update (used by employees.edit).
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'firstname'    => 'required|string|max:100',
            'middlename'   => 'nullable|string|max:100',
            'surname'      => 'required|string|max:100',
            'email'        => 'required|email|unique:employees,email,' . $employee->id,
            'basic_salary' => 'nullable|numeric',
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')->with('message', 'Employee updated successfully.');
    }

    /**
     * Delete employee.
     */
    public function destroy(Employee $employee)
    {
        // Optionally clean files
        foreach (['cv_file', 'nida_file', 'contract_file'] as $field) {
            if ($employee->$field) {
                Storage::disk('public')->delete($employee->$field);
            }
        }
        $employee->delete();

        return redirect()->route('employees.index')->with('message', 'Employee deleted.');
    }

    /**
     * Employee details page.
     */
    public function getEmployeeDetails($id)
    {
        $banks     = Bank::all();
        $employee  = Employee::with([
            'department', 'designation', 'salaryGroup',
            'contract', 'contractType', 'bankAccount', 'contributions',
        ])->findOrFail($id);

        $nssf = DB::table('nssf_details')
            ->join('employees', 'employees.id', '=', 'nssf_details.employee_id')
            ->where('nssf_details.employee_id', $id)
            ->select('nssf_details.*')
            ->get();

        $contributions = Contribution::all();

        return view('employees.employee-details', compact('employee', 'banks', 'contributions', 'nssf'));
    }

    /**
     * Edit employee details page.
     */
    public function editEmployeeDetails($id)
    {
        $employee = Employee::with([
            'department', 'designation', 'salaryGroup',
            'contract', 'contractType', 'bankAccount', 'contributions',
        ])->findOrFail($id);

        $designations  = Designation::all();
        $departments   = Department::all();
        $contractTypes = ContractType::all();
        $branches      = Branch::all();
        $salaryGroups  = SalaryGroup::where('branch_id', Auth::user()->branch_id)->get();

        return view('employees.edit-employee-details', compact(
            'employee', 'designations', 'salaryGroups',
            'branches', 'departments', 'contractTypes'
        ));
    }

    /**
     * JSON: contributions assigned to employee.
     */
    public function getContributions(Employee $employee)
    {
        $contributions = $employee->contributions()
            ->select('contributions.id', 'contributions.name', 'contributions.type', 'contributions.rate')
            ->get();

        return response()->json($contributions);
    }

    /* -----------------------------------------------------------------
     |  PRIVATE HELPERS
     | ----------------------------------------------------------------- */

    /**
     * Base query used by index / filter / pdf.
     * Empty branch_id or contract_type_id = ALL.
     */
    private function buildEmployeeQuery(Request $request)
    {
        $query = Employee::query()
            ->leftJoin('salary_groups',  'salary_groups.id',  '=', 'employees.salary_group_id')
            ->leftJoin('branches',       'branches.id',       '=', 'employees.branch_id')
            ->leftJoin('designations',   'designations.id',   '=', 'employees.designation_id')
            ->leftJoin('departments',    'departments.id',    '=', 'employees.department_id')
            ->leftJoin('contract_types', 'contract_types.id', '=', 'employees.contract_type_id')
            ->select(
                'employees.id',
                'employees.firstname',
                'employees.middlename',
                'employees.surname',
                'employees.email',
                'employees.photo',
                'employees.mobile',
                'employees.joining_date',
                'employees.branch_id as branch_id',
                'departments.name    as department_name',
                'designations.name   as position',
                'contract_types.name as contract_name',
                'salary_groups.group_name',
                'salary_groups.id    as salary_id',
                'branches.branch_name'
            );

        if ($request->filled('branch_id')) {
            $query->where('employees.branch_id', $request->branch_id);
        }

        if ($request->filled('contract_type_id')) {
            $query->where('employees.contract_type_id', $request->contract_type_id);
        }

        return $query
            ->orderBy('employees.firstname')
            ->orderBy('employees.surname')
            ->get();
    }

    /**
     * Store uploaded file and remember path for rollback.
     */
    private function storeFile(Request $request, string $field, string $dir, string $prefix, string $safeName, string $timestamp, array &$storedFiles): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }
        $ext      = $request->file($field)->getClientOriginalExtension();
        $filename = "{$prefix}_{$safeName}_{$timestamp}.{$ext}";
        $path     = $request->file($field)->storeAs($dir, $filename, 'public');
        $storedFiles[] = $path;

        return $path;
    }

    /**
     * Replace uploaded file, deleting old one after new is stored.
     */
    private function replaceFile(Request $request, string $field, ?string $currentPath, string $dir, string $prefix, string $safeName, string $timestamp, array &$newFiles): ?string
    {
        if (! $request->hasFile($field)) {
            return $currentPath;
        }
        $ext      = $request->file($field)->getClientOriginalExtension();
        $filename = "{$prefix}_{$safeName}_{$timestamp}.{$ext}";
        $path     = $request->file($field)->storeAs($dir, $filename, 'public');
        $newFiles[] = $path;

        if ($currentPath) {
            Storage::disk('public')->delete($currentPath);
        }

        return $path;
    }

    /**
     * Delete an array of stored paths (rollback).
     */
    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $p) {
            if ($p) {
                Storage::disk('public')->delete($p);
            }
        }
    }
}