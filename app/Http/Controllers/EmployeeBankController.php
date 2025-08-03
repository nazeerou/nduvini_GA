<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankAccount;

class EmployeeBankController extends Controller
{
    //

public function store(Request $request)
{
    BankAccount::create($request->all());
    return back()->with('success', 'Bank account added.');
}

public function update(Request $request, $id)
{
    BankAccount::findOrFail($id)->update($request->all());
    return back()->with('success', 'Bank account updated.');
}

public function destroy($id)
{
    BankAccount::findOrFail($id)->delete();
    return back()->with('success', 'Bank account deleted.');
}

}
