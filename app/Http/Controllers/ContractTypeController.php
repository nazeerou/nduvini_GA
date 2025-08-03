<?php

namespace App\Http\Controllers;

use App\Models\ContractType;
use Illuminate\Http\Request;

class ContractTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        ContractType::create([
            'name' => $request->name,
        ]);
    
        return redirect()->back()->with('success', 'Contract Type added successfully.');
    }
    
    public function update(Request $request, ContractType $contractType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $contractType->update([
            'name' => $request->name,
        ]);
    
        return redirect()->back()->with('success', 'Contract Type updated successfully.');
    }
    
    public function destroy(ContractType $contractType)
    {
        $contractType->delete();
    
        return redirect()->back()->with('success', 'Contract Type deleted successfully.');
    }
    
}