<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Supplier;
use App\Brand;
use App\Product;
use Auth;

class SupplierController extends Controller
{
    
    public function index() {

        $suppliers = DB::table('suppliers')
                   ->select('*')
                   ->where('branch_id', Auth::user()->branch_id)
                   ->get();

        return view('pages.supplier', compact('suppliers'));
    }
    

    public function addSupplier(Request $data){

        $data->validate([
            'supplier_name' => 'required',
        ]);


      $suppliers = Supplier::create([
          'supplier_name' => $data['supplier_name'], 
          'phone' => $data['phone'],
          'branch_id' => Auth::user()->branch_id,
        ]);

      if(!$suppliers){
        return 'Error!';
      } 
      return redirect('/suppliers')->with('message', 'Supplier added Successful!');

    }



    public function editSupplier($id) {

      $suppliers = Supplier::all();

     return view('pages.edit-supplier', compact('suppliers'));

}


public function updateSupplier (Request $request) {

      $update = DB::table('suppliers')
                ->where('id', $request->id)
                ->update([
                  "supplier_name" => $request['supplier_name'],
                  "phone" => $request['phone'],
                  ]);

      return redirect('suppliers')->with('message', 'Supplier updated successful!');  

}

public function deleteSupplier ($id){

        $suppliers = Supplier::where('id', $id);

        $suppliers->delete();

      return redirect()->back()->with('message', 'Supplier deleted');

   }

}
