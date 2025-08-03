<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use Illuminate\Http\Request;

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
            'type' => 'required|in:percentage,fixed',
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
            'type' => 'required|in:fixed,percentage',
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
}
