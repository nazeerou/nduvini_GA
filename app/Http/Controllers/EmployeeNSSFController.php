<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NssfDetail;
use Illuminate\Support\Facades\Validator;

class EmployeeNssfController extends Controller
{
    /**
     * Store a newly created NSSF entry.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'member_number' => 'required|string|max:50|unique:nssf_details,member_number',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        NssfDetail::create([
            'employee_id' => $request->employee_id,
            'member_number' => $request->member_number,
        ]);

        return redirect()->back()->with('success', 'NSSF member number added successfully.');
    }

    /**
     * Update the specified NSSF entry.
     */
    public function update(Request $request, $id)
    {
        $nssf = NssfDetail::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'member_number' => 'required|string|max:50|unique:nssf_details,member_number,' . $nssf->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $nssf->update([
            'member_number' => $request->member_number,
        ]);

        return redirect()->back()->with('success', 'NSSF member number updated successfully.');
    }

    /**
     * Remove the specified NSSF entry.
     */
    public function destroy($id)
    {
        $nssf = NssfDetail::findOrFail($id);
        $nssf->delete();

        return redirect()->back()->with('success', 'NSSF member number deleted successfully.');
    }
}
