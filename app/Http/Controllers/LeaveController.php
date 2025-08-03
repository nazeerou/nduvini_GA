<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{

    public function index(Request $request)
{
    $statusTab = $request->input('tab', 'all'); // default to 'all'

    $query = Leave::with(['employee', 'leaveType', 'approver']);

    // Filters
    if ($request->employee_id) {
        $query->where('employee_id', $request->employee_id);
    }

    if ($request->leave_type_id) {
        $query->where('leave_type_id', $request->leave_type_id);
    }

    if ($request->start_date && $request->end_date) {
        $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
    }

    // "approval" tab shows only logged-in user's pending leaves
    if ($statusTab === 'approval') {
        $query->where('status', 'pending')
              ->where('employee_id', auth()->user()->employee->id); // show own pending requests
    }

    $leaves = $query->latest()->paginate(10);
    $employees = \App\Models\Employee::all();
    $leaveTypes = \App\Models\LeaveType::all();

    return view('leaves.index', compact('leaves', 'employees', 'leaveTypes', 'statusTab'));
}

    // Show leave application form

public function store(Request $request)
{
    $data = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'leave_type_id' => 'required|exists:leave_types,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|string',
        'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
    ]);

    if ($request->hasFile('attachment')) {
        $data['attachment'] = $request->file('attachment')->store('attachments', 'public');
    }

    $data['status'] = 'pending';
    Leave::create($data);

    return redirect()->back()->with('success', 'Leave request submitted.');
}

public function update(Request $request, Leave $leave)
{
    $data = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'leave_type_id' => 'required|exists:leave_types,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|string',
        'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
    ]);

    if ($request->hasFile('attachment')) {
        if ($leave->attachment) {
            Storage::disk('public')->delete($leave->attachment);
        }
        $data['attachment'] = $request->file('attachment')->store('attachments', 'public');
    }

    $leave->update($data);

    return redirect()->back()->with('success', 'Leave request updated.');
}

    
}
