<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Contribution;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use PDF;
use App\Models\PayrollContribution;
use App\Models\Loan;
use App\Models\SalaryAdvance;
use App\Models\LoanRepayment;
use App\Models\AssignedEmployeeContribution;
use App\Models\SalaryGroup;


class PayrollController extends Controller
{
    public function index()
{
    $branchId = Auth::user()->branch_id;

    $employees = DB::table('employees')
        ->select(
            'employees.id', 'employees.firstname', 'employees.middlename', 'employees.surname',
            'employees.email', 'employees.mobile', 'employees.joining_date',
            'designations.name as position',
            'salary_groups.group_name', 'branches.branch_name',
            'salary_groups.id as salary_id', 'branches.id as branch_id',
            'salary_groups.basic_salary'
        )
        ->leftJoin('salary_groups', 'salary_groups.id', 'employees.salary_group_id')
        ->leftJoin('branches', 'branches.id', 'employees.branch_id')
        ->leftJoin('designations', 'designations.id', 'employees.designation_id')
        ->where('employees.branch_id', $branchId)
        ->get();
     
    $contributions = Contribution::all();

    $branches = Branch::where('id', $branchId)->get();

    $payrolls = Payroll::with('employee')
        ->where('payrolls.branch_id', $branchId)
        ->orderBy('payrolls.month', 'desc')
        ->get();

    $totalEmployees = Employee::where('branch_id', $branchId)->count();
    $salaryGroupsCount = Contribution::count();

    $currentMonth = now()->format('Y-m');

    $currentMonthPayroll_1 = Payroll::where('month', $currentMonth)
        ->where('branch_id', $branchId)
        ->sum('basic_salary');

     $currentMonthPayroll_2 = Payroll::where('month', $currentMonth)
        ->where('branch_id', $branchId)
        ->sum('allowance');

        // return $currentMonthPayroll_1;

     $currentMonthPayroll = $currentMonthPayroll_1 + $currentMonthPayroll_2;
     
    $workingDaysPerMonth = now()->daysInMonth;
    $presentDays = Attendance::whereMonth('date', now()->month)
        ->where('branch_id', $branchId)
        ->where('status', 'present')
        ->count();

    $denominator = $totalEmployees * $workingDaysPerMonth;
    $attendanceRate = $denominator > 0 ? round(($presentDays / $denominator) * 100, 2) : 0;

    $lowAttendanceAlerts = Attendance::select('employee_id')
        ->whereMonth('date', now()->month)
        ->where('branch_id', $branchId)
        ->where('status', 'absent')
        ->groupBy('employee_id')
        ->havingRaw('COUNT(*) > ?', [3])
        ->with('employee')
        ->get()
        ->map(function ($row) {
            return (object)[
                'employee' => $row->employee,
                'absences' => Attendance::where('employee_id', $row->employee_id)
                    ->whereMonth('date', now()->month)
                    ->where('status', 'absent')
                    ->count()
            ];
        });

    // Loop through all months of the current year
    $currentYear = now()->year;
    $currentMonthNum = now()->month;
    $months = collect();

    // Cache loans & advances for performance
    $loans = Loan::with(['employee', 'loanRepayments'])->get();
    $salary_advances = SalaryAdvance::with('employee')->get();
    
    for ($m = $currentMonthNum; $m >= 1; $m--) {
        $monthStr = Carbon::createFromDate($currentYear, $m, 1)->format('Y-m');

        $payrollsByMonth = Payroll::where('month', $monthStr)
            ->where('branch_id', $branchId)
            ->with(['employee', 'contributions.contribution'])
            ->get();

        if ($payrollsByMonth->isEmpty()) {
            $months->push([
                'month' => $monthStr,
                'exists' => false,
                'paid_employees' => 0,
                'total_amount' => 0,
                'nssf' => 0,
                'nhif' => 0,
                'wcf' => 0,
                'tuico' => 0,
                'paye' => 0,
                'net_paid' => 0,
                'id' => null,
                'reference' => null,
            ]);
            continue;
        }

        $totalGrossAmount = 0;
        $totalNssf = 0;
        $totalNhif = 0;
        $totalWcf = 0;
        $totalTuico = 0;
        $totalNetPaid = 0;

        foreach ($payrollsByMonth as $payroll) {
            $basic = floatval($payroll->basic_salary);
            $allowance = floatval($payroll->allowance);
            $gross = $basic + $allowance;

            $nssf = $wcf = $nhif = $tuico = 0;

            foreach ($payroll->contributions as $p) {
                $contribution = $p->contribution;
                if ($contribution) {
                    $type = strtolower($contribution->type);
                    $name = strtoupper($contribution->name);
                    $amount = ($type === 'fixed')
                        ? floatval($contribution->rate)
                        : (($type === 'percentage') ? (floatval($contribution->rate) / 100) * $basic : 0);

                    switch ($name) {
                        case 'NSSF': $nssf = $amount; break;
                        case 'WCF': $wcf = $amount; break;
                        case 'NHIF': $nhif = $amount; break;
                        case 'TUICO': $tuico = $amount; break;
                    }
                }
            }

            // Salary Advance
            $advance_pay = $salary_advances
                ->where('employee_id', $payroll->employee_id)
                ->where('month', $monthStr)
                ->sum('amount');

            // Loan Repayments
            $loan_deductions = $loans
                ->where('employee_id', $payroll->employee_id)
                ->flatMap(function ($loan) use ($monthStr) {
                    return $loan->loanRepayments->where('deduction_date', $monthStr);
                })
                ->sum('amount');

            $total_deductions = $nssf + $wcf + $nhif + $tuico + $advance_pay + $loan_deductions;
            $net = $gross - $total_deductions;

            $totalGrossAmount += $gross;
            $totalNssf += $nssf;
            $totalNhif += $nhif;
            $totalWcf += $wcf;
            $totalTuico += $tuico;

            $totalNetPaid += $net;
        }

        $months->push([
            'month' => $monthStr,
            'exists' => true,
            'paid_employees' => $payrollsByMonth->count(),
            'total_amount' => $totalGrossAmount,
            'nssf' => $totalNssf,
            'nhif' => $totalNhif,
            'wcf' => $totalWcf,
            'tuico' => $totalTuico,
            'net_paid' => $totalNetPaid,
            'id' => $payrollsByMonth->first()->id ?? null,
            'reference' => $payrollsByMonth->first()->reference ?? null,
        ]);
    }

    return view('payrolls.index', compact(
        'totalEmployees',
        'salaryGroupsCount',
        'currentMonthPayroll',
        'attendanceRate',
        'payrolls',
        'contributions',
        'branches',
        'months',
        'employees',
        'loans',
        'salary_advances'
    ));
}


