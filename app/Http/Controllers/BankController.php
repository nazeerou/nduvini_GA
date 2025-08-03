<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
        return view('your.hr_settings_view', compact('banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:banks,name',
        ]);

        Bank::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('message', 'Bank added successfully.');
    }

    public function update(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:banks,name,' . $id,
        ]);

        $bank->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('message', 'Bank updated successfully.');
    }

    public function destroy($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->delete();

        return redirect()->back()->with('message', 'Bank deleted successfully.');
    }

    
}
