<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payroll;
use PDF;
use App;
use Barryvdh\Snappy;

class SalaryAdvanceController extends Controller
{
    /**
     * Current branch id — single source of truth so all queries stay consistent.
     */
    protected function branchId()
    {
        return Auth::user()->branch_id;
    }

    public function index()
    {
        $advances = SalaryAdvance::with('employee')
            ->where('branch_id', $this->branchId())
            ->orderByDesc('created_at')
            ->get();

        $employees = Employee::all();

        return view('payrolls.salary_advances.index', compact('advances', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount'      => 'required|numeric|min:0',
            'month'       => 'required|date_format:Y-m',
        ]);

        SalaryAdvance::create([
            'employee_id' => $request->employee_id,
            'amount'      => $request->amount,
            'month'       => $request->month,
            'user_id'     => Auth::id(),
            'branch_id'   => $this->branchId(),
        ]);

        return redirect()->back()->with('success', 'Salary advance added successfully.');
    }

    public function edit($id)
    {
        $advance = SalaryAdvance::where('branch_id', $this->branchId())
            ->findOrFail($id);

        $employees = Employee::all();

        return view('payrolls.salary_advances.edit', compact('advance', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $advance = SalaryAdvance::where('branch_id', $this->branchId())
            ->findOrFail($id);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount'      => 'required|numeric|min:0',
            'month'       => 'required|date_format:Y-m',
        ]);

        $advance->update([
            'employee_id' => $request->employee_id,
            'amount'      => $request->amount,
            'month'       => $request->month,
        ]);

        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance updated successfully.');
    }

    public function destroy($id)
    {
        $advance = SalaryAdvance::where('branch_id', $this->branchId())
            ->findOrFail($id);

        $advance->delete();

        return redirect()->back()->with('success', 'Salary advance deleted.');
    }

    public function downloadPdf(Request $request)
    {
        $month = $request->input('month');   // '' or '01'..'12'
        $year  = $request->input('year');    // '' or 'YYYY'

        // Branch scoping is applied FIRST, before the optional month/year filters
        $query = SalaryAdvance::with('employee')
            ->where('branch_id', $this->branchId());

        if ($year && $month) {
            $query->where('month', $year . '-' . $month);
        } elseif ($year) {
            $query->where('month', 'LIKE', $year . '-%');
        } elseif ($month) {
            $query->where('month', 'LIKE', '%-' . $month);
        }

        $salary_advances = $query->orderBy('month', 'desc')->get();

        // Human-readable period for the PDF header
        $period = 'All Period';
        if ($year && $month) {
            $period = \Carbon\Carbon::createFromFormat('Y-m', $year . '-' . $month)->format('F Y');
        } elseif ($year) {
            $period = (string) $year;
        } elseif ($month) {
            $period = \Carbon\Carbon::createFromFormat('m', $month)->format('F');
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('payrolls.salary_advances_pdf', compact(
            'salary_advances',
            'month',
            'year',
            'period'
        ));

        return $pdf->stream(
            'salary-advances-' . ($year ?: 'all') . '-' . ($month ?: 'all') . '.pdf'
        );
    }


    // In SalaryAdvanceController
public function submitForApproval($id)
{
    $advance = SalaryAdvance::where('branch_id', $this->branchId())->with('employee')->findOrFail($id);

    $employeeName = trim(($advance->employee->firstname ?? '') . ' ' . ($advance->employee->surname ?? ''));
    ApprovalService::submit(
        'salary_advance',
        'salary_advances',
        $advance->id,
        'ADV-' . str_pad($advance->id, 5, '0', STR_PAD_LEFT),
        "Salary Advance — {$employeeName} ({$advance->month})",
        (float) $advance->amount,
        [
            'employee' => $employeeName,
            'month'    => $advance->month,
            'amount'   => $advance->amount,
        ]
    );

    return back()->with('success', 'Salary advance submitted to HR for approval.');
}
}