    public function create()
    {
        return view('payrolls.create');
    }

    public function store(Request $request)
    {
        $month = $request->input('month');
        $employees = $request->input('employees');
        $loanDeductions = [];
    
        foreach ($employees as $employeeId => $data) {
            if (!isset($data['selected'])) continue;
    
            $basicSalary = $data['basic_salary'];
            $allowance = $data['allowance'] ?? 0;
            $gross_salary = $basicSalary + $allowance;
            $deduction = 0;
    
            // Fetch loan for selected employee
                $loan = DB::table('loans')
                    ->where('employee_id', $employeeId)
                    // ->where('status', 'active')
                    ->where('balance', '>', 0)
                    ->orderBy('id', 'desc')
                    ->first();
    
                if ($loan) {
                    $deductAmount = min($loan->monthly_deduction, $loan->balance);
                    $deduction += $deductAmount;
    
                    DB::table('loans')->where('id', $loan->id)->update([
                        'balance' => $loan->balance - $deductAmount,
                        'status' => ($loan->balance - $deductAmount) <= 0 ? 'paid' : 'active',
                    ]);
    
                    DB::table('loan_repayments')->insert([
                        'loan_id'       => $loan->id,
                        'employee_id'   => $employeeId,
                        'amount'        => $deductAmount,
                        'deduction_date' => $month, // assumes YYYY-MM format input
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);

                    $loanDeductions[] = [
                        'employee_id' => $employeeId,
                        'loan_id' => $loan->id,
                        'deducted' => $deductAmount,
                    ];
                }
            // }
    
            // 2. SALARY ADVANCE Deduction
            $advance = DB::table('salary_advances')
                ->where('employee_id', $employeeId)
                ->where('month', $month)
                ->first();

            if ($advance) {
                $deduction += $advance->amount;

                // Optional: mark it as deducted if you add a `status` column
                DB::table('salary_advances')->where('id', $advance->id)->update([
                    'status' => 'deducted',
                    'updated_at' => now()
                ]);

                $advanceDeductions[] = [
                    'employee_id' => $employeeId,
                    'amount' => $advance->amount,
                ];
            }
        // }

        $nssf = 0;

        // Check if 'NSSF' is selected in contributions
        if (!empty($data['contributions']) && in_array('1', array_map('strtolower', $data['contributions']))) {
            // Assume 10% NSSF on gross salary as an example
            $nssf = 0.10 * $gross_salary;
        }

        // 4. Calculate TAXABLE INCOME (only NSSF is deductible)
        $taxable_income = $gross_salary - $nssf;

        // 5. PAYE
        if ($taxable_income <= 270000) {
            $paye = 0 ;
        } elseif ($taxable_income <= 520000) {
            $paye = ($taxable_income - 270000) * 0.08;
        } elseif ($taxable_income <= 760000) {
            $paye = 20000 + ($taxable_income - 520000) * 0.20;
        } elseif ($taxable_income <= 1000000) {
            $paye = 68000 + ($taxable_income - 760000) * 0.25;
        } else {
            $paye = 128000 + ($taxable_income - 1000000) * 0.30;
        }

            $netSalary = ($basicSalary + $allowance) - $deduction;
    
            $payroll = Payroll::create([
                'employee_id' => $employeeId,
                'branch_id' => Auth::user()->branch_id,
                'reference' => date('YhisH'),
                'month' => $month,
                'basic_salary' => $basicSalary,
                'allowance' => $allowance,
                'paye' => $paye,
                'net_salary' => $netSalary,
            ]);
    
            if (!empty($data['contributions'])) {
                foreach ($data['contributions'] as $contribName) {
                    PayrollContribution::create([
                        'payroll_id' => $payroll->id,
                        'contribution_id' => $contribName,
                        'name' => $contribName,
                        'rate' => null,
                    ]);
                }
            }
        }
    

        return redirect()->back()->with('message', 'Payroll generated.');

    }
    

public function store11(Request $request)
{
    $request->validate([
        'month' => 'required|date_format:Y-m',
        'employees' => 'required|array',
    ]);

    foreach ($request->employees as $id => $data) {
        if (!isset($data['selected'])) continue;

        Payroll::create([
            'employee_id' => $id,
            'branch_id' => Auth::user()->branch_id,
            'reference' => date('YhisH'),
            'month' => $request->month,
            'basic_salary' => $data['basic_salary'],
            'allowance' => $data['allowance'] ?? 0,
            'deduction' => $data['deduction'] ?? 0,
            'description' => $data['description'] ?? null,
            'net_salary' => ($data['basic_salary'] + ($data['allowance'] ?? 0)) - ($data['deduction'] ?? 0),
        ]);
    }

    return redirect()->route('payrolls.index')->with('success', 'Payroll generated successfully!');
}

    public function saveSalaryGroup(Request $request)
{
    $request->validate([
        'group_name' => 'required|string|max:100',
        'basic_salary' => 'required|numeric|min:0',
        'allowance' => 'nullable|numeric|min:0',
        'deductions' => 'nullable|numeric|min:0',
    ]);

    SalaryGroup::create($request->only('branch_id', 'group_name', 'basic_salary', 'allowance', 'deductions'));

    return redirect()->back()->with('success', 'Salary group added successfully!');
}

public function show() {

    return "payslip";
}

public function fetchSalarySlip(Request $request)
{
    $employeeId = $request->employee_id;
    $monthRaw = $request->month; // e.g., '2025-01'

    if (!$employeeId || !$monthRaw) {
        return response()->json([
            'success' => false,
            'message' => 'Employee ID or Month is missing.'
        ]);
    }

    // Get the payroll record for the specific employee and month
    $payroll = Payroll::where('employee_id', $employeeId)
        ->where('month', $monthRaw)
        ->with('employee')
        ->first();


    if (!$payroll) {
        return response()->json([
            'success' => false,
            'message' => 'No payroll data found for selected employee and month.'
        ]);
    }

    $month = \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y');
    $monthRaw_2 = \Carbon\Carbon::parse($request->month)->format('Y-m');

    // Load contributions for this payroll
    $contributions = PayrollContribution::where('payroll_id', $payroll->id)
        ->with(['payroll.employee', 'contribution'])
        ->get();

    $employeeId = $payroll->employee_id;

    
    // Get loan repayments for the employee for this month
    $loanRepayments = LoanRepayment::where('employee_id', $employeeId)
        ->where('deduction_date', $monthRaw_2)
        ->get()
        ->groupBy('employee_id');

    // Get salary advances for the employee for this month
    $salaryAdvances = DB::table('salary_advances')
        ->where('employee_id', $employeeId)
        ->where('month', $monthRaw_2)
        ->get()
        ->groupBy('employee_id');

    // Group contributions by employee ID
    $grouped = $contributions->groupBy(function ($item) {
        return $item->payroll->employee_id ?? 0;
    });

    $slips = $grouped->map(function ($rows, $employeeId) use ($loanRepayments, $salaryAdvances) {
        $first = $rows->first();

        $basic_salary = floatval($first->payroll->basic_salary ?? 0);
        $allowance = floatval($first->payroll->allowance ?? 0);
        $paye = floatval($first->payroll->paye ?? 0);

        $nssf = 0;
        $wcf = 0;
        $nhif = 0;
        $tuico = 0;

        foreach ($rows as $p) {
            $contribution = $p->contribution;
            if ($contribution) {
                $type = strtolower($contribution->type);
                $name = strtoupper($contribution->name);
                $amount = 0;

                if ($type === 'fixed') {
                    $amount = floatval($contribution->rate);
                } elseif ($type === 'percentage') {
                    $rate = floatval($contribution->rate);
                    $amount = ($rate / 100) * $basic_salary;
                }

                switch ($name) {
                    case 'NSSF': $nssf = $amount; break;
                    case 'WCF': $wcf = $amount; break;
                    case 'NHIF': $nhif = $amount; break;
                    case 'TUICO': $tuico = $amount; break;
                }
            }
        }

        $advance_pay = $salaryAdvances->has($employeeId)
            ? $salaryAdvances[$employeeId]->sum('amount')
            : 0;

        $loan = $loanRepayments->has($employeeId)
            ? $loanRepayments[$employeeId]->sum('amount')
            : 0;

        $total_deductions = $nssf + $wcf + $nhif + $tuico + $advance_pay + $loan + $paye;
        $net_salary = ($basic_salary + $allowance) - $total_deductions;

        
        return [
            'employee_name' => isset($first->payroll->employee)
                ? $first->payroll->employee->firstname . ' ' .
                  $first->payroll->employee->middlename . ' ' .
                  $first->payroll->employee->surname
                : 'N/A',
            'basic_salary' => number_format($basic_salary, 2),
            'allowance' => number_format($allowance, 2),
            'salary_advance' => number_format($advance_pay, 2),
            'nssf' => number_format($nssf, 2),
            'wcf' => number_format($wcf, 2),
            'nhif' => number_format($nhif, 2),
            'tuico' => number_format($tuico, 2),
            'loan' => number_format($loan, 2),
            'paye' => number_format($paye, 2),
            'net_salary' => number_format($net_salary, 2),
            'reference' => $first->payroll->reference ?? 'N/A',
        ];
    })->values();

    return response()->json([
        'success' => true,
        'month' => $month,
        'slips' => $slips
    ]);
}


public function downloadSalarySlip(Request $request)
{
    $settings = DB::table('general_settings')
        ->select('business_name')
        ->first(); // use first() instead of get()

        $employeeId = $request->employee_id;
        $monthRaw = $request->month; // e.g., '2025-01'
    
        if (!$employeeId || !$monthRaw) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ID or Month is missing.'
            ]);
        }
    
        // Get the payroll record for the specific employee and month
        $payroll = Payroll::where('employee_id', $employeeId)
            ->where('month', $monthRaw)
            ->with('employee')
            ->first();
    
    
        if (!$payroll) {
            return response()->json([
                'success' => false,
                'message' => 'No payroll data found for selected employee and month.'
            ]);
        }
    
        $month = \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y');
        $monthRaw_2 = \Carbon\Carbon::parse($request->month)->format('Y-m');
    
        // Load contributions for this payroll
        $contributions = PayrollContribution::where('payroll_id', $payroll->id)
            ->with(['payroll.employee', 'contribution'])
            ->get();
    
        $employeeId = $payroll->employee_id;
    
        
        // Get loan repayments for the employee for this month
        $loanRepayments = LoanRepayment::where('employee_id', $employeeId)
            ->where('deduction_date', $monthRaw_2)
            ->get()
            ->groupBy('employee_id');
    
        // Get salary advances for the employee for this month
        $salaryAdvances = DB::table('salary_advances')
            ->where('employee_id', $employeeId)
            ->where('month', $monthRaw_2)
            ->get()
            ->groupBy('employee_id');
    
        // Group contributions by employee ID
        $grouped = $contributions->groupBy(function ($item) {
            return $item->payroll->employee_id ?? 0;
        });
    
        $slips = $grouped->map(function ($rows, $employeeId) use ($loanRepayments, $salaryAdvances) {
            $first = $rows->first();
    
            $basic_salary = floatval($first->payroll->basic_salary ?? 0);
            $allowance = floatval($first->payroll->allowance ?? 0);
            $paye = floatval($first->payroll->paye ?? 0);

            $nssf = 0;
            $wcf = 0;
            $nhif = 0;
            $tuico = 0;
    
            foreach ($rows as $p) {
                $contribution = $p->contribution;
                if ($contribution) {
                    $type = strtolower($contribution->type);
                    $name = strtoupper($contribution->name);
                    $amount = 0;
    
                    if ($type === 'fixed') {
                        $amount = floatval($contribution->rate);
                    } elseif ($type === 'percentage') {
                        $rate = floatval($contribution->rate);
                        $amount = ($rate / 100) * $basic_salary;
                    }
    
                    switch ($name) {
                        case 'NSSF': $nssf = $amount; break;
                        case 'WCF': $wcf = $amount; break;
                        case 'NHIF': $nhif = $amount; break;
                        case 'TUICO': $tuico = $amount; break;
                    }
                }
            }
    
            $advance_pay = $salaryAdvances->has($employeeId)
                ? $salaryAdvances[$employeeId]->sum('amount')
                : 0;
    
            $loan = $loanRepayments->has($employeeId)
                ? $loanRepayments[$employeeId]->sum('amount')
                : 0;
    
            $total_deductions = $nssf + $wcf + $nhif + $tuico + $advance_pay + $loan + $paye;
            $net_salary = ($basic_salary + $allowance) - $total_deductions;
    
            
            return [
                'employee_name' => isset($first->payroll->employee)
                    ? $first->payroll->employee->firstname . ' ' .
                      $first->payroll->employee->middlename . ' ' .
                      $first->payroll->employee->surname
                    : 'N/A',
                'basic_salary' => $basic_salary,
                'allowance' => $allowance,
                'salary_advance' => $advance_pay,
                'nssf' => $nssf,
                'wcf' => $wcf,
                'nhif' => $nhif,
                'tuico' => $tuico,
                'loan' => $loan,
                'paye' => $paye,
                'net_salary' => $net_salary,
                'reference' => $first->payroll->reference ?? 'N/A',
                'employee_id' => $first->payroll->employee->id
            ];
        })->values();
        
    // Generate PDF
    $pdf = PDF::loadView('payrolls.slip_pdf', [
        'slip' => $slips[0],
        'settings' => $settings,
        'month' => $month
    ]);
    
    return $pdf->download("Salary_Slip_{$payroll->employee->name}_{$month}.pdf");
}

