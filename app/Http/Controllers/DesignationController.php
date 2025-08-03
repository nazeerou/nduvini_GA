<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
    
        Designation::create([
            'name' => $request->title,
        ]);
    
        return redirect()->back()->with('success', 'Designation added successfully.');
    }
    
    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
    
        $designation->update([
            'name' => $request->title,
        ]);
    
        return redirect()->back()->with('success', 'Designation updated successfully.');
    }
    
    public function destroy(Designation $designation)
    {
        $designation->delete();
    
        return redirect()->back()->with('success', 'Designation deleted successfully.');
    }
    
}
