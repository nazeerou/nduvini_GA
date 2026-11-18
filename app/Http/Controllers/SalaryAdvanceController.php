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
use DB;

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


public function downloadPdf(Request $request)
{
    $month = $request->input('month');   // '' or '01'..'12'
    $year  = $request->input('year');    // '' or 'YYYY'

    $query = SalaryAdvance::query()
        ->with('employee')
        ->leftJoin('users as approvers',  'approvers.id',  '=', 'salary_advances.user_id')
        ->leftJoin('users as requesters', 'requesters.id', '=', 'salary_advances.paid_by')
        ->leftJoin('roles as approver_roles',  'approver_roles.id',  '=', 'approvers.role_id')
        ->leftJoin('roles as requester_roles', 'requester_roles.id', '=', 'requesters.role_id')
        ->select(
            'salary_advances.*',
            DB::raw("CONCAT(approvers.fname, ' ', approvers.lname) as approver_name"),
            DB::raw("CONCAT(requesters.fname, ' ', requesters.lname) as requester_name"),
            'approvers.signature as approver_signature',
            'requesters.signature as requester_signature',
            'approver_roles.name as approver_role',
            'requester_roles.name as requester_role'
        )
        ->where('salary_advances.branch_id', $this->branchId());

    if ($year && $month) {
        $query->where('salary_advances.month', $year . '-' . $month);
    } elseif ($year) {
        $query->where('salary_advances.month', 'LIKE', $year . '-%');
    } elseif ($month) {
        $query->where('salary_advances.month', 'LIKE', '%-' . $month);
    }

    $salary_advances = $query->orderBy('salary_advances.month', 'desc')->get();

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


public function markPaid(Request $request)
{
    $request->validate([
        'advance_ids'   => 'required|array',
        'advance_ids.*' => 'integer',
    ]);

    \App\Models\SalaryAdvance::whereIn('id', $request->advance_ids)
        ->where('branch_id', auth()->user()->branch_id)
        ->update([
            'is_paid'  => true,
            'status'   => 'paid',
            'paid_at'  => now(),
            'paid_by'  => auth()->id(),
        ]);

    return response()->json(['message' => 'Advances marked as paid.']);
}

public function update(Request $request, $id)
{
    $request->validate([
        'amount'  => 'required|numeric|min:0.01',
        'is_paid' => 'required|in:0,1',
    ]);

    $advance = SalaryAdvance::where('id', $id)
        ->where('branch_id', Auth::user()->branch_id)
        ->firstOrFail();

    $advance->amount = $request->amount;
    $advance->is_paid = $request->is_paid;
    $advance->save();

    return response()->json([
        'success' => true,
        'message' => 'Salary advance updated successfully.'
    ]);
}


public function destroy($id)
{
    $advance = SalaryAdvance::where('id', $id)
        ->where('branch_id', Auth::user()->branch_id)
        ->firstOrFail();

    $advance->delete();

    return response()->json([
        'success' => true,
        'message' => 'Salary advance deleted successfully.'
    ]);
}
}