public function details($reference) 
{
    // Get all payroll IDs with the same reference
    $payrollIds = Payroll::where('reference', $reference)->pluck('id');
    
    if ($payrollIds->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No data found.']);
    }

    // Get main payroll for date/month info
    $mainPayroll = Payroll::find($payrollIds->first());
    $month = \Carbon\Carbon::parse($mainPayroll->month)->format('F Y');
    $month_repayment = \Carbon\Carbon::parse($mainPayroll->month)->format('Y-m');

    // Load contributions
    $contributions = PayrollContribution::whereIn('payroll_id', $payrollIds)
        ->with(['payroll.employee', 'contribution'])
        ->get();
    $contributions1 = Payroll::leftJoin('payroll_contributions', 'payrolls.id', '=', 'payroll_contributions.payroll_id')
    ->leftJoin('contributions', 'payroll_contributions.contribution_id', '=', 'contributions.id')
    ->leftJoin('employees', 'payrolls.employee_id', '=', 'employees.id')
    ->where('payrolls.reference', $reference)
    ->select(
        'payrolls.id as payroll_id',
        'payrolls.month',
        'employees.id as employee_id',
        'employees.firstname as employee_name',
        'payroll_contributions.id as contribution_id',
        'contributions.name as contribution_name',
        'payroll_contributions.amount'
    )
    ->get();

    // Get employee IDs for those payrolls
    $employeeIds = Payroll::whereIn('id', $payrollIds)->pluck('employee_id');

    // Get loan repayments for all employees in this payroll batch and for the same month
    $loanRepayments = LoanRepayment::whereIn('employee_id', $employeeIds)
        ->where('deduction_date', $month_repayment)
        ->get()
        ->groupBy('employee_id');

    // Get salary advances for this month
    $salaryAdvances = DB::table('salary_advances')
            ->whereIn('employee_id', $employeeIds)
            ->where('month', $month_repayment)
            ->get()
            ->groupBy('employee_id');
        
    // Group contributions by employee
    $grouped = $contributions->groupBy(function ($item) {
        return $item->payroll->employee_id ?? 0;
    });

    $slips = $grouped->map(function ($rows, $employeeId) use ($loanRepayments, $salaryAdvances) {
        $first = $rows->first();

        $basic_salary = floatval($first->payroll->basic_salary ?? 0);
        $allowance = floatval($first->payroll->allowance ?? 0);
        $paye = floatval($first->payroll->paye ?? 0);

        $gross_salary = $basic_salary + $allowance;

        $nssf = 0;
        $wcf = 0;
        $nhif = 0;
        $tuico = 0;
        $advance_pay = 0;
        $loan = 0;

        foreach ($rows as $p) {
            $contribution = $p->contribution;
            if ($contribution) {
                $type = strtolower($contribution->type);
                $name = strtoupper($contribution->name);
                $amount = 0;

                if ($type === 'fixed') {
                    $amount = floatval($contribution->rate);
                } elseif ($type === 'percentage') {
                    $rate = floatval($contribution->rate);
                    $amount = ($rate / 100) * $gross_salary;
                }

                switch ($name) {
                    case 'NSSF': $nssf = $amount; break;
                    case 'WCF': $wcf = $amount; break;
                    case 'NHIF': $nhif = $amount; break;
                    case 'TUICO': $tuico = $amount; break;
                }
            }
        }

       // Get salary advance for this employee
    $advance_pay = $salaryAdvances->has($employeeId)
    ? $salaryAdvances[$employeeId]->sum('amount')
    : 0;

// Get loan repayment for this employee
    $loan = $loanRepayments->has($employeeId)
    ? $loanRepayments[$employeeId]->sum('amount')
    : 0;

        $net_salary = ($basic_salary + $allowance) - ($nssf + $wcf + $nhif + $tuico + $advance_pay + $loan + $paye);

        return [
            'employee_name' => isset($first->payroll->employee)
                ? $first->payroll->employee->firstname . ' ' .
                  $first->payroll->employee->middlename . ' ' .
                  $first->payroll->employee->surname
                : 'N/A',
            'basic_salary' => number_format($basic_salary, 2),
            'allowance' => number_format($allowance, 2),
            'salary_advance' => number_format($advance_pay, 2),
            'nssf' => number_format($nssf, 2),
            'wcf' => number_format($wcf, 2),
            'nhif' => number_format($nhif, 2),
            'tuico' => number_format($tuico, 2),
            'loan' => number_format($loan, 2), 
            'paye' => number_format($paye, 2), 
            'net_salary' => number_format($net_salary, 2),
            'reference' => $first->payroll->reference ?? 'N/A',
        ];
    })->values();
    
    return response()->json([
        'success' => true,
        'month' => $month,
        'slips' => $slips
    ]);
}



