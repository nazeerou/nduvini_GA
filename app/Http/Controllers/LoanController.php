<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use App\Models\LoanRepayment;
use App\Models\Employee;
use PDF;

class LoanController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'employee_id'       => 'required|exists:employees,id',
            'type'              => 'required|string|max:100',
            'principal'         => 'required|numeric|min:0',
            'monthly_deduction' => 'required|numeric|min:0',
            'balance'           => 'required|numeric|min:0',
            'start_date'        => 'required|date',
        ]);
    
        Loan::create([
            'employee_id'       => $request->employee_id,
            'type'              => $request->type,
            'principal'         => $request->principal,
            'monthly_deduction' => $request->monthly_deduction,
            'balance'           => $request->balance,
            'start_date'        => $request->start_date,
            'branch_id' => Auth::user()->branch_id
        ]);

        return back()->with('message', 'Loan added.');
    }

    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'employee_id' => 'required',
            'type'              => 'required|string|max:100',
            'principal'         => 'required|numeric|min:0',
            'monthly_deduction' => 'required|numeric|min:0',
            'balance'           => 'required|numeric|min:0',
            'start_date'        => 'required|date',
        ]);

        $loan->update($request->all());

        return back()->with('success', 'Loan updated.');
    }

    public function destroy(Loan $loan)
    {
        $loan->delete();
        return back()->with('success', 'Loan deleted.');
    }


//     public function statement($id)
// {
//     $loan = LoanRepayment::with('employee')->findOrFail($id);

//     return view('payrolls.loan_statement', compact('loan'));
// }

public function statement($id)
{
    $loan = Loan::with(['employee', 'loanRepayments'])->findOrFail($id);

    return view('payrolls.loan_statement', compact('loan'));
}


public function downloadPdf()
{
    $loans = Loan::with('employee')->get();

    $pdf = Pdf::loadView('payrolls.loan_pdf', compact('loans'))
        ->setPaper('A4', 'landscape'); // Landscape for wide tables

    return $pdf->download('employee_loans.pdf');
}

}
