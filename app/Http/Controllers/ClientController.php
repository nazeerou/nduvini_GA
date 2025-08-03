<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Client;
use App\Models\Brand;
use App\Models\Product;
use Auth;

class ClientController extends Controller
{
    
    public function index() {

        $clients = DB::table('clients')
                   ->select('*')
                   ->WHERE('clients.branch_id', Auth::user()->branch_id)
                   ->get();

        return view('pages.clients', compact('clients'));
    }
    

    public function addClient(Request $data){

        $data->validate([
            'client_name' => 'required',
        ]);


      $clients = Client::create([
          'branch_id' => Auth::user()->branch_id,
          'client_name' => $data['client_name'], 
          'abbreviation' => $data['abbreviation'], 
          'address' => $data['address'], 
          'place' => $data['place'], 
          'phone' => $data['phone'],
          "tin" => $data['tin'],
          "vrn" => $data['vrn'],
          "email" => $data['email']
        ]);

      if(!$clients){
        return redirect()->back()->with('error', 'Client  Not added !');
      } 
      return redirect('/clients')->with('message', 'Client added Successful!');

    }



    public function editClient($id) {

      $clients = Client::where('id', $id)->get();

     return view('pages.edit-clients', compact('clients'));

}


public function updateClient(Request $request) {

      $update = DB::table('clients')
                ->where('id', $request->id)
                ->update([
                  "client_name" => $request['client_name'],
                  "abbreviation" => $request['abbreviation'],
                  "place" => $request['place'],
                  "address" => $request['address'],
                  "phone" => $request['phone'],
                  "tin" => $request['tin'],
                  "vrn" => $request['vrn'],
                  "email" => $request['email']
                  ]);

      return redirect()->back()->with('message', 'Client updated successful!');  

}

public function deleteClient($id){

        $clients = Client::where('id', $id);

        $clients->delete();

      return redirect()->back()->with('message', 'Client deleted');

   }

}
