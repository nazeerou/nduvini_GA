<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('employee')->orderBy('date', 'desc')->get();
        return view('attendances.index', compact('attendances'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id.*' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status.*' => 'required|in:Present,Absent,Leave',
        ]);

        foreach ($request->employee_id as $index => $id) {
            Attendance::updateOrCreate(
                ['employee_id' => $id, 'date' => now()],
                ['status' => $request->status[$index]] // [$index]
            );
        }

        return redirect()->route('attendances.index')->with('success', 'Attendance recorded successfully.');
    }
}
