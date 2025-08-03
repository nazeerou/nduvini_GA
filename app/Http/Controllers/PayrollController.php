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


class PayrollController extends Controller
{
    public function index()
    {

 $employees = DB::table('employees')
        ->select('employees.id', 'employees.name', 'employees.email',
         'employees.mobile', 'employees.joining_date', 'designations.name as position',
         'salary_groups.group_name', 'branches.branch_name', 'salary_groups.id as salary_id',
          'salary_groups.group_name', 'branches.id as branch_id', 'salary_groups.basic_salary')
         ->leftJoin('salary_groups', 'salary_groups.id', 'employees.salary_group_id')
         ->leftJoin('branches', 'branches.id', 'employees.branch_id')
         ->leftJoin('designations', 'designations.id', 'employees.designation_id')
         ->where('employees.branch_id', Auth::user()->branch_id)
         ->get();

        $contributions = Contribution::select('*')->get();

        $branches = Branch::where('id', Auth::user()->branch_id)->get();

        $payrolls = Payroll::with('employee')
                   ->where('payrolls.branch_id', Auth::user()->branch_id)
                    ->orderBy('payrolls.month', 'desc')->get();

                    $totalEmployees = Employee::where('branch_id', Auth::user()->branch_id)->count();
                    $salaryGroupsCount = Contribution::select('*')->count();
            
                    // This month's payroll total (net salaries)
                    $currentMonth = Carbon::now()->format('Y-m');
                    $currentMonthPayroll = Payroll::where('month', $currentMonth)
                         ->where('branch_id', Auth::user()->branch_id)
                         ->sum('net_salary');
            
                    // Attendance rate: % present days vs working days in current month
                    $workingDaysPerMonth = now()->daysInMonth;
                    $presentDays = Attendance::whereMonth('date', now()->month)
                          ->where('branch_id', Auth::user()->branch_id)
                        ->where('status', 'present')
                        ->count();

                        $denominator = $totalEmployees * $workingDaysPerMonth;
                        $attendanceRate = $denominator > 0
                            ? round(($presentDays / $denominator) * 100, 2)
                            : 0;
            
                   
                    // Low attendance alerts: employees with >3 absences this month
                    $lowAttendanceAlerts = Attendance::select('employee_id')
                        ->whereMonth('date', now()->month)
                        ->where('branch_id', Auth::user()->branch_id)
                        ->where('status', 'absent')
                        ->groupBy('employee_id')
                        ->havingRaw('COUNT(*) > ?', [3])
                        ->with('employee')
                        ->get()
                        ->map(function($row) {
                            return (object)[
                                'employee' => $row->employee,
                                'absences' => Attendance::where('employee_id', $row->employee_id)
                                    ->whereMonth('date', now()->month)
                                    ->where('status', 'absent')
                                    ->count()
                            ];
                        });
            
                   
$currentYear = now()->year;
$currentMonth = now()->month;

$months = collect();
         
for ($m = $currentMonth; $m >= 1; $m--) {
    $monthStr = Carbon::createFromDate($currentYear, $m, 1)->format('Y-m');

    $payrolls = Payroll::where('month', $monthStr)
        ->where('branch_id', Auth::user()->branch_id)
        ->with('contributions.contribution') // eager load
        ->get();

    if ($payrolls->isEmpty()) {
        $months->push([
            'month' => $monthStr,
            'exists' => false,
            'paid_employees' => 0,
            'total_amount' => 0,
            'nssf' => 0,
            'nhif' => 0,
            'paye' => 0,
            'net_paid' => 0,
            'id' => null,
            'reference' => null,
        ]);
        continue;
    }

    $totalNssf = 0;
    $totalNhif = 0;
    $totalPaye = 0;
    $totalNetPaid = 0;

    foreach ($payrolls as $payroll) {
        $basic = floatval($payroll->basic_salary);
        $allowance = floatval($payroll->allowance);

        $nssf = 0;
        $nhif = 0;
        $paye = 0;

        foreach ($payroll->contributions as $contrib) {
            $name = strtoupper($contrib->contribution->name ?? '');
            $type = strtolower($contrib->contribution->type ?? 'fixed'); // fixed or percentage
            $rate = floatval($contrib->contribution->rate ?? '');

            $amount = 0;
            if ($type === 'percentage') {
                $amount = ($rate / 100) * $basic;
            } else {
                $amount = $rate;
            }

            switch ($name) {
                case 'NSSF': $nssf += $amount; break;
                case 'NHIF': $nhif += $amount; break;
                case 'PAYE': $paye += $amount; break;
            }
        }

        $net = ($basic + $allowance) - ($nssf + $nhif + $paye);

        $totalNssf += $nssf;
        $totalNhif += $nhif;
        $totalPaye += $paye;
        $totalNetPaid += $net;
    }

    $months->push([
        'month' => $monthStr,
        'exists' => true,
        'paid_employees' => $payrolls->count(),
        'total_amount' => $totalNetPaid,
        'nssf' => $totalNssf,
        'nhif' => $totalNhif,
        'paye' => $totalPaye,
        'net_paid' => $totalNetPaid,
        'id' => $payrolls->first()->id ?? null,
        'reference' => $payrolls->first()->reference ?? null,
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
                        'nssf',
                        'nhif',
                        'paye',
                        'employees',
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
    
        foreach ($employees as $employeeId => $data) {
            if (!isset($data['selected'])) {
                continue;
            }
    
            // Store payroll
            $payroll = Payroll::create([
                'employee_id' => $employeeId,
                'branch_id' => Auth::user()->branch_id,
                'reference' => date('YhisH'),
                'month' => $month,
                'basic_salary' => $data['basic_salary'],
                'allowance' => $data['allowance'] ?? 0,
                'net_salary' => ($data['basic_salary'] + ($data['allowance'] ?? 0)) - ($data['deduction'] ?? 0),
            ]);
    
            // Store related contributions
            if (!empty($data['contributions'])) {
                foreach ($data['contributions'] as $contribName) {
                    PayrollContribution::create([
                        'payroll_id' => $payroll->id,
                        'contribution_id' => $contribName,
                        'name' => $contribName,
                        'rate' 
                    ]);
                }
            }
        }
    
        return redirect()->back()->with('message', 'Payroll generated with contributions.');
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

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'month' => 'required|date_format:Y-m',
    //     ]);

    //     $month = $request->month;
    //     $startDate = Carbon::parse($month)->startOfMonth();
    //     $endDate = Carbon::parse($month)->endOfMonth();

    //     $employees = Employee::all();

    //     foreach ($employees as $employee) {
    //         $presentDays = Attendance::where('employee_id', $employee->id)
    //             ->whereBetween('date', [$startDate, $endDate])
    //             ->where('status', 'Present')
    //             ->count();

    //         $totalWorkingDays = $endDate->diffInWeekdays($startDate);
    //         $absentDays = $totalWorkingDays - $presentDays;
    //         $perDayRate = $employee->basic_salary / $totalWorkingDays;
    //         $deductions = $absentDays * $perDayRate;
    //         $netSalary = $employee->basic_salary - $deductions;

    //         Payroll::updateOrCreate(
    //             ['employee_id' => $employee->id, 'month' => $month . '-01'],
    //             [
    //                 'basic_salary' => $employee->basic_salary,
    //                 'days_present' => $presentDays,
    //                 'deductions' => round($deductions, 2),
    //                 'net_salary' => round($netSalary, 2),
    //             ]
    //         );
    //     }

    //     return redirect()->route('payrolls.index')->with('success', 'Payroll generated successfully.');
    // }

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
        return response()->json(['success' => false, 'message' => 'Employee ID or Month is missing.']);
    }

    $payroll = Payroll::where('employee_id', $employeeId)
        ->where('month', $monthRaw)
        ->with('employee')
        ->first();

    if (!$payroll) {
        return response()->json(['success' => false, 'message' => 'No payroll data found for selected employee and month.']);
    }

    $month = \Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y');

    $contributions = PayrollContribution::where('payroll_id', $payroll->id)
        ->with('contribution')
        ->get();

    $basic_salary = floatval($payroll->basic_salary ?? 0);
    $allowance = floatval($payroll->allowance ?? 0);
    $deduction = floatval($payroll->deduction ?? 0); // optional

    $nssf = 0;
    $paye = 0;
    $nhif = 0;

    foreach ($contributions as $p) {
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
                case 'PAYE': $paye = $amount; break;
                case 'NHIF': $nhif = $amount; break;
            }
        }
    }

    $net_salary = ($basic_salary + $allowance) - ($nssf + $paye + $nhif + $deduction);

    $slip = [
        'employee_name' => $payroll->employee->name ?? 'N/A',
        'basic_salary' => number_format($basic_salary, 2),
        'allowance' => number_format($allowance, 2),
        'deduction' => number_format($deduction, 2),
        'nssf' => number_format($nssf, 2),
        'paye' => number_format($paye, 2),
        'nhif' => number_format($nhif, 2),
        'net_salary' => number_format($net_salary, 2),
        'reference' => $payroll->reference ?? 'N/A',
        'description' => $payroll->description ?? '-',
    ];

    return response()->json([
        'success' => true,
        'month' => $month,
        'slip' => $slip
    ]);
}



