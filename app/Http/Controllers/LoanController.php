<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class LoanController extends Controller
{
    /** Sum of repayments for a single loan. */
    protected function repaidFor(Loan $loan): float
    {
        return (float) $loan->loanRepayments()->sum('amount');
    }

    /** Derive balance = principal − repayments (never below 0). */
    protected function balanceFor(Loan $loan): float
    {
        return max(0, (float) $loan->principal - $this->repaidFor($loan));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'       => ['required', 'exists:employees,id'],
            'type'              => ['required', 'string', 'max:100'],
            'principal'         => ['required', 'numeric', 'min:0'],
            'monthly_deduction' => ['required', 'numeric', 'min:0'],
            'start_date'        => ['required', 'date'],
        ]);

        $employee = Employee::where('id', $validated['employee_id'])
            ->where('branch_id', Auth::user()->branch_id)
            ->firstOrFail();

        // Balance is derived from repayments; a brand-new loan starts at full principal.
        Loan::create([
            'employee_id'       => $employee->id,
            'type'              => $validated['type'],
            'principal'         => $validated['principal'],
            'monthly_deduction' => $validated['monthly_deduction'],
            'balance'           => $validated['principal'], // cached; recalculated on read
            'start_date'        => $validated['start_date'],
            'branch_id'         => Auth::user()->branch_id,
        ]);

        return back()->with('success', 'Loan added successfully.');
    }

    public function update(Request $request, Loan $loan)
    {
        abort_unless((int) $loan->branch_id === (int) Auth::user()->branch_id, 403);

        $validated = $request->validate([
            'employee_id'       => ['required', 'exists:employees,id'],
            'type'              => ['required', 'string', 'max:100'],
            'principal'         => ['required', 'numeric', 'min:0'],
            'monthly_deduction' => ['required', 'numeric', 'min:0'],
            'start_date'        => ['required', 'date'],
        ]);

        $loan->update([
            'employee_id'       => $validated['employee_id'],
            'type'              => $validated['type'],
            'principal'         => $validated['principal'],
            'monthly_deduction' => $validated['monthly_deduction'],
            'start_date'        => $validated['start_date'],
            'balance'           => max(0, (float) $validated['principal'] - $this->repaidFor($loan)),
        ]);

        return back()->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan)
    {
        abort_unless((int) $loan->branch_id === (int) Auth::user()->branch_id, 403);

        DB::transaction(function () use ($loan) {
            $loan->loanRepayments()->delete();
            $loan->delete();
        });

        return back()->with('success', 'Loan deleted successfully.');
    }

    /**
     * Record a repayment against a loan.
     */
   public function recordRepayment(Request $request, Loan $loan)
{
    abort_unless(
        (int) $loan->branch_id === (int) Auth::user()->branch_id,
        403,
        'This loan does not belong to your branch.'
    );

    /* ---------- Validation ---------- */
    $validated = $request->validate([
        'employee_id'    => ['required', 'integer', 'exists:employees,id'],
        'amount'         => ['required', 'numeric', 'min:0.01'],
        'deduction_date' => ['required', 'date'],   // full date e.g. "2025-01-15"
        'notes'          => ['nullable', 'string', 'max:500'],
    ]);

    /* ---------- Guard: employee must match the loan's owner ---------- */
    if ((int) $validated['employee_id'] !== (int) $loan->employee_id) {
        $message = 'Employee does not match the loan owner.';
        return $request->expectsJson()
            ? response()->json([
                'status'  => 'error',
                'message' => $message,
                'errors'  => ['employee_id' => [$message]],
            ], 422)
            : back()->withErrors(['employee_id' => $message])->withInput();
    }

    /* ---------- Normalise dates ----------
     | Store deduction_date as FULL date (Y-m-d) so rollbackPayroll()
     | — which filters on "YYYY-MM-01" — will not miss it.
     | NOTE: if you record a manual repayment on 2025-01-15, rollback
     | of month 2025-01 will look for 2025-01-01 and miss it. See §3
     | below for the fix to rollbackPayroll().
     */
    $repaymentDate = Carbon::parse($validated['deduction_date'])->toDateString(); // "2025-01-15"

    /* ---------- Balance check (from real repayments, never cached) ---------- */
    $principal = (float) $loan->principal;
    $repaid    = (float) LoanRepayment::where('loan_id', $loan->id)->sum('amount');
    $balance   = max(0, $principal - $repaid);

    if ((float) $validated['amount'] > $balance) {
        $message = 'Amount (TZS ' . number_format($validated['amount'], 2)
                 . ') exceeds outstanding balance (TZS ' . number_format($balance, 2) . ').';

        return $request->expectsJson()
            ? response()->json([
                'status'  => 'error',
                'message' => $message,
                'errors'  => ['amount' => [$message]],
                'balance' => $balance,
            ], 422)
            : back()->withErrors(['amount' => $message])->withInput();
    }

    try {
        $repayment = DB::transaction(function () use ($loan, $validated, $repaymentDate) {

            /* ---------- Build payload, only include columns that exist ---------- */
            $payload = [
                'loan_id'     => $loan->id,
                'employee_id' => (int) $validated['employee_id'],
                'amount'      => (float) $validated['amount'],
            ];

            // Both date columns (if present) get the full Y-m-d date.
            if (Schema::hasColumn('loan_repayments', 'deduction_date')) {
                $payload['deduction_date'] = $repaymentDate;    // "2025-01-15"
            }
            if (Schema::hasColumn('loan_repayments', 'repayment_date')) {
                $payload['repayment_date'] = $repaymentDate;    // "2025-01-15"
            }
            if (Schema::hasColumn('loan_repayments', 'branch_id')) {
                $payload['branch_id'] = $loan->branch_id;
            }
            if (Schema::hasColumn('loan_repayments', 'notes')) {
                $payload['notes'] = $validated['notes'] ?? null;
            }

            /* ---------- Insert ---------- */
            $repayment = LoanRepayment::create($payload);

            /* ---------- Sync cached loan balance ---------- */
            $totalRepaid = (float) LoanRepayment::where('loan_id', $loan->id)->sum('amount');
            $loan->update(['balance' => max(0, (float) $loan->principal - $totalRepaid)]);

            return $repayment;
        });

        $freshBalance = max(0, (float) $loan->principal -
            (float) LoanRepayment::where('loan_id', $loan->id)->sum('amount'));

        return $request->expectsJson()
            ? response()->json([
                'status'    => 'success',
                'message'   => 'Repayment recorded successfully.',
                'repayment' => [
                    'id'             => $repayment->id,
                    'employee_id'    => (int) $validated['employee_id'],
                    'amount'         => (float) $repayment->amount,
                    'deduction_date' => $repaymentDate,
                    'notes'          => $validated['notes'] ?? null,
                ],
                'loan'      => [
                    'id'        => $loan->id,
                    'principal' => (float) $loan->principal,
                    'balance'   => $freshBalance,
                ],
            ], 201)
            : back()->with('success', 'Repayment of TZS '
                . number_format((float) $repayment->amount, 2)
                . ' recorded for employee #' . $validated['employee_id']
                . '. New balance: TZS ' . number_format($freshBalance, 2) . '.');

    } catch (\Throwable $e) {
        \Log::error('recordRepayment failed', [
            'loan_id'     => $loan->id,
            'employee_id' => $validated['employee_id'] ?? null,
            'user_id'     => Auth::id(),
            'error'       => $e->getMessage(),
        ]);

        $message = 'Failed to record repayment: ' . $e->getMessage();

        return $request->expectsJson()
            ? response()->json(['status' => 'error', 'message' => $message], 500)
            : back()->withErrors(['repayment' => $message])->withInput();
    }
}


