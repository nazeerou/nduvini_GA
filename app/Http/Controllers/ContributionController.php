<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AssignedEmployeeContribution;

class ContributionController extends Controller
{
    // Show all contributions
    public function index()
    {
        $contributions = Contribution::all();
        return view('contributions.index', compact('contributions'));
    }

    // Store new contribution
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:contributions,name',
            'type' => 'required',
            'rate' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        Contribution::create($request->all());

        return redirect()->back()->with('message', 'Contribution added successfully.');
    }

    // Update contribution
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage,tax',
            'rate' => 'required|numeric|min:0',
        ]);
    
        $contribution = Contribution::findOrFail($id);
        $contribution->update($request->only(['name', 'type', 'rate']));
    
        return redirect()->back()->with('message', 'Contribution updated successfully.');
    }
    

    // Delete contribution
    public function destroy(Contribution $contribution)
    {
        $contribution->delete();

        return redirect()->back()->with('message', 'Contribution deleted successfully.');
    }


    public function storeAssignedContribution(Request $request)
    {
        $employee = Employee::findOrFail($request->employee_id);
    
        $existing = $employee->contributions->pluck('id')->toArray();
        $newContributions = array_diff($request->input('contributions', []), $existing);
    
        foreach ($newContributions as $contributionId) {
            AssignedEmployeeContribution::create([
                'employee_id' => $employee->id,
                'contribution_id' => $contributionId,
            ]);
        }
    
        return back()->with('message', 'Contributions assigned.');
    }
    
    public function destroyAssignedContribution($employeeId, $contributionId)
    {
        AssignedEmployeeContribution::where('employee_id', $employeeId)
            ->where('contribution_id', $contributionId)
            ->delete();
    
        return back()->with('error', 'Contribution removed.');
    }
    
}