public function downloadPayrollDetails($reference)
{

    $settings= DB::table('general_settings')
    ->select('business_name')
    ->get(); 

$payrollIds = Payroll::where('reference', $reference)->pluck('id');
    
if ($payrollIds->isEmpty()) {
    return response()->json(['success' => false, 'message' => 'No data found.']);
}

// Get main payroll for date/month info
$mainPayroll = Payroll::find($payrollIds->first());
$month = \Carbon\Carbon::parse($mainPayroll->month)->format('F Y');
$month_repayment = \Carbon\Carbon::parse($mainPayroll->month)->format('Y-m');

// Load contributions
$contributions = PayrollContribution::whereIn('payroll_id', $payrollIds)
    ->with(['payroll.employee', 'contribution'])
    ->get();

// Get employee IDs for those payrolls
$employeeIds = Payroll::whereIn('id', $payrollIds)->pluck('employee_id');

// Get loan repayments for all employees in this payroll batch and for the same month
$loanRepayments = LoanRepayment::whereIn('employee_id', $employeeIds)
    ->where('deduction_date', $month_repayment)
    ->get()
    ->groupBy('employee_id');

// Get salary advances for this month
$salaryAdvances = DB::table('salary_advances')
        ->whereIn('employee_id', $employeeIds)
        ->where('month', $month_repayment)
        ->get()
        ->groupBy('employee_id');
    
// Group contributions by employee
$grouped = $contributions->groupBy(function ($item) {
    return $item->payroll->employee_id ?? 0;
});

$slips = $grouped->map(function ($rows, $employeeId) use ($loanRepayments, $salaryAdvances) {
    $first = $rows->first();

    $basic_salary = floatval($first->payroll->basic_salary ?? 0);
    $allowance = floatval($first->payroll->allowance ?? 0);
    $paye = floatval($first->payroll->paye ?? 0);

    $gross_salary = $basic_salary + $allowance;

    $nssf = 0;
    $wcf = 0;
    $nhif = 0;
    $tuico = 0;
    $advance_pay = 0;
    $loan = 0;

    foreach ($rows as $p) {
        $contribution = $p->contribution;
        if ($contribution) {
            $type = strtolower($contribution->type);
            $name = strtoupper($contribution->name);
            $amount = 0;

            if ($type === 'fixed') {
                $amount = floatval($contribution->rate);
            } elseif ($type === 'percentage') {
                $rate = floatval($contribution->rate);
                $amount = ($rate / 100) * $gross_salary;
            }

            switch ($name) {
                case 'NSSF': $nssf = $amount; break;
                case 'WCF': $wcf = $amount; break;
                case 'NHIF': $nhif = $amount; break;
                case 'TUICO': $tuico = $amount; break;
            }
        }
    }

   // Get salary advance for this employee
$advance_pay = $salaryAdvances->has($employeeId)
? $salaryAdvances[$employeeId]->sum('amount')
: 0;

// Get loan repayment for this employee
$loan = $loanRepayments->has($employeeId)
? $loanRepayments[$employeeId]->sum('amount')
: 0;

    $net_salary = ($basic_salary + $allowance) - ($nssf + $wcf + $nhif + $tuico + $advance_pay + $loan + $paye);

    return [
        'employee_name' => isset($first->payroll->employee)
            ? $first->payroll->employee->firstname . ' ' .
              $first->payroll->employee->middlename . ' ' .
              $first->payroll->employee->surname
            : 'N/A',
        'basic_salary'     => $basic_salary,
        'allowance'        => $allowance,
        'salary_advance'   => $advance_pay,
        'nssf'             => $nssf,
        'wcf'              => $wcf,
        'nhif'             => $nhif,
        'tuico'            => $tuico,
        'loan'             => $loan,
        'paye'             => $paye,
        'net_salary'       => $net_salary,
        'reference' => $first->payroll->reference ?? 'N/A',
    ];
})->values();

    $pdf = Pdf::loadView('payrolls.payrolls_details_pdf', [
        'slips' => $slips,
        'month' => $month,
        'settings' => $settings
    ]);

    return $pdf->download("Payroll-Details-{$month}.pdf");
}