/**
 * Download a full repayment statement for a single loan as PDF.
 *
 * GET /loans/{loan}/statement-pdf
 */
public function repaymentStatementPdf($loan)
{
    $loanModel = \App\Models\Loan::with(['employee', 'loanRepayments'])
        ->where('branch_id', \Illuminate\Support\Facades\Auth::user()->branch_id)
        ->findOrFail($loan);

    $repayments = $loanModel->loanRepayments->sortBy(function ($r) {
        return $r->repayment_date
            ?? $r->payment_date
            ?? $r->paid_at
            ?? $r->created_at;
    })->values();

    $totalRepaid = (float) $repayments->sum('amount');
    $balance     = max(0, (float) $loanModel->principal - $totalRepaid);
    $progress    = (float) $loanModel->principal > 0
        ? min(100, ($totalRepaid / (float) $loanModel->principal) * 100)
        : 0;

    $pdf = PDF::loadView('payrolls.loan_statement_pdf', [
        'loanModel'   => $loanModel,
        'repayments'  => $repayments,
        'totalRepaid' => $totalRepaid,
        'balance'     => $balance,
        'progress'    => $progress,
    ])->setPaper('A4', 'portrait');

    $employeePart = optional($loanModel->employee)->surname ?: 'employee';
    $filename = 'loan-statement-' . $employeePart . '-' . $loanModel->id . '-' . now()->format('Ymd') . '.pdf';

    return $pdf->download($filename);
}


    public function statement($id)
    {
        $loan = Loan::with(['employee', 'loanRepayments'])
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        $principal = (float) $loan->principal;
        $repaid    = (float) $loan->loanRepayments->sum('amount');
        $balance   = max(0, $principal - $repaid);
        $progress  = $principal > 0 ? min(100, ($repaid / $principal) * 100) : 0;

        return view('payrolls.loan_statement', compact('loan', 'repaid', 'balance', 'progress'));
    }

    public function detailsJson($id)
    {
        $loan = Loan::with(['employee', 'loanRepayments'])
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        $principal = (float) $loan->principal;
        $repaid    = (float) $loan->loanRepayments->sum('amount');
        $balance   = max(0, $principal - $repaid);
        $progress  = $principal > 0 ? min(100, ($repaid / $principal) * 100) : 0;

        $name = trim(
            (optional($loan->employee)->firstname ?? '') . ' ' .
            (optional($loan->employee)->surname ?? '')
        );

        return response()->json([
            'id'                => $loan->id,
            'employee_id'       => optional($loan->employee)->id,
            'employee_name'     => $name ?: ('Employee #' . optional($loan->employee)->id),
            'type'              => $loan->type,
            'principal'         => $principal,
            'repaid'            => $repaid,
            'balance'           => $balance,
            'progress'          => round($progress, 2),
            'monthly_deduction' => (float) $loan->monthly_deduction,
            'start_date'        => $loan->start_date
                ? Carbon::parse($loan->start_date)->format('d M Y')
                : null,
            'repayments'        => $loan->loanRepayments->map(fn ($r) => [
                'id'     => $r->id,
                'amount' => (float) $r->amount,
                'date'   => $r->repayment_date
                    ? Carbon::parse($r->repayment_date)->format('d M Y')
                    : ($r->created_at ? Carbon::parse($r->created_at)->format('d M Y') : null),
                'notes'  => $r->notes ?? '',
            ])->values(),
        ]);
    }

    public function downloadPdf()
    {
        $loans = Loan::with(['employee', 'loanRepayments'])
            ->where('branch_id', Auth::user()->branch_id)
            ->orderBy('id')
            ->get();

        $grouped = $loans
            ->groupBy(fn ($l) => optional($l->employee)->id)
            ->map(function ($rows) {
                $emp  = optional($rows->first())->employee;
                $name = trim(
                    (optional($emp)->firstname ?? '') . ' ' .
                    (optional($emp)->surname ?? '')
                ) ?: ('Employee #' . optional($emp)->id);

                $totalPrincipal = (float) $rows->sum('principal');
                $totalRepaid    = (float) $rows->sum(fn ($l) => (float) $l->loanRepayments->sum('amount'));
                $totalBalance   = max(0, $totalPrincipal - $totalRepaid);

                return [
                    'employee_id'     => optional($emp)->id,
                    'employee_name'   => $name,
                    'loan_count'      => $rows->count(),
                    'total_principal' => $totalPrincipal,
                    'total_repaid'    => $totalRepaid,
                    'total_balance'   => $totalBalance,
                    'loans'           => $rows->values(),
                ];
            })
            ->sortBy('employee_name')
            ->values();

        $pdf = PDF::loadView('payrolls.loan_pdf', compact('loans', 'grouped'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('employee_loans_' . now()->format('Ymd_His') . '.pdf');
    }
}