<?php

namespace App\Http\Controller;
use Auth;
use App\Models\SalaryGroup;
use Illuminate\Http\Request;


class SalaryGroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'basic_salary' => 'required|numeric|min:0',
        ]);
    
        SalaryGroup::create([
            'group_name' => $request->group_name,
            'basic_salary' => $request->basic_salary,
            'branch_id' => Auth::user()->branch_id
        ]);
    
        return redirect()->back()->with('success', 'Salary Group added successfully.');
    }
    
    public function update(Request $request, SalaryGroup $salaryGroup)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'basic_salary' => 'required|numeric|min:0',
        ]);
    
        $salaryGroup->update([
            'group_name' => $request->group_name,
            'basic_salary' => $request->basic_salary,
        ]);
    
        return redirect()->back()->with('success', 'Salary Group updated successfully.');
    }
    
    public function destroy(SalaryGroup $salaryGroup)
    {
        $salaryGroup->delete();
    
        return redirect()->back()->with('success', 'Salary Group deleted successfully.');
    }
    
}
