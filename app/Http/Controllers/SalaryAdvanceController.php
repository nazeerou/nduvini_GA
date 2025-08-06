<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryAdvanceController extends Controller
{
    public function index()
    {
        $advances = SalaryAdvance::with('employee')->orderByDesc('created_at')->get();
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
        ]);

        return redirect()->back()->with('success', 'Salary advance added successfully.');
    }

    public function edit($id)
    {
        $advance = SalaryAdvance::findOrFail($id);
        $employees = Employee::all();

        return view('payrolls.salary_advances.edit', compact('advance', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $advance = SalaryAdvance::findOrFail($id);

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

        return redirect()->route('salary-advances.index')->with('success', 'Salary advance updated successfully.');
    }

    public function destroy($id)
    {
        $advance = SalaryAdvance::findOrFail($id);
        $advance->delete();

        return redirect()->back()->with('success', 'Salary advance deleted.');
    }
}