public function bankDetails($reference)
{

    $payrollIds = Payroll::where('reference', $reference)->pluck('id');

    if ($payrollIds->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No data found.']);
    }

    // Get the first for month display
    $mainPayroll = Payroll::find($payrollIds->first());
    $month = \Carbon\Carbon::parse($mainPayroll->month)->format('F Y');

    // Load contributions for all payrolls in that batch
    $contributions = PayrollContribution::whereIn('payroll_id', $payrollIds)
    ->with([
        'payroll.employee.bankAccount.bank', // Load bankAccounts and nested bank
        'contribution'
    ])
    ->get();

    // Group by employee
    $grouped = $contributions->groupBy(function ($item) {
        return $item->payroll->employee_id ?? 0;
    });

    $slips = $grouped->map(function ($rows) {
        $first = $rows->first();
    
        $payroll = $first->payroll;
        $employee = $payroll->employee ?? null;
    
        $basic_salary = floatval($payroll->basic_salary ?? 0);
        $allowance = floatval($payroll->allowance ?? 0);
    
        $gross_salary = $basic_salary + $allowance;

        $nssf = $paye = $nhif = 0;
    
        foreach ($rows as $p) {
            $contribution = $p->contribution;
            if ($contribution) {
                $type = strtolower($contribution->type);
                $name = strtoupper($contribution->name);
                $amount = 0;
    
                if ($type === 'fixed') {
                    $amount = floatval($contribution->rate);
                } elseif ($type === 'percentage') {
                    $rate = floatval($contribution->rate);
                    $amount = ($rate / 100) * $gross_salary;
                }
    
                switch ($name) {
                    case 'NSSF': $nssf = $amount; break;
                    case 'PAYE': $paye = $amount; break;
                    case 'NHIF': $nhif = $amount; break;
                    case 'TUICO': $nhif = $amount; break;
                }
            }
        }
    
        $net_salary = ($basic_salary + $allowance) - ($nssf + $paye + $nhif + $tuico);
    
        // Bank account details
        $bankAccount = $employee?->bankAccount?->first(); // or use a loop if multiple
    
        return [
            'employee_name' =>  isset($first->payroll->employee)
                ? $first->payroll->employee->firstname . ' ' .
                  $first->payroll->employee->middlename . ' ' .
                  $first->payroll->employee->surname
                : 'N/A',
            'basic_salary' => number_format($basic_salary, 2),
            'allowance' => number_format($allowance, 2),
            'nssf' => number_format($nssf, 2),
            'paye' => number_format($paye, 2),
            'nhif' => number_format($nhif, 2),
            'net_salary' => number_format($net_salary, 2),
            'reference' => $payroll->reference ?? 'N/A',
    
            // bank info
            'bank_name' => $bankAccount?->bank?->name ?? 'N/A',
            'account_number' => $bankAccount?->account_number ?? 'N/A',
            'account_name' => $bankAccount?->account_name ?? 'N/A',
        ];
    })->values();
    
    return view('payrolls.bank_details', compact('slips'));
}

