<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    // Store new department (you may already have this)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Department::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Department added successfully.');
    }

    // Update existing department
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Department updated successfully.');
    }

    // Delete department
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully.');
    }
}
