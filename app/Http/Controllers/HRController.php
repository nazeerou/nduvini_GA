<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Designation;
use App\Models\ContractType;
use App\Models\SalaryGroup;
use App\Models\Bank;

class HRController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //        
        return view('hrm.index', [
            'departments' => Department::all(),
            'designations' => Designation::all(),
            'contract_types' => ContractType::all(),
            'salary_groups' => SalaryGroup::where('branch_id', Auth:user()->branch_id)->get(),
            'banks' => Bank::where('branch_id', Auth:user()->branch_id)->get(),

        ]);      
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