public function nssfVoucherView($reference)
{
    // Assume $reference is a month string like "August 2025"
    $month = $reference;

    $employees = Employee::with(['payrolls', 'nssfdetails'])
        ->whereHas('payrolls', function ($q) use ($month) {
            $q->where('month', $month);
        })
        ->get();

    if ($employees->isEmpty()) {
        return response()->json(['error' => 'No NSSF records found for the selected month.'], 404);
    }

    $slips = $employees->map(function ($emp) use ($month) {
        $payroll = $emp->payrolls->where('month', $month)->first();

        return [
            'firstname' => trim("{$emp->firstname}"),
            'middlename' => trim("{$emp->middlename}"),
            'surname' => trim("{$emp->surname}"),
            'nssf_no' => $emp->nssfdetails->member_number ?? $emp->member_number ?? 'N/A',
            'gross_salary' => ($payroll->basic_salary ?? 0) + ($payroll->allowance ?? 0),
        ];
    });

    return view('payrolls.nssf-details', compact('slips', 'month'));
}


public function tuicoVoucherView($reference)
{
    $month = $reference;


    // Load payrolls and nested payment_contributions with contribution
    $employees = Employee::with([
        'payrolls' => function ($q) use ($month) {
            $q->where('month', $month)->with([
                'payment_contributions.contribution'
            ]);
        }
    ])->whereHas('payrolls', function ($q) use ($month) {
        $q->where('month', $month);
    })->get();
    

    if ($employees->isEmpty()) {
        return response()->json(['error' => 'No TUICO records found for the selected month.'], 404);
    }

    $slips = $employees->map(function ($emp) use ($month) {
        $payroll = $emp->payrolls->where('month', $month)->first();
        $gross_salary = ($payroll->basic_salary ?? 0) + ($payroll->allowance ?? 0);

        $tuico = 0;

        foreach ($payroll->payment_contributions ?? [] as $p) {
            $contribution = $p->contribution;

            if ($contribution && strtoupper($contribution->name) === 'TUICO') {
                $type = strtolower($contribution->type);
                $rate = floatval($contribution->rate);

                if ($type === 'fixed') {
                    $tuico = $rate;
                } elseif ($type === 'percentage') {
                    $tuico = ($rate / 100) * $gross_salary;
                }
            }
        }

        return [
            'firstname' => trim($emp->firstname),
            'middlename' => trim($emp->middlename),
            'surname' => trim($emp->surname),
            'gross_salary' => $gross_salary,
            'tuico_amount' => $tuico,
        ];
    });

    return view('payrolls.tuico-details', compact('slips', 'month'));
}