    // if ($payroll) {
    //     return response()->json([
    //         'success' => true,
    //         'month' => Carbon::parse($month)->format('F Y'),
    //         'slip' => [
    //             'employee_name' => $payroll->employee->name,
    //             'basic_salary' => number_format($payroll->basic_salary, 2),
    //             'allowance' => number_format($payroll->allowance, 2),
    //             'deduction' => number_format($payroll->deduction, 2),
    //             'description' => $payroll->description,
    //             'net_salary' => number_format($payroll->net_salary, 2),
    //             'reference' => $payroll->reference,
    //         ]
    //     ]);
    // }

    // return response()->json(['success' => false, 'message' => 'No salary slip found for the selected employee and month.']);
// }


public function downloadSalarySlip(Request $request)
{
    $settings = DB::table('general_settings')
        ->select('business_name')
        ->first(); // use first() instead of get()

    $employeeId = $request->employee_id;
    $monthRaw = $request->month; // e.g. '2025-01'

    $payroll = Payroll::where('employee_id', $employeeId)
        ->where('month', $monthRaw)
        ->with('employee')
        ->first();

    if (!$payroll) {
        abort(404, 'Payroll not found for the selected employee and month.');
    }

    $month = \Carbon\Carbon::createFromFormat('Y-m', $request->month)->format('F Y');

    $contributions = PayrollContribution::where('payroll_id', $payroll->id)
        ->with('contribution')
        ->get();

    $basic_salary = floatval($payroll->basic_salary ?? 0);
    $allowance = floatval($payroll->allowance ?? 0);
    $deduction = floatval($payroll->deduction ?? 0);

    $nssf = 0;
    $paye = 0;
    $nhif = 0;

    foreach ($contributions as $p) {
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
                case 'PAYE': $paye = $amount; break;
                case 'NHIF': $nhif = $amount; break;
            }
        }
    }

    $net_salary = ($basic_salary + $allowance) - ($nssf + $paye + $nhif + $deduction);

    $slip = [
        'employee_name' => $payroll->employee->name ?? 'N/A',
        'basic_salary' => number_format($basic_salary, 2),
        'allowance' => number_format($allowance, 2),
        'deduction' => number_format($deduction, 2),
        'nssf' => number_format($nssf, 2),
        'paye' => number_format($paye, 2),
        'nhif' => number_format($nhif, 2),
        'net_salary' => number_format($net_salary, 2),
        'reference' => $payroll->reference ?? 'N/A',
        'description' => $payroll->description ?? '-',
    ];

    // Generate PDF
    $pdf = PDF::loadView('payrolls.slip_pdf', [
        'slip' => $slip,
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

    // Get the first for month display
    $mainPayroll = Payroll::find($payrollIds->first());
    $month = \Carbon\Carbon::parse($mainPayroll->month)->format('F Y');

    // Load contributions for all payrolls in that batch
    $contributions = PayrollContribution::whereIn('payroll_id', $payrollIds)
        ->with(['payroll.employee', 'contribution'])
        ->get();

// return $contributions;

    // Group by employee
    $grouped = $contributions->groupBy(function ($item) {
        return $item->payroll->employee_id ?? 0;
    });

    $slips = $grouped->map(function ($rows) {
        $first = $rows->first();

        $basic_salary = floatval($first->payroll->basic_salary ?? 0);
        $allowance = floatval($first->payroll->allowance ?? 0);

        $nssf = 0;
        $paye = 0;
        $nhif = 0;

        foreach ($rows as $p) {
            $contribution = $p->contribution;
            if ($contribution) {
                $type = strtolower($contribution->type); // 'fixed' or 'percentage'
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
                    case 'PAYE': $paye = $amount; break;
                    case 'NHIF': $nhif = $amount; break;

                }
            }
        }
        

        $net_salary = ($basic_salary + $allowance) - ($nssf + $paye + $nhif);

        return [
            'employee_name' => $first->payroll->employee->name ?? 'N/A',
            'basic_salary' => number_format($basic_salary, 2),
            'allowance' => number_format($allowance, 2),
            'nssf' => number_format($nssf, 2),
            'paye' => number_format($paye, 2),
            'nhif' => number_format($nhif, 2),
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

    $month = $payrollIds->first()->month ?? 'N/A';

    if ($payrollIds->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No data found.']);
    }

    // Get the first for month display
    $mainPayroll = Payroll::find($payrollIds->first());
    $month = \Carbon\Carbon::parse($mainPayroll->month)->format('F Y');

    // Load contributions for all payrolls in that batch
    $contributions = PayrollContribution::whereIn('payroll_id', $payrollIds)
        ->with(['payroll.employee', 'contribution'])
        ->get();

// return $contributions;

    // Group by employee
    $grouped = $contributions->groupBy(function ($item) {
        return $item->payroll->employee_id ?? 0;
    });

    $slips = $grouped->map(function ($rows) {
        $first = $rows->first();

        $basic_salary = floatval($first->payroll->basic_salary ?? 0);
        $allowance = floatval($first->payroll->allowance ?? 0);

        $nssf = 0;
        $paye = 0;
        $nhif = 0;

        foreach ($rows as $p) {
            $contribution = $p->contribution;
            if ($contribution) {
                $type = strtolower($contribution->type); // 'fixed' or 'percentage'
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
                    case 'PAYE': $paye = $amount; break;
                    case 'NHIF': $nhif = $amount; break;

                }
            }
        }
        

        $net_salary = ($basic_salary + $allowance) - ($nssf + $paye + $nhif);

        return [
            'employee_name' => $first->payroll->employee->name ?? 'N/A',
            'basic_salary' => $basic_salary,
            'allowance' => $allowance,
            'nssf' => $nssf,
            'paye' => $paye,
            'nhif' => $nhif,
            'net_salary' => $net_salary,
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
    // $slips = Payroll::with(['employee.bankAccount'])
    //             ->where('reference', $reference)
    //             ->get();

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

// return $contributions;

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
                    $amount = ($rate / 100) * $basic_salary;
                }
    
                switch ($name) {
                    case 'NSSF': $nssf = $amount; break;
                    case 'PAYE': $paye = $amount; break;
                    case 'NHIF': $nhif = $amount; break;
                }
            }
        }
    
        $net_salary = ($basic_salary + $allowance) - ($nssf + $paye + $nhif);
    
        // Bank account details
        $bankAccount = $employee?->bankAccount?->first(); // or use a loop if multiple
    
        return [
            'employee_name' => $employee->name ?? 'N/A',
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


// private function calculatePaye($salary)
// {
//     if ($salary <= 270000) {
//         return 0;
//     } elseif ($salary <= 520000) {
//         return ($salary - 270000) * 0.08;
//     } elseif ($salary <= 760000) {
//         return 20000 + ($salary - 520000) * 0.20;
//     } elseif ($salary <= 1000000) {
//         return 68000 + ($salary - 760000) * 0.25;
//     } else {
//         return 128000 + ($salary - 1000000) * 0.30;
//     }
// }

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


}
