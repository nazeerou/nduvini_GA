<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        LeaveType::create($request->only('name', 'description'));
        return redirect('leaves?tab=types')->with('success', 'Leave type added.');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);
        LeaveType::findOrFail($id)->update($request->only('name', 'description'));
        return redirect('leaves?tab=types')->with('success', 'Leave type updated.');
    }

    public function destroy($id)
    {
        LeaveType::destroy($id);
        return redirect('leaves?tab=types')->with('success', 'Leave type deleted.');
    }
}