public function generateNssfVoucher($month)
{
    $company = [
        'name' => 'Nduvini Autoworks Ltd',
        'address' => 'P.O. Box 1234, Dar es Salaam',
        'phone' => '+255 789 123 456',
        'tin' => '123-456-789',
        'nssf_number' => 'NSSF/00123456'
    ];

    // Example: Get payroll data for the month
    $employees = Employee::with('payrolls')
        ->whereHas('payrolls', function ($q) use ($month) {
            $q->where('month', $month);
        })
        ->get()
        ->map(function ($emp) use ($month) {
            $payroll = $emp->payrolls->where('month', $month)->first();
            return [
                'name' => $emp->name,
                'nssf_no' => $emp->nssf_number,
                'gross_salary' => $payroll->basic_salary + $payroll->allowance ?? 0
            ];
        });

    $pdf = Pdf::loadView('payment-voucher.nssf_voucher', compact('company', 'employees', 'month'))
            ->setPaper('A4', 'portrait');

    return $pdf->download("NSSF_Voucher_$month.pdf");
}


public function generateNhifVoucher($month)
{
    $company = [
        'name' => 'Nduvini Autoworks Ltd',
        'address' => 'P.O. Box 1234, Dar es Salaam',
        'phone' => '+255 789 123 456',
        'tin' => '123-456-789',
        'nhif_number' => 'NHIF/00123456'
    ];

    $employees = Employee::with('payrolls')
        ->whereHas('payrolls', function ($q) use ($month) {
            $q->where('month', $month);
        })
        ->get()
        ->map(function ($emp) use ($month) {
            $payroll = $emp->payrolls->where('month', $month)->first();
            return [
                'name' => $emp->name,
                'nhif_no' => $emp->nhif_number,
                'gross_salary' => $payroll->basic_salary + $payroll->allowance ?? 0
            ];
        });

    $pdf = Pdf::loadView('payment-voucher.nhif_voucher', compact('company', 'employees', 'month'))
        ->setPaper('A4', 'portrait');

    return $pdf->download("NHIF_Voucher_$month.pdf");
}



