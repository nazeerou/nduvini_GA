<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Contribution;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use App\Models\PayrollContribution;
use App\Models\Loan;
use App\Models\SalaryAdvance;
use App\Models\LoanRepayment;
use App\Models\PayrollRequest;
use App\Models\PayrollRequestEvent;
use App\Models\AssignedEmployeeContribution;
use App\Models\SalaryGroup;
    use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class PayrollController extends Controller
{
public function index()
{
    $branchId = Auth::user()->branch_id;
    $currentYear  = now()->year;
    $currentMonth = now()->format('Y-m');

    $employees = DB::table('employees')
        ->select(
            'employees.id', 'employees.firstname', 'employees.middlename', 'employees.surname',
            'employees.email', 'employees.mobile', 'employees.joining_date',
            'designations.name as position',
            'salary_groups.group_name', 'branches.branch_name',
            'salary_groups.id as salary_id', 'branches.id as branch_id',
            'salary_groups.basic_salary', 'salary_groups.allowance'
        )
        ->leftJoin('salary_groups', 'salary_groups.id', 'employees.salary_group_id')
        ->leftJoin('branches', 'branches.id', 'employees.branch_id')
        ->leftJoin('designations', 'designations.id', 'employees.designation_id')
        ->where('employees.branch_id', $branchId)
        ->get();

    $contributions = Contribution::all();
    $branches      = Branch::where('id', $branchId)->get();
    $salary_groups = SalaryGroup::where('branch_id', $branchId)->get();

    $payrolls = Payroll::with('employee')
        ->where('payrolls.branch_id', $branchId)
        ->orderBy('payrolls.month', 'desc')
        ->get();

    $totalEmployees    = Employee::where('branch_id', $branchId)->count();
    $salaryGroupsCount = Contribution::count();

    $currentMonthPayroll_1 = Payroll::where('month', $currentMonth)
        ->where('branch_id', $branchId)
        ->sum('basic_salary');

    $currentMonthPayroll_2 = Payroll::where('month', $currentMonth)
        ->where('branch_id', $branchId)
        ->sum('allowance');

    $currentMonthPayroll = $currentMonthPayroll_1 + $currentMonthPayroll_2;

    $months = collect();

    $loans = Loan::with(['employee', 'loanRepayments'])
        ->where('branch_id', $branchId)
        ->get();

    // Load ALL salary advances for the branch (not just current month) so that
    // each month's advance deduction can be computed accurately below.
    $salary_advances = SalaryAdvance::with('employee')
        ->where('branch_id', $branchId)
        ->get();

    /* ------------------------------------------------------------------
     |  Build the payroll summary for every month of the current year
     |  (January → December). Months without a payroll run become
     |  placeholder rows so the front-end can list all 12 months.
     | ------------------------------------------------------------------ */
    for ($m = 12; $m >= 1; $m--) {
        $monthStr = Carbon::createFromDate($currentYear, $m, 1)->format('Y-m');

        $payrollsByMonth = Payroll::where('month', $monthStr)
            ->where('branch_id', $branchId)
            ->with(['employee', 'contributions.contribution'])
            ->get();

        if ($payrollsByMonth->isEmpty()) {
            $months->push([
                'month'          => $monthStr,
                'exists'         => false,
                'paid_employees' => 0,
                'total_count'    => 0,
                'paid_count'     => 0,
                'total_amount'   => 0,
                'nssf'           => 0,
                'nhif'           => 0,
                'wcf'            => 0,
                'tuico'          => 0,
                'paye'           => 0,
                'net_paid'       => 0,
                'id'             => null,
                'reference'      => null,
            ]);
            continue;
        }

        $totalGrossAmount = 0;
        $totalNssf        = 0;
        $totalNhif        = 0;
        $totalWcf         = 0;
        $totalTuico       = 0;
        $totalNetPaid     = 0;

        foreach ($payrollsByMonth as $payroll) {
            $basic     = floatval($payroll->basic_salary);
            $allowance = floatval($payroll->allowance);
            $gross     = $basic + $allowance;

            $nssf = $wcf = $nhif = $tuico = 0;

            foreach ($payroll->contributions as $p) {
                $contribution = $p->contribution;
                if ($contribution) {
                    $type   = strtolower($contribution->type);
                    $name   = strtoupper($contribution->name);
                    $amount = ($type === 'fixed')
                        ? floatval($contribution->rate)
                        : (($type === 'percentage')
                            ? (floatval($contribution->rate) / 100) * $basic
                            : 0);

                    switch ($name) {
                        case 'NSSF':  $nssf  = $amount; break;
                        case 'WCF':   $wcf   = $amount; break;
                        case 'NHIF':  $nhif  = $amount; break;
                        case 'TUICO': $tuico = $amount; break;
                    }
                }
            }

            $advance_pay = $salary_advances
                ->where('employee_id', $payroll->employee_id)
                ->where('month', $monthStr)
                ->sum('amount');


            $loan_deductions = $loans
                ->where('employee_id', $payroll->employee_id)
                ->flatMap(function ($loan) use ($monthStr) {
                    return $loan->loanRepayments->where('deduction_date', $monthStr);
                })
                ->sum('amount');

            $total_deductions = $nssf + $nhif + $tuico + $advance_pay + $loan_deductions;
            $net              = $gross - $total_deductions;

            $totalGrossAmount += $gross;
            $totalNssf        += $nssf;
            $totalNhif        += $nhif;
            $totalTuico       += $tuico;
            $totalNetPaid     += $net;
        }

        $totalCount = $payrollsByMonth->count();
        $paidCount  = $payrollsByMonth->where('is_paid', 1)->count();

        $months->push([
            'month'          => $monthStr,
            'exists'         => true,
            'paid_employees' => $totalCount,
            'total_count'    => $totalCount,
            'paid_count'     => $paidCount,
            'total_amount'   => $totalGrossAmount,
            'nssf'           => $totalNssf,
            'nhif'           => $totalNhif,
            'wcf'            => $totalWcf,
            'tuico'          => $totalTuico,
            'net_paid'       => $totalNetPaid,
            'id'             => $payrollsByMonth->first()->id        ?? null,
            'reference'      => $payrollsByMonth->first()->reference ?? null,
        ]);
    }

    return view('payrolls.index', compact(
        'totalEmployees',
        'salaryGroupsCount',
        'currentMonthPayroll',
        'payrolls',
        'contributions',
        'branches',
        'months',
        'employees',
        'loans',
        'salary_advances',
        'salary_groups'
    ));
}

    public function create()
    {
        return view('payrolls.create');
    }

    // public function store(Request $request)
    // {
    //     $month = $request->input('month');
    //     $employees = $request->input('employees');
    //     $loanDeductions = [];
    
    //     foreach ($employees as $employeeId => $data) {
    //         if (!isset($data['selected'])) continue;
    
    //         $basicSalary = $data['basic_salary'];
    //         $allowance = $data['allowance'] ?? 0;
    //         $gross_salary = $basicSalary + $allowance;
    //         $deduction = 0;
    
    //         // Fetch loan for selected employee
    //             $loan = DB::table('loans')
    //                 ->where('employee_id', $employeeId)
    //                 // ->where('status', 'active')
    //                 ->where('balance', '>', 0)
    //                 ->orderBy('id', 'desc')
    //                 ->first();
    
    //             if ($loan) {
    //                 $deductAmount = min($loan->monthly_deduction, $loan->balance);
    //                 $deduction += $deductAmount;
    
    //                 DB::table('loans')->where('id', $loan->id)->update([
    //                     'balance' => $loan->balance - $deductAmount,
    //                     'status' => ($loan->balance - $deductAmount) <= 0 ? 'paid' : 'active',
    //                 ]);
    
    //                 DB::table('loan_repayments')->insert([
    //                     'loan_id'       => $loan->id,
    //                     'branch_id' => Auth::user()->branch_id,
    //                     'employee_id'   => $employeeId,
    //                     'amount'        => $deductAmount,
    //                     'deduction_date' => $month, // assumes YYYY-MM format input
    //                     'created_at'    => now(),
    //                     'updated_at'    => now(),
    //                 ]);

    //                 $loanDeductions[] = [
    //                     'employee_id' => $employeeId,
    //                     'loan_id' => $loan->id,
    //                     'deducted' => $deductAmount,
    //                 ];
    //             }
    //         // }
    
    //         // 2. SALARY ADVANCE Deduction
    //         $advance = DB::table('salary_advances')
    //             ->where('employee_id', $employeeId)
    //             ->where('month', $month)
    //             ->first();

    //         if ($advance) {
    //             $deduction += $advance->amount;

    //             // Optional: mark it as deducted if you add a `status` column
    //             DB::table('salary_advances')->where('id', $advance->id)->update([
    //                 'status' => 'deducted',
    //                 'updated_at' => now()
    //             ]);

    //             $advanceDeductions[] = [
    //                 'employee_id' => $employeeId,
    //                 'amount' => $advance->amount,
    //             ];
    //         }
    //     // }

    //     $nssf = 0;

    //     // Check if 'NSSF' is selected in contributions
    //     if (!empty($data['contributions']) && in_array('1', array_map('strtolower', $data['contributions']))) {
    //         // Assume 10% NSSF on gross salary as an example
    //         $nssf = 0.10 * $gross_salary;
    //     }

    //     // 4. Calculate TAXABLE INCOME (only NSSF is deductible)
    //     $taxable_income = $gross_salary - $nssf;

    //     // 5. PAYE
    //     if ($taxable_income <= 270000) {
    //         $paye = 0 ;
    //     } elseif ($taxable_income <= 520000) {
    //         $paye = ($taxable_income - 270000) * 0.08;
    //     } elseif ($taxable_income <= 760000) {
    //         $paye = 20000 + ($taxable_income - 520000) * 0.20;
    //     } elseif ($taxable_income <= 1000000) {
    //         $paye = 68000 + ($taxable_income - 760000) * 0.25;
    //     } else {
    //         $paye = 128000 + ($taxable_income - 1000000) * 0.30;
    //     }

    //         $netSalary = ($basicSalary + $allowance) - $deduction;
    
    //         $payroll = Payroll::create([
    //             'employee_id' => $employeeId,
    //             'branch_id' => Auth::user()->branch_id,
    //             'reference' => date('YhisH'),
    //             'month' => $month,
    //             'basic_salary' => $basicSalary,
    //             'allowance' => $allowance,
    //             'paye' => $paye,
    //             'net_salary' => $netSalary,
    //         ]);
    
    //         if (!empty($data['contributions'])) {
    //             foreach ($data['contributions'] as $contribName) {
    //                 PayrollContribution::create([
    //                     'payroll_id' => $payroll->id,
    //                     'contribution_id' => $contribName,
    //                     'name' => $contribName,
    //                     'rate' => null,
    //                     'branch_id' => Auth::user()->branch_id
    //                 ]);
    //             }
    //         }
    //     }
    

    //     return redirect()->back()->with('message', 'Payroll generated.');

    // }
    


public function store(Request $request)
{
    $request->validate([
        'month'     => 'required|date_format:Y-m',
        'employees' => 'required|array|min:1',
    ]);

    $month     = $request->input('month');
    $employees = $request->input('employees', []);
    $branchId  = Auth::user()->branch_id;

    $created = 0;
    $skipped = 0;

    /* One reference for the entire payroll batch */
    $batchReference =  random_int(10000000, 99999999);

    try {
        DB::transaction(function () use (
            $employees,
            $month,
            $branchId,
            $batchReference,
            &$created,
            &$skipped
        ) {
            foreach ($employees as $key => $data) {
                if (!is_array($data)) {
                    continue;
                }

                // Support employees[12][selected] checkbox submissions.
                $selectedValue = $data['selected'] ?? false;
                $isSelected = in_array(
                    $selectedValue,
                    [true, 1, '1', 'on', 'yes'],
                    true
                );

                if (!$isSelected) {
                    continue;
                }

                $employeeId = (int) ($data['employee_id'] ?? $key);
                if ($employeeId <= 0) {
                    continue;
                }

                // Confirm employee belongs to the logged-in user's branch.
                $employee = Employee::where('id', $employeeId)
                    ->where('branch_id', $branchId)
                    ->first();

                if (!$employee) {
                    continue;
                }

                // Avoid generating the same employee's payroll twice.
                $exists = Payroll::where('employee_id', $employeeId)
                    ->where('branch_id', $branchId)
                    ->where('month', $month)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $basicSalary = max(0, (float) ($data['basic_salary'] ?? 0));
                $allowance   = max(0, (float) ($data['allowance']    ?? 0));
                $grossSalary = $basicSalary + $allowance;

                $loanDeduction    = 0;
                $advanceDeduction = 0;
                $nssf             = 0;
                $nhif             = 0;
                $tuico            = 0;
                $paye             = 0;

                /* ================= Outstanding loan ================= */
                // $loan = DB::table('loans')
                //     ->where('employee_id', $employeeId)
                //     ->where('branch_id', $branchId)
                //     ->where('balance', '>', 0)
                //     ->orderByDesc('id')
                //     ->lockForUpdate()
                //     ->first();

                // if ($loan) {
                //     $balance       = max(0, (float) $loan->balance);
                //     $monthlyAmount = max(0, (float) ($loan->monthly_deduction ?? 0));
                //     $loanDeduction = min($monthlyAmount, $balance);

                //     if ($loanDeduction > 0) {
                //         $newBalance = round($balance - $loanDeduction, 2);

                //         DB::table('loans')
                //             ->where('id', $loan->id)
                //             ->update([
                //                 'balance'    => $newBalance,
                //                 'status'     => $newBalance <= 0 ? 'paid' : 'active',
                //                 'updated_at' => now(),
                //             ]);

                //         DB::table('loan_repayments')->insert([
                //             'loan_id'        => $loan->id,
                //             'branch_id'      => $branchId,
                //             'employee_id'    => $employeeId,
                //             'amount'         => $loanDeduction,
                //             'deduction_date' => $month . '-01',
                //             'created_at'     => now(),
                //             'updated_at'     => now(),
                //         ]);
                //     }
                // }


$loan = DB::table('loans')
    ->where('employee_id', $employeeId)
    ->where('branch_id', $branchId)
    ->where('status', '!=', 'paid')
    ->orderByDesc('id')
    ->lockForUpdate()
    ->first();

if ($loan) {

    // Calculate total repayments already made
    $totalRepaid = DB::table('loan_repayments')
        ->where('loan_id', $loan->id)
        ->sum('amount');

    // Calculate actual remaining balance
    $balance = max(
        0,
        round((float) $loan->principal - (float) $totalRepaid, 2)
    );

    // Monthly deduction
    $monthlyAmount = max(
        0,
        (float) ($loan->monthly_deduction ?? 0)
    );

    // Amount to deduct this month
    $loanDeduction = min($monthlyAmount, $balance);

    if ($loanDeduction > 0) {

        // Insert repayment
        DB::table('loan_repayments')->insert([
            'loan_id'        => $loan->id,
            'branch_id'      => $branchId,
            'employee_id'    => $employeeId,
            'amount'         => $loanDeduction,
            'deduction_date' => $month . '-01',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Recalculate balance after inserting repayment
        $newTotalRepaid = DB::table('loan_repayments')
            ->where('loan_id', $loan->id)
            ->sum('amount');

        $newBalance = max(
            0,
            round((float) $loan->principal - (float) $newTotalRepaid, 2)
        );

        // Update loan
        DB::table('loans')
            ->where('id', $loan->id)
            ->update([
                'balance'    => $newBalance,
                'status'     => $newBalance <= 0 ? 'paid' : 'active',
                'updated_at' => now(),
            ]);
    }
}

                /* ================= Salary advance ================= */
                $advance = DB::table('salary_advances')
                    ->where('employee_id', $employeeId)
                    ->where('branch_id', $branchId)
                    ->where('month', $month)
                    ->where(function ($query) {
                        $query->whereNull('status')
                              ->orWhere('status', '!=', 'deducted');
                    })
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->first();

                if ($advance && (float) $advance->amount > 0) {
                    $advanceDeduction = (float) $advance->amount;

                    DB::table('salary_advances')
                        ->where('id', $advance->id)
                        ->update([
                            'status'     => 'deducted',
                            'updated_at' => now(),
                        ]);
                }

                /* ================= Contributions ================= */
                $contributionIds = array_values(array_unique(
                    array_filter(
                        (array) ($data['contributions'] ?? []),
                        fn ($id) => is_numeric($id) && (int) $id > 0
                    )
                ));

                $contributions = empty($contributionIds)
                    ? collect()
                    : Contribution::whereIn('id', $contributionIds)
                        ->get()
                        ->keyBy('id');

                foreach ($contributions as $contribution) {
                    $name = strtoupper((string) $contribution->name);
                    $type = strtolower((string) $contribution->type);
                    $rate = (float) $contribution->rate;

                    // Compute the amount for this contribution record.
                    $amount = 0;
                    if ($type === 'percentage') {
                        $amount = $grossSalary * $rate / 100;
                    } elseif ($type === 'fixed') {
                        $amount = $rate;
                    }

                    // Route to the correct accumulator.
                    if (str_contains($name, 'NSSF')) {
                        $nssf += $amount;
                    } elseif (str_contains($name, 'NHIF')) {
                        $nhif += $amount;
                    } elseif (str_contains($name, 'TUICO')) {
                        $tuico += $amount;
                    }
                    // WCF is EMPLOYER-paid — do NOT deduct from net salary.
                }

                $nssf  = round($nssf,  2);
                $nhif  = round($nhif,  2);
                $tuico = round($tuico, 2);

                /* ================= PAYE ================= */
                $taxableIncome = max(0, $grossSalary - $nssf);

                if ($taxableIncome <= 270000) {
                    $paye = 0;
                } elseif ($taxableIncome <= 520000) {
                    $paye = ($taxableIncome - 270000) * 0.08;
                } elseif ($taxableIncome <= 760000) {
                    $paye = 20000 + (($taxableIncome - 520000) * 0.20);
                } elseif ($taxableIncome <= 1000000) {
                    $paye = 68000 + (($taxableIncome - 760000) * 0.25);
                } else {
                    $paye = 128000 + (($taxableIncome - 1000000) * 0.30);
                }

                $paye = round(max(0, $paye), 2);

                /* ================= NET SALARY =================
                 * NHIF and TUICO are now included.
                 * WCF is employer-paid → excluded.
                 */
                $netSalary = round(
                    $grossSalary
                    - $loanDeduction
                    - $advanceDeduction
                    - $nssf
                    - $nhif
                    - $tuico
                    - $paye,
                    2
                );

                /* ================= Save payroll ================= */
                $payroll = new Payroll();

                $payroll->employee_id  = $employeeId;
                $payroll->branch_id    = $branchId;
                $payroll->reference    = $batchReference;
                $payroll->month        = $month;
                $payroll->basic_salary = $basicSalary;
                $payroll->allowance    = $allowance;
                $payroll->paye         = $paye;
                $payroll->net_salary   = $netSalary;
                $payroll->created_by   = Auth::id();
                $payroll->save();

                /* Save contribution records */
                foreach ($contributions as $contribution) {
                    PayrollContribution::create([
                        'payroll_id'      => $payroll->id,
                        'contribution_id' => $contribution->id,
                        'name'            => $contribution->name,
                        'rate'            => $contribution->rate,
                        'branch_id'       => $branchId,
                    ]);
                }

                $created++;

                Log::info('Employee payroll generated', [
                    'employee_id'    => $employeeId,
                    'payroll_id'     => $payroll->id,
                    'reference'      => $batchReference,
                    'month'          => $month,
                    'gross'          => $grossSalary,
                    'nssf'           => $nssf,
                    'nhif'           => $nhif,
                    'tuico'          => $tuico,
                    'paye'           => $paye,
                    'loan'           => $loanDeduction,
                    'advance'        => $advanceDeduction,
                    'net_salary'     => $netSalary,
                ]);
            }

            if ($created === 0) {
                throw new \RuntimeException(
                    'No new payroll records were generated. Check employee selection and existing payroll records.'
                );
            }
        });

    } catch (\Throwable $e) {
        Log::error('Payroll generation failed', [
            'month'     => $month,
            'branch_id' => $branchId,
            'error'     => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
        ]);

        return redirect()->back()->with(
            'error',
            'Payroll generation failed: ' . $e->getMessage()
        );
    }

    $message = "Payroll generated successfully for {$created} employee(s).";
    if ($skipped > 0) {
        $message .= " {$skipped} employee(s) skipped because payroll already exists.";
    }

    return redirect()->back()->with('message', $message);
}


    public function saveSalaryGroup(Request $request)
{
    $request->validate([
        'group_name' => 'required|string|max:100',
        'basic_salary' => 'required|numeric|min:0',
        'allowance' => 'nullable|numeric|min:0',
        'deductions' => 'nullable|numeric|min:0',
    ]);

    SalaryGroup::create([
        'group_name' => $request->group_name,
        'basic_salary' => $request->basic_salary,
        'branch_id' => Auth::user()->branch_id,
        'allowance' => $request->allowance,
    ]);

    return redirect()->back()->with('message', 'Salary group added successfully!');
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
        ->whereRaw("DATE_FORMAT(deduction_date, '%Y-%m') = ?", [$monthRaw_2])
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
        $tuico = 0;
        $gross_salary = 0;

        $gross_salary = $basic_salary + $allowance;

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

        $total_deductions = $nssf  + $nhif + $tuico + $advance_pay + $loan + $paye;
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
            ->whereRaw("DATE_FORMAT(deduction_date, '%Y-%m') = ?", [$monthRaw_2])
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
            $gross_salary = 0;

            $gross_salary = $basic_salary + $allowance;
    
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
    
            $total_deductions = $nssf  + $nhif + $tuico + $advance_pay + $loan + $paye;
            $net_salary = ($gross_salary) - $total_deductions;
    
            
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
        'payroll_contributions.amount',
        'payrolls.is_paid'
    )
    ->get();

    // Get employee IDs for those payrolls
    $employeeIds = Payroll::whereIn('id', $payrollIds)->pluck('employee_id');

    // Get loan repayments for all employees in this payroll batch and for the same month
    $loanRepayments = LoanRepayment::whereIn('employee_id', $employeeIds)
        ->whereRaw("DATE_FORMAT(deduction_date, '%Y-%m') = ?", [$month_repayment])
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
                    $amount_ = ($rate / 100) * $basic_salary;
                }

                switch ($name) {
                    case 'NSSF': $nssf = $amount; break;
                    case 'NHIF': $nhif = $amount; break;
                    case 'TUICO': $tuico = $amount_; break;
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

        $net_salary = ($basic_salary + $allowance) - ($nssf + $nhif + $tuico + $advance_pay + $loan + $paye);

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
    // =====================================================
    // 1. GENERAL SETTINGS
    // =====================================================

    $settings = DB::table('general_settings')
        ->select('business_name')
        ->get();


    // =====================================================
    // 2. GET PAYROLL IDS
    // =====================================================

    $payrollIds = Payroll::where('reference', $reference)
        ->pluck('id');

    if ($payrollIds->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No payroll data found.'
        ], 404);
    }


    // =====================================================
    // 3. GET CREATOR AND APPROVER INFORMATION
    // =====================================================

    $mainPayroll = DB::table('payrolls')

        // Creator from payrolls.created_by
        ->leftJoin('users as creator', 'creator.id', '=', 'payrolls.created_by')
        ->leftJoin('roles as creator_role', 'creator_role.id', '=', 'creator.role_id')

        // Approver from payroll_requests.approved_by
        ->leftJoin('payroll_requests', function ($join) {
            $join->on('payroll_requests.reference', '=', 'payrolls.reference');
        })

        ->leftJoin('users as approver', 'approver.id', '=', 'payroll_requests.approved_by')
        ->leftJoin('roles as approver_role', 'approver_role.id', '=', 'approver.role_id')

        ->where('payrolls.id', $payrollIds->first())

        ->select(
            'payrolls.*',

            // Creator information
            'creator.id as creator_id',

            DB::raw("
                TRIM(CONCAT_WS(' ',
                    NULLIF(creator.fname, ''),
                    NULLIF(creator.lname, '')
                )) as creator_name
            "),

            'creator_role.name as creator_role',
            'creator.signature as creator_signature',

            // Approver information
            'approver.id as approver_id',

            DB::raw("
                TRIM(CONCAT_WS(' ',
                    NULLIF(approver.fname, ''),
                    NULLIF(approver.lname, '')
                )) as approver_name
            "),

            'approver_role.name as approver_role',
            'approver.signature as approver_signature'
        )

        ->first();

        
    if (!$mainPayroll) {
        return response()->json([
            'success' => false,
            'message' => 'Main payroll record not found.'
        ], 404);
    }


    // =====================================================
    // 4. PREPARE SIGNATURE IMAGE PATHS
    // =====================================================

    $getSignaturePath = function ($signature) {

        if (empty($signature)) {
            return null;
        }

        $signature = str_replace('\\', '/', $signature);

        // Remove public/ prefix if stored in database
        $signature = preg_replace('#^public/#', '', $signature);

        // Remove leading slash
        $signature = ltrim($signature, '/');

        $path = public_path($signature);

        return file_exists($path) ? $path : null;
    };


    // Creator details
    $creator = [
        'id' => $mainPayroll->creator_id,
        'name' => $mainPayroll->creator_name ?: 'N/A',
        'role' => $mainPayroll->creator_role ?: 'N/A',
        'signature' => $getSignaturePath($mainPayroll->creator_signature),
    ];


    // Approver details
    $approver = [
        'id' => $mainPayroll->approver_id,
        'name' => $mainPayroll->approver_name ?: 'N/A',
        'role' => $mainPayroll->approver_role ?: 'N/A',
        'signature' => $getSignaturePath($mainPayroll->approver_signature),
    ];


    // =====================================================
    // 5. PAYROLL MONTH
    // =====================================================

    $month = \Carbon\Carbon::parse($mainPayroll->month)
        ->format('F Y');

    $month_repayment = \Carbon\Carbon::parse($mainPayroll->month)
        ->format('Y-m');


    // =====================================================
    // 6. GET PAYROLL CONTRIBUTIONS
    // =====================================================

    $contributions = PayrollContribution::whereIn('payroll_id', $payrollIds)
        ->with([
            'payroll.employee',
            'contribution'
        ])
        ->get();


    // =====================================================
    // 7. GET EMPLOYEE IDS
    // =====================================================

    $employeeIds = Payroll::whereIn('id', $payrollIds)
        ->pluck('employee_id');


    // =====================================================
    // 8. GET LOAN REPAYMENTS
    // =====================================================

    $loanRepayments = LoanRepayment::whereIn('employee_id', $employeeIds)
        ->whereRaw("DATE_FORMAT(deduction_date, '%Y-%m') = ?", [$month_repayment])
        ->get()
        ->groupBy('employee_id');


    // =====================================================
    // 9. GET SALARY ADVANCES
    // =====================================================

    $salaryAdvances = DB::table('salary_advances')
        ->whereIn('employee_id', $employeeIds)
        ->where('month', $month_repayment)
        ->get()
        ->groupBy('employee_id');


    // =====================================================
    // 10. GROUP CONTRIBUTIONS BY EMPLOYEE
    // =====================================================

    $grouped = $contributions->groupBy(function ($item) {
        return $item->payroll->employee_id ?? 0;
    });


    // =====================================================
    // 11. CALCULATE EMPLOYEE SALARIES
    // =====================================================

    $slips = $grouped->map(function ($rows, $employeeId) use (
        $loanRepayments,
        $salaryAdvances
    ) {

        $first = $rows->first();

        $basic_salary = (float) ($first->payroll->basic_salary ?? 0);
        $allowance = (float) ($first->payroll->allowance ?? 0);
        $paye = (float) ($first->payroll->paye ?? 0);

        $gross_salary = $basic_salary + $allowance;

        $nssf = 0;
        $nhif = 0;
        $tuico = 0;

        // Calculate contributions
        foreach ($rows as $p) {

            $contribution = $p->contribution;

            if (!$contribution) {
                continue;
            }

            $type = strtolower($contribution->type);
            $name = strtoupper($contribution->name);

            $amount = 0;

            if ($type === 'fixed') {

                $amount = (float) $contribution->rate;

            } elseif ($type === 'percentage') {

                $rate = (float) $contribution->rate;

                $amount = ($rate / 100) * $gross_salary;
            }

            switch ($name) {

                case 'NSSF':
                    $nssf = $amount;
                    break;

                case 'NHIF':
                    $nhif = $amount;
                    break;

                case 'TUICO':
                    $tuico = $amount;
                    break;
            }
        }


        // Salary advances
        $advance_pay = $salaryAdvances->has($employeeId)
            ? (float) $salaryAdvances[$employeeId]->sum('amount')
            : 0;


        // Loan repayments
        $loan = $loanRepayments->has($employeeId)
            ? (float) $loanRepayments[$employeeId]->sum('amount')
            : 0;


        // Net salary
        $net_salary = $gross_salary - (
            $nssf +
            $nhif +
            $tuico +
            $advance_pay +
            $loan +
            $paye
        );


        return [

            'employee_name' => isset($first->payroll->employee)
                ? trim(
                    $first->payroll->employee->firstname . ' ' .
                    $first->payroll->employee->middlename . ' ' .
                    $first->payroll->employee->surname
                )
                : 'N/A',

            'basic_salary' => $basic_salary,
            'allowance' => $allowance,
            'salary_advance' => $advance_pay,
            'nssf' => $nssf,
            'nhif' => $nhif,
            'tuico' => $tuico,
            'loan' => $loan,
            'paye' => $paye,
            'net_salary' => $net_salary,

            'reference' => $first->payroll->reference ?? 'N/A',
        ];

    })->values();


    // =====================================================
    // 12. GENERATE PDF
    // =====================================================

    $pdf = Pdf::loadView('payrolls.payrolls_details_pdf', [

        'slips' => $slips,

        'month' => $month,

        'settings' => $settings,

        'creator' => $creator,

        'approver' => $approver,

    ])->setPaper('a4', 'landscape');


    return $pdf->download("Payroll-Details-{$month}.pdf");
}


// public function downloadPayrollDetails($reference)
// {

//     $settings= DB::table('general_settings')
//     ->select('business_name')
//     ->get(); 

// $payrollIds = Payroll::where('reference', $reference)->pluck('id');
    
// if ($payrollIds->isEmpty()) {
//     return response()->json(['success' => false, 'message' => 'No data found.']);
// }

// // Get main payroll for date/month info
// $mainPayroll = Payroll::find($payrollIds->first());
// $month = \Carbon\Carbon::parse($mainPayroll->month)->format('F Y');
// $month_repayment = \Carbon\Carbon::parse($mainPayroll->month)->format('Y-m');

// // Load contributions
// $contributions = PayrollContribution::whereIn('payroll_id', $payrollIds)
//     ->with(['payroll.employee', 'contribution'])
//     ->get();

// // Get employee IDs for those payrolls
// $employeeIds = Payroll::whereIn('id', $payrollIds)->pluck('employee_id');

// // Get loan repayments for all employees in this payroll batch and for the same month
// $loanRepayments = LoanRepayment::whereIn('employee_id', $employeeIds)
//     ->where('deduction_date', $month_repayment)
//     ->get()
//     ->groupBy('employee_id');

// // Get salary advances for this month
// $salaryAdvances = DB::table('salary_advances')
//         ->whereIn('employee_id', $employeeIds)
//         ->where('month', $month_repayment)
//         ->get()
//         ->groupBy('employee_id');
    
// // Group contributions by employee
// $grouped = $contributions->groupBy(function ($item) {
//     return $item->payroll->employee_id ?? 0;
// });

// $slips = $grouped->map(function ($rows, $employeeId) use ($loanRepayments, $salaryAdvances) {
//     $first = $rows->first();

//     $basic_salary = floatval($first->payroll->basic_salary ?? 0);
//     $allowance = floatval($first->payroll->allowance ?? 0);
//     $paye = floatval($first->payroll->paye ?? 0);

//     $gross_salary = $basic_salary + $allowance;

//     $nssf = 0;
//     $nhif = 0;
//     $tuico = 0;
//     $advance_pay = 0;
//     $loan = 0;

//     foreach ($rows as $p) {
//         $contribution = $p->contribution;
//         if ($contribution) {
//             $type = strtolower($contribution->type);
//             $name = strtoupper($contribution->name);
//             $amount = 0;

//             if ($type === 'fixed') {
//                 $amount = floatval($contribution->rate);
//             } elseif ($type === 'percentage') {
//                 $rate = floatval($contribution->rate);
//                 $amount = ($rate / 100) * $gross_salary;
//                 $amount_ = ($rate / 100) * $basic_salary;
//             }

//             switch ($name) {
//                 case 'NSSF': $nssf = $amount; break;
//                 case 'NHIF': $nhif = $amount; break;
//                 case 'TUICO': $tuico = $amount_; break;
//             }
//         }
//     }

//    // Get salary advance for this employee
// $advance_pay = $salaryAdvances->has($employeeId)
// ? $salaryAdvances[$employeeId]->sum('amount')
// : 0;

// // Get loan repayment for this employee
// $loan = $loanRepayments->has($employeeId)
// ? $loanRepayments[$employeeId]->sum('amount')
// : 0;

//     $net_salary = ($basic_salary + $allowance) - ($nssf + $nhif + $tuico + $advance_pay + $loan + $paye);

//     return [
//         'employee_name' => isset($first->payroll->employee)
//             ? $first->payroll->employee->firstname . ' ' .
//               $first->payroll->employee->middlename . ' ' .
//               $first->payroll->employee->surname
//             : 'N/A',
//         'basic_salary'     => $basic_salary,
//         'allowance'        => $allowance,
//         'salary_advance'   => $advance_pay,
//         'nssf'             => $nssf,
//         'nhif'             => $nhif,
//         'tuico'            => $tuico,
//         'loan'             => $loan,
//         'paye'             => $paye,
//         'net_salary'       => $net_salary,
//         'reference' => $first->payroll->reference ?? 'N/A',
//     ];
// })->values();

// $pdf = Pdf::loadView('payrolls.payrolls_details_pdf', [
//     'slips' => $slips,
//     'month' => $month,
//     'settings' => $settings
// ])
// ->setPaper('a4', 'landscape'); // ✅ lowercase 'a4' is safer

// return $pdf->download("Payroll-Details-{$month}.pdf");

// }



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

        $nssf = $paye = $nhif = $tuico = 0;
    
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
                    $amount_ = ($rate / 100) * $basic_salary;

                }
    
                switch ($name) {
                    case 'NSSF': $nssf = $amount; break;
                    case 'PAYE': $paye = $amount; break;
                    case 'NHIF': $nhif = $amount; break;
                    case 'TUICO': $tuico = $amount_; break;
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
        $gross_salary = ($payroll->basic_salary ?? 0);
        // + ($payroll->allowance ?? 0);

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

    $taxable = $grossSalary - ($nssf);

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


public function filterSalaryAdvances(Request $request)
{
    $month = $request->input('month');   // '' or '01'..'12'
    $year  = $request->input('year');    // '' or 'YYYY'

    $query = SalaryAdvance::with('employee');
    $employees = Employee::orderBy('firstname')->get();

    if ($year && $month) {
        $query->where('month', $year . '-' . $month);
    } elseif ($year) {
        $query->where('month', 'LIKE', $year . '-%');
    } elseif ($month) {
        $query->where('month', 'LIKE', '%-' . $month);
    }

    $salary_advances = $query->orderBy('month', 'desc')->get();

    return response()->json([
        'success' => true,
        'html'    => view('payrolls._advance_content', compact('salary_advances', 'employees'))->render(),
        'count'   => $salary_advances->count(),
    ]);
}

public function rollbackPayroll($id)
{
    try {
        DB::beginTransaction();

        // Get the selected payroll
        $referencePayroll = Payroll::findOrFail($id);
        $month    = $referencePayroll->month;          // "YYYY-MM"
        $branchId = Auth::user()->branch_id;

        [$year, $monthNum] = explode('-', $month);
        $year     = (int) $year;
        $monthNum = (int) $monthNum;

        // Get all payrolls for this month+branch
        $payrolls = Payroll::where('month', $month)
            ->where('branch_id', $branchId)
            ->get();

        // Delete contributions and payrolls
        foreach ($payrolls as $payroll) {
            PayrollContribution::where('payroll_id', $payroll->id)
                ->where('branch_id', $branchId)
                ->delete();
            $payroll->delete();
        }

        /* ==============================================================
         | 1. Capture which loans had repayments this month — BEFORE
         |    deleting them, so we know what to recompute.
         ============================================================== */

        $month_repay = $referencePayroll->month . '-01';
        $repaymentsQuery = LoanRepayment::where('deduction_date', $month_repay)
            ->where('branch_id', $branchId);

        $affectedLoanIds = (clone $repaymentsQuery)
            ->whereNotNull('loan_id')
            ->distinct()
            ->pluck('loan_id')
            ->unique()
            ->values()
            ->all();

        // 2. Delete the loan repayments for this month
        $deletedRepayments = $repaymentsQuery->delete();

        /* ==============================================================
         | 3. Restore each affected loan's balance
         |    balance = max(0, principal − SUM(remaining repayments))
         ============================================================== */
        $restoredLoans = 0;
        foreach ($affectedLoanIds as $loanId) {
            $loan = Loan::find($loanId);
            if (!$loan) continue;

            $totalRepaid = (float) LoanRepayment::where('loan_id', $loanId)->sum('amount');
            $newBalance  = max(0, (float) $loan->principal - $totalRepaid);

            $loan->balance = $newBalance;
            $loan->status = "active";
            $loan->save();          // also refreshes updated_at
            $restoredLoans++;
        }

        // 4. Delete payroll requests (and their events) for that period
        $requestIds = PayrollRequest::where('branch_id', $branchId)
            ->where('period_year',  $year)
            ->where('period_month', $monthNum)
            ->pluck('id');

        if ($requestIds->isNotEmpty()) {
            PayrollRequestEvent::whereIn('payroll_request_id', $requestIds)->delete();
            PayrollRequest::whereIn('id', $requestIds)->delete();
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'All payrolls, related data, and payment requests for this month deleted successfully.',
            'stats'   => [
                'repayments_deleted' => $deletedRepayments,
                'loans_restored'     => $restoredLoans,
            ],
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to rollback all payrolls.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


   public function employeesForPayment($reference)
{
    $branchId = Auth::user()->branch_id;

    $rows = DB::table('payrolls')
        ->join('employees', 'employees.id', '=', 'payrolls.employee_id')
        ->where('payrolls.branch_id', $branchId)
        ->where('payrolls.reference', $reference)
        ->select(
            'payrolls.id',
            DB::raw("CONCAT(employees.firstname,' ',employees.surname) AS name"),
            'payrolls.net_salary',
            'payrolls.is_paid'
        )
        ->orderBy('employees.firstname')
        ->get();

    return response()->json(['employees' => $rows]);
}


public function markEmployeesPaid(Request $request, $reference)
{
    $data = $request->validate([
        'payroll_ids'   => 'required|array|min:1',
        'payroll_ids.*' => 'integer|exists:payrolls,id',
    ]);

    $branchId = Auth::user()->branch_id;

    /* ============================================================
     * STEP 1 — debug: what do we actually match?
     * ============================================================ */
    $matched = DB::table('payrolls')
        ->where('branch_id', $branchId)
        ->where('reference', $reference)
        ->whereIn('id', $data['payroll_ids'])
        ->get(['id', 'reference', 'branch_id', 'is_paid', 'net_salary']);

    \Log::info('markEmployeesPaid: pre-check', [
        'reference_param' => $reference,
        'branch_id_param' => $branchId,
        'requested_ids'   => $data['payroll_ids'],
        'matched_count'   => $matched->count(),
        'matched_rows'    => $matched->toArray(),
    ]);

    if ($matched->isEmpty()) {
        return response()->json([
            'message' => 'No matching payrolls found. '
                       . 'Reference: "' . $reference . '", '
                       . 'Branch: ' . $branchId . ', '
                       . 'Requested IDs: ' . implode(',', $data['payroll_ids']),
            'updated' => 0,
        ], 422);
    }

    /* ============================================================
     * STEP 2 — update (allow NULL is_paid + use 1 for DB safety)
     * ============================================================ */
    $updated = DB::table('payrolls')
        ->where('branch_id', $branchId)
        ->where('reference', $reference)
        ->whereIn('id', $data['payroll_ids'])
        ->where(function ($q) {
            $q->where('is_paid', 0)
              ->orWhereNull('is_paid');   // ← critical if column was added after data existed
        })
        ->update([
            'is_paid'  => 1,              // int, not bool — safest across drivers
            'paid_at'  => now(),
            'paid_by'  => Auth::id(),
        ]);

    /* ============================================================
     * STEP 3 — fail loudly if nothing changed
     * ============================================================ */
    if ($updated === 0) {
        $alreadyPaid = $matched->where('is_paid', 1)->pluck('id')->all();

        return response()->json([
            'message' => $alreadyPaid
                ? 'These payrolls are already marked paid: ' . implode(',', $alreadyPaid)
                : 'Update affected 0 rows. Check that is_paid/paid_at/paid_by columns exist.',
            'updated'      => 0,
            'already_paid' => $alreadyPaid,
        ], 422);
    }

    return response()->json([
        'message' => "Marked {$updated} payroll(s) as paid.",
        'updated' => $updated,
    ]);
}

}