// public function generatePayeVoucher($month)
// {
//     $company = [
//         'name' => 'Nduvini AutoWorks Ltd',
//         'address' => 'P.O. Box 1234, Dar es Salaam',
//         'phone' => '+255 789 123 456',
//         'tin' => '123-456-789',
//     ];

//     $employees = Employee::with('payrolls')
//         ->whereHas('payrolls', function ($q) use ($month) {
//             $q->where('month', $month);
//         })
//         ->get()
//         ->map(function ($emp) use ($month) {
//             $payroll = $emp->payrolls->where('month', $month)->first();
//             $gross_salary = $payroll->basic_salary + $payroll->allowance ?? 0;

//             return [
//                 'name' => $emp->name,
//                 'gross_salary' => $gross_salary,
//                 'paye' => $this->calculatePaye($gross_salary),
//             ];
//         });

//     $pdf = Pdf::loadView('payment-voucher.paye_voucher', compact('company', 'employees', 'month'))
//             ->setPaper('A4', 'portrait');

//     return $pdf->download("PAYE_Voucher_$month.pdf");
// }

private function calculatePaye($grossSalary, $nssf = 0, $nhif = 0)
{

    $taxable = $grossSalary - ($nssf + $nhif);

    if ($taxable <= 270000) {
        return 0;
    } elseif ($taxable <= 520000) {
        return ($taxable - 270000) * 0.08;
    } elseif ($taxable <= 760000) {
        return 20000 + ($taxable - 520000) * 0.20;
    } elseif ($taxable <= 1000000) {
        return 68000 + ($taxable - 760000) * 0.25;
    } else {
        return 128000 + ($taxable - 1000000) * 0.30;
    }
}

public function generatePayeVoucher($month)
{
    $company = [
        'name' => 'Nduvini AutoWorks Ltd',
        'address' => 'P.O. Box 1234, Dar es Salaam',
        'phone' => '+255 789 123 456',
        'tin' => '123-456-789',
    ];

    $employees = Employee::with('payrolls')
        ->whereHas('payrolls', function ($q) use ($month) {
            $q->where('month', $month);
        })
        ->get()
        ->map(function ($emp) use ($month) {
            $payroll = $emp->payrolls->where('month', $month)->first();
            $gross = $payroll->basic_salary  ?? 0;

            // Calculate exemptions
            $nssf = $gross * 0.10;
            $nhif = $gross * 0.03;

            // Calculate PAYE using taxable income
            $paye = $this->calculatePaye($gross, $nssf, $nhif); // you may remove $nhif if not exempted

            return [
                'name' => $emp->name,
                'gross_salary' => $gross,
                'paye' => $paye,
            ];
        });

    $pdf = Pdf::loadView('payment-voucher.paye_voucher', compact('company', 'employees', 'month'))
        ->setPaper('A4', 'portrait');

    return $pdf->download("PAYE_Voucher_$month.pdf");
}



public function generateWcfVoucher($month)
{
    $company = [
        'name' => 'Nduvini AutoWorks Ltd',
        'address' => 'P.O. Box 1234, Dar es Salaam',
        'phone' => '+255 789 123 456',
        'tin' => '123-456-789',
    ];

    $employees = Employee::with('payrolls')
        ->whereHas('payrolls', function ($q) use ($month) {
            $q->where('month', $month);
        })
        ->get()
        ->map(function ($emp) use ($month) {
            $payroll = $emp->payrolls->where('month', $month)->first();
            $gross = $payroll->basic_salary ?? 0;

            $wcf = $gross * 0.01;
            $net_after_wcf = $gross - $wcf;

            return [
                'name' => $emp->name,
                'nida' => $emp->nida_number,
                'gross_salary' => $gross,
                'wcf' => $wcf,
                'net_after_wcf' => $net_after_wcf,
            ];
        });

    $pdf = Pdf::loadView('payment-voucher.wcf_voucher', compact('company', 'employees', 'month'))
        ->setPaper('A4', 'portrait');

    return $pdf->download("WCF_Voucher_$month.pdf");
}


public function rollbackPayroll($id)
{
    try {
        DB::beginTransaction();

        // Get the selected payroll
        $referencePayroll = Payroll::findOrFail($id);
        $employeeId = $referencePayroll->employee_id;
        $month = $referencePayroll->month;

        // Get all payrolls for the same employee and month
        $payrolls = Payroll::
            where('month', $month)
            ->get();

        // Delete contributions and payrolls
        foreach ($payrolls as $payroll) {
            PayrollContribution::where('payroll_id', $id)->delete();
            $payroll->delete();
        }

        // Delete loan repayments for the employee in that month
        LoanRepayment::
            where('deduction_date', $month)
            ->delete();

        DB::commit();

        return response()->json(['message' => true, 'message' => 'All payrolls and related data for this month deleted successfully.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to rollback all payrolls.',
            'error' => $e->getMessage()
        ], 500);
    }
   }

}
