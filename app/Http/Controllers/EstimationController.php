<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Year;
use App\Models\ClientPayment;
use App\Models\Estimation;
use App\Models\Labour;
use App\Models\Item;
use DB;
use PdfReport;
use PDF;
use Barryvdh\Snappy;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App;
use Mail;
use App\Models\Client;
use Carbon\Carbon;
use Auth;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\LabourEstimation;

class EstimationController extends Controller
{
 
    public function createEstimations() {

        $clients = DB::table('clients')
                    ->select('*')
                    ->where('clients.branch_id', Auth::user()->branch_id)
                    ->get();

        $products = DB::table('price_lists')
                    ->select('price_lists.id', 'items.item_name', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
                    ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
                    // ->groupBy('price_lists.client_id', 'products.pid', 'id', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
                    ->where('price_lists.branch_id', Auth::user()->branch_id)
                    ->where('price_lists.client_id', '0')
                    ->get();
        
         $labours = DB::table('labours')
                  ->select('*')
                  ->get();

        return view('estimations.create-estimates', compact('products', 'clients', 'labours'));
    }


    public function estimationSummary() {
      
        $role = Role::find(Auth::user()->role_id);

        $sales = DB::table('estimations')
                 ->select([DB::raw("SUM(((estimations.total_sales + labour_estimations.total_amount) * (0.18)) + estimations.total_sales + labour_estimations.total_amount) as total_amount"), 
                  'job_cards.amount', 'clients.place', DB::raw("SUM(estimations.total_sales * estimations.vat_amount) as vat_amount"), 'estimations.job_card_no', 'estimations.profoma_invoice',
                    'users.fname', 'users.lname', 'estimations.reference', 'estimations.created_date', 'clients.client_name', 'estimations.customer_name', 'estimations.vehicle_reg'])
                 ->join('products', 'products.pid', '=', 'estimations.product_id')
                 ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                 ->leftjoin('job_cards', 'job_cards.job_card_no', '=', 'estimations.job_card_no')
                 ->leftjoin('labour_estimations', 'labour_estimations.estimate_reference', '=', 'estimations.reference')
                 ->leftjoin('users', 'users.id', '=', 'estimations.user_id')
                 ->groupBy('estimations.job_card_no', 'users.fname', 'users.lname', 'clients.place', 'job_cards.amount', 'estimations.reference', 'estimations.customer_name',
                  'estimations.vehicle_reg', 'clients.client_name', 'estimations.created_date', 'estimations.profoma_invoice')
                 ->where('estimations.branch_id', Auth::user()->branch_id)
                 ->where('estimations.profoma_invoice', '!=', NULL)
                 ->orderBy('estimations.profoma_invoice', 'desc')
                 ->get();

             return view('estimations.estimations-history', compact('sales', 'role'));
    }

    
    public function getAllInvoices() {

        $role = Role::find(Auth::user()->role_id);
        $clients = DB::table('clients')
                  ->select('*')
                  ->where('clients.branch_id', Auth::user()->branch_id)
                  ->get();
       $labours = DB::table('labours')
                  ->select('*')
                  ->get();
      $products = DB::table('price_lists')
            ->select('price_lists.id', 'items.item_name', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
            ->join('items', 'items.id', 'products.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
            ->where('price_lists.branch_id', Auth::user()->branch_id)
            ->where('price_lists.client_id', '0')
            ->get();

       $sales = DB::table('invoices')
                 ->select(['clients.place', 'invoices.bill_amount', 'invoices.id',
                  'users.fname', 'users.lname', 'invoices.invoice_number', 'invoices.estimate_ref',
                   'invoices.reference', 'invoices.created_date', 'clients.client_name', 'invoices.vehicle_reg', 'estimations.customer_name'])
                 ->join('estimations', 'estimations.reference', '=', 'invoices.estimate_ref')
                 ->leftjoin('clients', 'clients.id', '=', 'invoices.client_id')
                 ->leftjoin('users', 'users.id', '=', 'invoices.user_id')
                 ->groupBy('clients.place', 'invoices.bill_amount', 'users.fname', 'invoices.id',
                 'users.lname', 'invoices.invoice_number', 'invoices.estimate_ref', 'estimations.customer_name',
                  'invoices.reference', 'invoices.created_date', 'invoices.vehicle_reg', 'clients.client_name')
                 ->where('invoices.branch_id', Auth::user()->branch_id)
                 ->where('invoices.invoice_number', '!=', NULL)
                 ->orderBy('invoices.invoice_number', 'desc')
                 ->get();
                 
             return view('estimations.all-invoices', compact('sales', 'role', 'clients', 'products', 'labours'));
    }

    public function getJobCards() {

        $role = Role::find(Auth::user()->role_id);

        $sales = DB::table('job_cards')
        ->select([
            'clients.place',
            'job_cards.created_date',
            'users.fname',
            'invoices.invoice_number',
            'users.lname',
            'job_cards.job_card_no',
            'estimations.reference',
            'estimations.created_date',
            'job_cards.status',
            'clients.client_name',
            'estimations.vehicle_reg',
            'estimations.customer_name'
        ])
        ->leftjoin('estimations', 'estimations.job_card_no', '=', 'job_cards.job_card_no')
        ->leftJoin('invoices', 'invoices.estimate_ref', '=', 'estimations.reference') // Corrected leftJoin method parameter order
        ->leftJoin('users', 'users.id', '=', 'job_cards.user_id')
        ->leftJoin('clients', 'clients.id', '=', 'estimations.client_name')
        ->where('job_cards.branch_id', Auth::user()->branch_id)
        ->groupBy('job_cards.job_card_no', 'job_cards.created_date', 'clients.place', 
        'estimations.customer_name', 'users.fname', 'job_cards.job_card_ID',
        'users.lname', 'invoices.invoice_number', 'job_cards.status', 'estimations.reference', 'estimations.vehicle_reg', 'clients.client_name', 'estimations.created_date') // Removed duplicate grouping
        ->orderBy('job_cards.job_card_ID', 'desc')
        ->get();
    

             return view('estimations.job-cards', compact('sales', 'role'));
    }


    public function saveEstimations(Request $request) {
       
        // $request->validate([
        //     'labour_qty' => 'required',
        // ]);
        $refence_no = date('YdmHis');
        $profoma_invoice = date('His');
        $today = $request['created_date'];
        $date = date('Y-m-d', strtotime('+1 month', strtotime($today)));

            $accounts = Estimation::where('client_name', $request->client_name)->where('branch_id', Auth::user()->branch_id)->orderBy('account_no', 'desc')->first();
            $profoma_id  = DB::table('estimations')->max('profoma_invoice');
           
            if(!$profoma_id) {
                $profoma = 1;
            } else {
                $profoma = $profoma_id + 1;
            }
            if (!$accounts) {
                $account_no = 1;
            } else {
            $account_no = $accounts['account_no'] + 1 ? $accounts['account_no'] + 1: 1;
            }

             $client_ID = Client::where('id', $request->client_name)->where('branch_id', Auth::user()->branch_id)->first();
        
   if ($client_ID != NULL) {
    $client = substr($client_ID['client_name'], 0, 3);
} else {
    $client = substr('customer_name', 0, 3);
}

     for($i = 0; $i < count($request->id); $i++){

           $sale = Product::where('pid', $request->id[$i])->where('branch_id', Auth::user()->branch_id)->first();

            $product_id = $request->id[$i]; 
            $qty = $request->qty[$i]; 

            $estimates = Estimation::create([
            'product_id' => $product_id,
            'branch_id' => $request->branch_id, 
            'user_id' => Auth::user()->id,
            'account_no' => $account_no,
            'account_prefix' => strtoupper($client),
            'qty' => $qty, 
            // 'qty_balance' => $sale['quantity'] - $qty,
            'discount' => $request->discount[$i],
            'total_discount' => (($request->discount[$i]/100) * ($request['selling_price'][$i]*$qty)),
            'selling_price' => $request["selling_price"][$i],
            'valid_estimate_date' => $date,
            'profoma_invoice' => $profoma,
            'vat_amount' => $request->vat_amount,
            'temesa_fee' => $request->temesa_fee,
            'make'=> $request['make'],
            'model'=> $request['model'],
            'chassis'=> $request['chassis'],
            'milleage'=> $request['milleage'],
            'registration_year'=> $request['registration_year'],
            'vehicle_reg'=> $request['vehicle_reg'],
            'client_name'=> $request['client_name'],
            'customer_name' => $request->customer_name,
            'total_sales' => $request['total_charge'][$i],
            'reference' => $refence_no,
            'created_date' => $request['created_date']
            ]);
         }
        

         for($j = 0; $j < count($request->labour_name); $j++){

         $labours = LabourEstimation::create([
            'labour_name' => $request->labour_name[$j],
            'branch_id' => $request->branch_id, 
            'estimate_reference' => $refence_no,
            'charge' => $request->unit_charge[$j],
            'qty' => $request->labour_qty[$j],
            'rate' => $request->rate[$j],
            'total_amount' => $request->total_amount[$j],
            'created_date' => $request['created_date']
            ]);
        }
        return redirect()->back()->with('message', 'You have Created Estimation for '.$request->vehicle_reg);
    }      

    public function addMoreEstimations(Request $request) {
       
        // $request->validate([
        //     'qty' => 'required',
        // ]);

            $accounts = Estimation::where('client_name', $request->client_id)
              ->where('reference', $request->reference)
              ->where('branch_id', Auth::user()->branch_id)->orderBy('account_no', 'desc')
              ->first();

             $profoma_id  = DB::table('estimations')->max('profoma_invoice');
            $account_no = $accounts['account_no'];

             $client_ID = Client::where('id', $request->client_name)->where('branch_id', Auth::user()->branch_id)->first();
            
          // substr($client_ID['client_name'], 0,3);

     for($i = 0; $i < count($request->id); $i++){

            $estimates = Estimation::create([
            'product_id' => $request->id[$i],
            'user_id' => $accounts['user_id'],
            'branch_id' => Auth::user()->branch_id, 
            'account_no' => $accounts['account_no'],
            'account_prefix' => $accounts['account_prefix'],
            'qty' => $request->qty[$i], 
            'discount' => $request->discount[$i],
            'total_discount' => (($request->discount[$i]/100) * ($request['selling_price'][$i]*$request->qty[$i])),
            'selling_price' => $request["selling_price"][$i],
            'valid_estimate_date' => $accounts['valid_estimate_date'],
            'profoma_invoice' => $accounts['profoma_invoice'],
            'job_card_no' => $accounts['job_card_no'],
            'vat_amount' => $accounts['vat_amount'],
            'make'=> $accounts['model'],
            'model'=> $accounts['model'],
            'chassis'=> $accounts['chassis'],
            'milleage'=> $accounts['milleage'],
            'registration_year'=> $accounts['registration_year'],
            'vehicle_reg'=> $accounts['vehicle_reg'],
            'client_name'=> $accounts['client_name'],
            'customer_name' => $accounts->customer_name, 
            'total_sales' => (($request['selling_price'][$i]*$request->qty[$i]) - (($request->discount[$i]/100) * ($request['selling_price'][$i]*$request->qty[$i]))),
            'reference' => $request->reference,
            'created_date' => $accounts['created_date']
            ]);
         }

        return redirect('/estimations/details/'.$request->reference)->with('message', 'You have Added New Part for '.$accounts['vehicle_reg']);
    }


    public function saveNewLabourEstimations(Request $request) {
       
        // $request->validate([
        //     'labour_qty' => 'required',
        // ]);
      
        
         for($j = 0; $j < count($request->labour_name); $j++){

         $labours = LabourEstimation::create([
            'labour_name' => $request->labour_name[$j],
            'branch_id' => Auth::user()->branch_id, 
            'estimate_reference' => $request->reference,
            'charge' => $request->unit_charge[$j],
            'qty' => $request->labour_qty[$j],
            'total_amount' => $request->total_amount[$j],
            'created_date' => date('Y-m-d')
            ]);
        }
        return redirect('estimations/details/'.$request->reference)->with('message', 'You have Added new Labour ');
    }


    public function getEstimationsDetails($id) {
        
        $client_value = Estimation::where('reference', $id)->first();

        $clients = DB::table('clients')
                ->select('*')
                ->where('clients.branch_id', Auth::user()->branch_id)
                ->get();

        $sales = DB::table('estimations')
                    ->select('estimations.id as id', 'estimations.vat_amount', 'estimations.temesa_fee',  'items.item_name', 'brands.title', 'estimations.customer_name', 'estimations.discount', 'estimations.qty', 'estimations.created_date', 'estimations.vehicle_reg', 'estimations.reference', 'clients.client_name as client', 'estimations.client_name','estimations.model', 'estimations.make', 'estimations.chassis', 'estimations.milleage', 'estimations.selling_price', 'estimations.total_sales')
                    ->join('products', 'products.pid', '=', 'estimations.product_id')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                    ->where('estimations.reference', '=', $id)
                    ->where('estimations.branch_id', Auth::user()->branch_id)
                    ->get();

       $total_sales = DB::table('estimations')
                     ->where('reference', $id)
                     ->where('estimations.branch_id', Auth::user()->branch_id)
                     ->sum('total_sales');

      $total_discounts = DB::table('estimations')
                     ->where('reference', $id)
                     ->where('estimations.branch_id', Auth::user()->branch_id)
                     ->sum('total_discount');

       $client_name = DB::table('estimations')
                    ->select('estimations.client_name', 'clients.client_name as client', 'clients.place')
                    ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                    ->where('reference', '=', $id)
                    ->where('estimations.branch_id', Auth::user()->branch_id)
                    ->distinct()
                    ->get();

      $vehicle = Estimation::select('vehicle_reg')
                 ->where('estimations.branch_id', Auth::user()->branch_id)
                ->where('reference', $id)->distinct()->get();

      $products = DB::table('price_lists')
                ->select('price_lists.id', 'items.item_name', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
                ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
                ->join('items', 'items.id', 'products.product_id')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
                ->groupBy('price_lists.id', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'products.selling_price', 'brands.title', 'products.quantity', 'products.model')
                ->where('price_lists.branch_id', Auth::user()->branch_id)
                ->where('price_lists.client_id',  0)
                ->get();
      
      $labours = DB::table('labour_estimations')
                      ->select('labour_estimations.estimate_reference', 'id', 'qty', 'charge', 'labour_estimations.labour_name', 'labour_estimations.total_amount')
                      ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                      ->where('labour_estimations.estimate_reference', '=', $id)
                      ->where('labour_estimations.labour_name', '!=', NULL)
                      ->get();

        $total_labours = DB::table('labour_estimations')
                        ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                        ->where('labour_estimations.estimate_reference', '=', $id)   
                        ->where('labour_estimations.labour_name', '!=', NULL)                  
                        ->sum('total_amount');

        $labour_settings = Labour::all();

        $vat_calculations =  (0.18) * ($total_sales);
        
        if($sales[0]->vat_amount == '0') {
            $vat_charges = 0.00;
        } else {
            $vat_charges =  (0.18) * ($total_sales + $total_labours);
        }

        $temesa_fee =  ($sales[0]->temesa_fee ?? 0) * ($total_sales + $total_labours + $vat_charges);

         $grand_total_amount = ($total_sales + $total_labours + $temesa_fee + $vat_charges);

         return  view('estimations.estimation-details', compact('sales', 'clients', 'labour_settings', 'temesa_fee', 'labours', 'total_discounts', 'total_labours', 'vat_charges', 'grand_total_amount', 'total_sales', 'id', 'products', 'client_name', 'vat_calculations', 'vehicle'));
       
    }
      public function getDeliveryNotePDF($id) {
    
        $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
        $user_role = DB::table('users')
                    ->select('roles.name')
                   ->leftjoin('roles', 'roles.id', 'users.role_id')
                   ->where('users.role_id', Auth::user()->role_id)
                   ->first();

          $sales = DB::table('estimations')
                      ->select('estimations.id as id', 'items.item_name','account_no', 'account_prefix', 'brands.title',
                        'estimations.vehicle_reg', 'estimations.vat_amount', 'job_cards.delivery_date', 'job_cards.job_card_ID',
                        'estimations.reference', 'estimations.client_name', 'products.purchase_unit',
                        'estimations.model as emodel', 'estimations.make as emake', 'estimations.chassis', 
                        'estimations.milleage', 'estimations.qty', 'job_cards.invoice_no', 
                        'estimations.valid_estimate_date', 'job_cards.created_date', 'clients.client_name as client_name',
                         'clients.place', 'estimations.reference', 'estimations.client_name as client', 
                       'products.model')
                      ->join('products', 'products.pid', '=', 'estimations.product_id')
                      ->join('items', 'items.id', 'products.product_id')
                      ->join('brands', 'brands.id', 'products.brand_id')
                      ->join('job_cards', 'job_cards.estimate_reference', 'estimations.reference')
                      ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                      ->where('estimations.branch_id', Auth::user()->branch_id)
                      ->where('estimations.reference', '=', $id)
                      ->get();
                      
        $labours = DB::table('labour_estimations')
                      ->select('labour_estimations.estimate_reference', 'charge', 'qty', 'labour_estimations.labour_name', 'labour_estimations.total_amount')
                      ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                      ->where('labour_estimations.estimate_reference', '=', $id)
                      ->where('labour_name', '!=', NULL)                   
                      ->get();

        $total_labours = DB::table('labour_estimations')
                        ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                        ->where('labour_estimations.estimate_reference', '=', $id)  
                        ->where('labour_name', '!=', NULL)                   
                        ->sum('total_amount');

         $total_sales = DB::table('estimations')
                       ->where('reference', $id)
                       ->where('estimations.branch_id', Auth::user()->branch_id)
                       ->sum('total_sales');
        
        $total_discounts = DB::table('estimations')
                       ->where('reference', $id)
                       ->where('estimations.branch_id', Auth::user()->branch_id)
                       ->sum('total_discount');
  
         $client_name = DB::table('estimations')
                      ->select('clients.client_name', 'clients.id', 'clients.place', 'clients.address')
                      ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                      ->where('reference', '=', $id)
                      ->where('estimations.branch_id', Auth::user()->branch_id)
                      ->distinct()
                      ->get();
  
        $vehicle = Estimation::select('vehicle_reg')
                    ->where('estimations.branch_id', Auth::user()->branch_id)
                    ->where('reference', $id)->distinct()
                    ->get();
  
        $products = DB::table('products')
                      ->select('*')
                      ->join('items', 'items.id', 'products.product_id')
                      ->join('brands', 'brands.id', 'products.brand_id')
                      ->where('products.branch_id', Auth::user()->branch_id)
                      ->get();
                              
       $vat_calculations =  (0.18) * ($total_sales);
  
       if($sales[0]->vat_amount == '0') {
        $vat_charges = 0.00;
        } else {
            $vat_charges =  (0.18) * ($total_sales + $total_labours);
        }

       $grand_total_amount = ($total_sales + $total_labours + $vat_charges);

       
       $pdf = App::make('dompdf.wrapper');
       
       $pdf->loadView('estimations.delivery_note_pdf', compact('sales', 'total_discounts', 'user_role', 'vat_charges', 'grand_total_amount', 'labours', 'total_labours', 'total_sales', 'id', 'products', 'client_name', 'vat_calculations', 'vehicle', 'settings'));
       return $pdf->stream();
      }
   public function editDeliveryNote($reference) {

        $edit = DB::table('job_cards')
                    ->select('job_cards.id', 'job_cards.job_card_no', 'job_cards.estimate_reference', 'job_cards.created_date')
                    ->where('job_cards.branch_id', Auth::user()->branch_id)
                    ->where('job_cards.estimate_reference', $reference)
                    ->get();

       return $edit;  

    }
     public function updateDeliveryNote(Request $request) {

        $update = DB::table('job_cards')
                    ->where('estimate_reference', $request->reference_no)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->update(["created_date" => $request->created_date
                            ]);
 
        return redirect()->back()->with('message', 'Delivery updated successful');
     } 
   
  public function editProfomaDetails($id) {

        $edit = DB::table('estimations')
                    ->select('estimations.id', 'estimations.reference', 'estimations.vehicle_reg', 'estimations.profoma_invoice', 'estimations.make', 'estimations.model', 'estimations.created_date', 'estimations.chassis', 'estimations.milleage')
                    ->where('estimations.branch_id', Auth::user()->branch_id)
                    ->where('estimations.reference', $id)
                    ->get();

       return $edit;  

    }

    public function updateProfomaDetails(Request $request)
{
    // Find invoice either by reference or proforma_invoice
    $invoice = DB::table('estimations')
        ->where(function($query) use ($request) {
            $query->where('reference', $request->reference)
                  ->orWhere('profoma_invoice', $request->proforma_invoice);
        })
        ->where('branch_id', Auth::user()->branch_id)
        ->first();

    if (!$invoice) {
        return redirect()->back()->with('error', 'Proforma not found.');
    }

    // Check which field changed
    $referenceChanged = $request->reference !== $invoice->reference;
    $proformaChanged = $request->proforma_invoice !== $invoice->profoma_invoice;

    if ($referenceChanged && $proformaChanged) {
        return redirect()->back()->with('error', 'You cannot update Reference and Proforma Invoice at the same time.');
    }

    if (!$referenceChanged && !$proformaChanged) {
        return redirect()->back()->with('error', 'You must update either Reference or Proforma Invoice.');
    }

    // Prepare update data
    $updateData = [
        "vehicle_reg"  => $request->vehicle_reg,
        "make"         => $request->make, 
        "model"        => $request->model, 
        "chassis"      => $request->chassis, 
        "milleage"     => $request->milleage, 
        "created_date" => $request->created_date
    ];

    if ($referenceChanged) {
        $updateData['reference'] = $request->reference;
    } elseif ($proformaChanged) {
        $updateData['profoma_invoice'] = $request->proforma_invoice;
    }

    // Perform update
    DB::table('estimations')
        ->where('id', $invoice->id)
        ->update($updateData);

    return redirect()->back()->with('message', 'Proforma updated successfully!');
}

    public function updateP(Request $request)
{
    $invoice = DB::table('estimations')
        ->where('reference', $request->reference)
        ->where('branch_id', Auth::user()->branch_id)
        ->first();

    if (!$invoice) {
        return redirect()->back()->with('error', 'Proforma not found.');
    }

    // Check if fields changed
    $referenceChanged = $request->reference !== $invoice->reference;
    $proformaChanged = $request->proforma_invoice !== $invoice->profoma_invoice;

    if ($referenceChanged && $proformaChanged) {
        return redirect()->back()->with('error', 'You cannot update Reference and Proforma Invoice at the same time.');
    }

    if (!$referenceChanged && !$proformaChanged) {
        return redirect()->back()->with('error', 'You must update either Reference or Proforma Invoice.');
    }

    // Perform update
    DB::table('estimations')
        ->where('id', $invoice->id)
        ->update([
            "vehicle_reg"      => $request->vehicle_reg,
            "make"             => $request->make, 
            "model"            => $request->model, 
            "chassis"          => $request->chassis, 
            "milleage"         => $request->milleage, 
            "profoma_invoice"  => $request->proforma_invoice,
            "created_date"     => $request->created_date,
            "reference"        => $request->reference
        ]);

    return redirect()->back()->with('message', 'Proforma updated successfully!');
}


    //  public function updateProfomaDetails(Request $request) {

    //     $update = DB::table('estimations')
    //                 ->where('reference', $request->reference)
    //                 ->where('branch_id', Auth::user()->branch_id)
    //                 ->update(["vehicle_reg" => $request->vehicle_reg,
    //                          "make" => $request->make, 
    //                         "model"=> $request->model, 
    //                         "chassis"=> $request->chassis, 
    //                         "milleage"=> $request->milleage, 
    //                         "profoma_invoice" => $request->proforma_invoice,
    //                         "created_date" => $request->created_date,
    //                         "reference" => $request->reference
    //                     ]);
 
    //     return redirect()->back()->with('message', 'Proforma updated successful');
    //  } 
    public function getEstimationDetailsPDF($id) {
    
        $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
   
          $sales = DB::table('estimations')
                      ->select('estimations.id as id', 'items.item_name', 'products.purchase_unit', 'estimations.vat_amount', 'account_no', 'account_prefix', 'brands.title',
                       'estimations.discount', 'estimations.total_discount', 'estimations.vehicle_reg', 'estimations.reference', 'clients.client_name as client',
                        'estimations.client_name','estimations.model as emodel', 'estimations.make as emake', 'estimations.chassis', 'estimations.milleage',
                         'estimations.qty', 'estimations.profoma_invoice', 'estimations.valid_estimate_date', 'estimations.created_date',
                         'clients.client_name as client_name', 'clients.place', 'estimations.created_date', 'estimations.reference', 'estimations.temesa_fee',
                           'estimations.client_name as client',  'products.model', 'estimations.selling_price', 'estimations.customer_name', 'estimations.total_sales')
                      ->join('products', 'products.pid', '=', 'estimations.product_id')
                      ->join('items', 'items.id', 'products.product_id')
                      ->join('brands', 'brands.id', 'products.brand_id')
                      ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                      ->where('estimations.branch_id', Auth::user()->branch_id)
                      ->where('estimations.reference', '=', $id)
                      ->get();

                      
        $labours = DB::table('labour_estimations')
                      ->select('labour_estimations.estimate_reference', 'charge', 'qty','rate', 'labour_estimations.labour_name', 'labour_estimations.total_amount')
                      ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                      ->where('labour_estimations.estimate_reference', '=', $id)
                      ->where('labour_name', '!=', NULL)                   
                      ->get();

        $total_labours = DB::table('labour_estimations')
                        ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                        ->where('labour_estimations.estimate_reference', '=', $id)  
                        ->where('labour_name', '!=', NULL)                   
                        ->sum('total_amount');

         $total_sales = DB::table('estimations')
                       ->where('reference', $id)
                       ->where('estimations.branch_id', Auth::user()->branch_id)
                       ->sum('total_sales');
        
        $total_discounts = DB::table('estimations')
                       ->where('reference', $id)
                       ->where('estimations.branch_id', Auth::user()->branch_id)
                       ->sum('total_discount');
  
         $client_name = DB::table('estimations')
                      ->select('clients.client_name', 'clients.id', 'clients.place', 'clients.address', 'clients.address', 'clients.vrn', 'clients.tin', 'clients.email')
                      ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                      ->where('reference', '=', $id)
                      ->where('estimations.branch_id', Auth::user()->branch_id)
                      ->distinct()
                      ->get();
  
        $vehicle = Estimation::select('vehicle_reg')
                    ->where('estimations.branch_id', Auth::user()->branch_id)
                    ->where('reference', $id)->distinct()
                    ->get();
  
        $products = DB::table('products')
                      ->select('*')
                      ->join('items', 'items.id', 'products.product_id')
                      ->join('brands', 'brands.id', 'products.brand_id')
                      ->where('products.branch_id', Auth::user()->branch_id)
                      ->get();
                              
       $vat_calculations =  (0.18) * ($total_sales);
  
       if($sales[0]->vat_amount == '0') {
        $vat_charges = 0.00;
      } else {
        $vat_charges =  (0.18) * ($total_sales + $total_labours);
      }

         $temesa_fee =  ($sales[0]->temesa_fee ?? 0) * ($total_sales + $total_labours + $vat_charges);

         $grand_total_amount = ($total_sales + $total_labours + $temesa_fee + $vat_charges);

       
       $pdf = App::make('dompdf.wrapper');
       
       $pdf->loadView('estimations.estimation-details-pdf', compact('sales', 'total_discounts', 'temesa_fee', 'vat_charges', 'grand_total_amount', 'labours', 'total_labours', 'total_sales', 'id', 'products', 'client_name', 'vat_calculations', 'vehicle', 'settings'));
       return $pdf->stream();
         
      }


      public function createJobCard($id) {
   
$job_card  = DB::table('job_cards')->max('job_card_ID');           
            if(!$job_card) {
            $job_card_no = 1;
            } else {
            $job_card_no = $job_card + 1;
            }


        $invoice = date('his');
        $sales = DB::table('estimations')
                      ->select('estimations.id as id', 'estimations.product_id', 'estimations.qty', 'items.item_name', 'estimations.vat_amount', 'brands.title', 'estimations.vehicle_reg', 'estimations.reference', 'clients.client_name as client', 'estimations.client_name as client_id', 'estimations.client_name','estimations.model', 'estimations.make', 'estimations.chassis', 'estimations.milleage', 'estimations.qty', 'estimations.profoma_invoice', 'estimations.valid_estimate_date', 'estimations.created_date', 'clients.client_name as client_name', 'clients.place', 'estimations.created_date', 'estimations.reference', 'estimations.client_name as client',  'products.model', 'estimations.selling_price', 'estimations.total_sales')
                      ->join('products', 'products.pid', '=', 'estimations.product_id')
                      ->join('items', 'items.id', 'products.product_id')
                      ->join('brands', 'brands.id', 'products.brand_id')
                      ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                      ->where('estimations.branch_id', Auth::user()->branch_id)
                      ->where('estimations.reference', $id)
                      ->get();

       $total_labours = DB::table('labour_estimations')
                      ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                      ->where('labour_estimations.estimate_reference', '=', $id)                     
                      ->sum('total_amount');

       $total_sales = DB::table('estimations')
                     ->where('reference', $id)
                     ->where('estimations.branch_id', Auth::user()->branch_id)
                     ->sum('total_sales');
      
        $total_bill_amount = DB::table('estimations')
                       ->select([DB::raw("SUM((estimations.total_sales * estimations.vat_amount) + estimations.total_sales) as total_billed_amount")])
                       ->where('reference', $id)
                       ->where('estimations.branch_id', Auth::user()->branch_id)
                       ->get();

         if($sales[0]->vat_amount == '0') {
            $vat_charges = 0.00;
          } else {
            $vat_charges =  (0.18) * ($total_sales + $total_labours);
          }

         $grand_total_amount = ($total_sales + $total_labours + $vat_charges);

        return view('estimations.create-job-cards', compact('sales', 'grand_total_amount', 'job_card_no',  'invoice', 'total_bill_amount'));

    }


    public function saveJobCard(Request $request) {
    
        $refence_no = date('Yis');
        $profoma_invoice = date('His');
        $today = date('Y-m-d');
        $date = date('Y-m-d', strtotime('+1 month', strtotime($today)));


     for($i = 0; $i < count($request->id); $i++){
            $sale = Product::where('pid', $request->product_id[$i])
            ->where('products.branch_id', Auth::user()->branch_id)
            ->select('quantity')->first();
        
    $create_bill = DB::table('estimations')
        ->where('id', $request->id[$i])
        ->where('estimations.branch_id', Auth::user()->branch_id)
         ->where('reference', $request->reference)
         ->update(["job_card_no" => $request->job_card_no,
        ]);

        $stocks = DB::table('products')
                ->where('pid', $request->product_id[$i])
                ->where('products.branch_id', Auth::user()->branch_id)
                ->update(["quantity" => $sale['quantity'] - $request->qty[$i]]);
        }

            $job_cards = JobCard::create([
                'branch_id' => Auth::user()->branch_id,
                'client_id' => $request->client_id,
                'user_id' => Auth::user()->id,
                'vehicle_reg' => $request->vehicle_reg,
                'estimate_reference' => $request->reference, 
                'job_card_reference' => $refence_no,
                'job_card_no' => $request->job_card_no,
                'job_card_ID' => $request->job_card_no,
                'status' => '0',
                'amount' => $request->bill_amount,
                'created_date' => $request['created_date']
                ]);

            return redirect('estimations-history')->with('message', 'You Created Job Card Reference '.$refence_no);
         } 

         public function getJobCardPDF($id) {
    
            $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
       
           $sales = DB::table('job_cards')
                          ->select('estimations.id as id', 'account_no', 'account_prefix', 'items.item_name', 
                             'brands.title', 'products.part_number', 'estimations.vehicle_reg', 'job_cards.job_card_reference',
                              'estimations.reference', 'clients.client_name as client', 'estimations.client_name', 'estimations.customer_name',
                              'estimations.model as emodel', 'estimations.make as emake', 'estimations.chassis', 
                              'estimations.milleage', 'estimations.qty', 'estimations.profoma_invoice', 
                              'estimations.valid_estimate_date', 'estimations.vat_amount',
                              'clients.client_name as client_name', 'clients.place', 'estimations.created_date',
                               'estimations.reference', 'estimations.client_name as client',  'products.model',
                                'estimations.selling_price', 'estimations.total_sales')
                          ->join('estimations', 'estimations.reference', '=', 'job_cards.estimate_reference')
                          ->join('products', 'products.pid', '=', 'estimations.product_id')
                          ->join('items', 'items.id', 'products.product_id')
                          ->join('brands', 'brands.id', 'products.brand_id')
                          ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                          ->where('estimations.branch_id', Auth::user()->branch_id)
                          ->where('estimations.reference', '=', $id)
                          ->get();

      
             $total_sales = DB::table('estimations')
                           ->where('reference', $id)
                           ->where('estimations.branch_id', Auth::user()->branch_id)
                           ->sum('total_sales');

            $labours = DB::table('labour_estimations')
                            ->select('labour_estimations.estimate_reference', 'labour_estimations.labour_name')
                            ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                            ->where('labour_estimations.estimate_reference', '=', $id)
                            ->where('labour_estimations.labour_name', '!=', '')
                            ->get();

             $client_name = DB::table('estimations')
                          ->select('clients.client_name', 'clients.id', 'clients.place', 'clients.address')
                          ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                          ->where('reference', '=', $id)
                          ->where('estimations.branch_id', Auth::user()->branch_id)
                          ->distinct()
                          ->get();
      
            $vehicle = Estimation::select('vehicle_reg')
                        ->where('estimations.branch_id', Auth::user()->branch_id)
                        ->where('reference', $id)->distinct()
                        ->get();
      
            $products = DB::table('products')
                          ->select('*')
                          ->join('items', 'items.id', 'products.product_id')
                          ->join('brands', 'brands.id', 'products.brand_id')
                          ->where('products.branch_id', Auth::user()->branch_id)
                          ->get();
                                  
           $vat_calculations =  (0.18) * ($total_sales);
      
           $pdf = App::make('dompdf.wrapper');
           $pdf->loadView('estimations.job-cards-pdf', compact('sales', 'labours', 'total_sales', 'id', 'products', 'client_name', 'vat_calculations', 'vehicle', 'settings'));
           return $pdf->stream();

          }


    public function getJobCardStatus($reference) {

       $job_cards = DB::table('job_cards')
                      ->select('*')
                      ->where('job_cards.branch_id', Auth::user()->branch_id)
                      ->where('job_cards.job_card_no', '=', $reference)                     
                      ->get();

        return view('estimations.change-job-cards-status', compact('job_cards'));
    } 

    public function updateJobCardStatus(Request $request) {

        $job_card  = DB::table('job_cards')->max('job_card_ID');
        if(!$job_card) {
            $job_card_no = 1;
            } else {
                $job_card_no = $job_card + 1;
            }
            
        $job_cards = DB::table('job_cards')
                    ->where('job_card_no', $request->job_card_no)
                    ->where('job_cards.branch_id', Auth::user()->branch_id)
                    ->update(
                        ["status" => $request->status,
                        "job_card_ID" => $job_card_no,
                        "delivery_date" => date('Y-m-d'),
        ]);
 
 
        return redirect()->back()->with('message', 'Status updated successful');
     } 

     public function updateEstimationDetails(Request $request) {

        $update = DB::table('estimations')
                    ->where('id', $request->id)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->update(["discount" => $request->discount, "qty" => $request->quantity, 
                            "selling_price"=> $request->selling_price, 
                            "total_sales" => (($request->selling_price *  $request->quantity)  - ($request->discount/100) * ($request->selling_price * $request->quantity)),
                            "total_discount" => ($request->discount/100) * ($request->selling_price * $request->quantity)]);
 
        return redirect()->back()->with('message', 'Item updated successful');
     } 
 

     public function deleteEstimationDetails($id) {

        $delete = Estimation::where('id', $id)->first();
        $delete->delete();

        return redirect()->back()->with('error', 'Item deleted successful');
     } 


     public function deleteEstimations($id) {
        
        $delete = DB::table('estimations')->where('reference', $id)->delete();

        return redirect()->back()->with('error', 'Estimation deleted successful');
     } 


   public function getInvoice($reference) {

        $invoices  = DB::table('invoices')->where('branch_id', Auth::user()->branch_id)->max('invoice_number');           

        if(!$invoices) {
            $invoice_no = 1;
            } else {
                $invoice_no = $invoices + 1;
            }
        $reference_id = date('YdHis');
        $invoices = DB::table('job_cards')
                       ->select('*')
                       ->leftjoin('estimations', 'estimations.reference', 'job_cards.estimate_reference')
                       ->where('job_cards.branch_id', Auth::user()->branch_id)
                       ->where('job_cards.job_card_no', '=', $reference)                     
                       ->get();
 
         return view('estimations.create-invoice', compact('invoices', 'invoice_no', 'reference_id'));
     } 

     public function saveInvoice(Request $request) {

        
        $date = date('Y-m-d');

        // check if invoice number already exists
        if (Invoice::where('invoice_number', $request->invoice_no)->exists()) {
            return redirect()->back()->with('error', 'Invoice number exists.');
        }
    
        // auto-generate invoice number if not provided
        $lastInvoice = DB::table('invoices')->max('invoice_number');
        if (!$lastInvoice) {
            $invoice_no = 1;
        } else {
            $invoice_no = $lastInvoice + 1;
        }
    
        $invoices = Invoice::create([
            'branch_id' => Auth::user()->branch_id,
            'user_id' => Auth::user()->id,
            'reference' => $request->reference, 
            'account' => $request->account,
            'make' => $request->make,
            'estimate_ref' => $request->estimate_ref, 
            'client_id' => $request->client_id,
            'vehicle_reg' => $request->vehicle_reg,
            'job_card_no' => $request->job_card_no,
            'invoice_number' => $invoice_no,
            'bill_amount' => $request->bill_amount,
            'account_number' => $request->account_no,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'swift_code' => $request->swift_code,
            'payment_status' => 0,
            'created_date' => $date
        ]);
    
        DB::table('job_cards')
            ->where('job_card_no', $request->job_card_no)
            ->where('branch_id', Auth::user()->branch_id)
            ->update([
                'invoice_no' => $invoice_no,
                'amount' => $request->bill_amount
            ]);
    
        return redirect('job-cards')->with('message', 'You have created Invoice '.$invoice_no);
    }


         public function saveNewInvoice(Request $request) {
       
          $total_bill = 0; // Initialize total_bill as a number
          $bill = 0; 
          $reference_no = date('Yis');
          $accounts = Estimation::where('client_name', $request->client_name)
              ->where('branch_id', Auth::user()->branch_id)
              ->orderBy('account_no', 'desc')
              ->first();
          
          if (!$accounts) {
              $account_no = 1;
          } else {
              $account_no = $accounts->account_no + 1; // Access object property directly,
          }
          
          $client_ID = Client::where('id', $request->client_name)
              ->where('branch_id', Auth::user()->branch_id)
              ->first();
          $client_prefix = substr($client_ID->client_name, 0, 3); // Assign the result to a variable
          
          for ($i = 0; $i < count($request->id); $i++) {
              $sale = Product::where('pid', $request->id[$i])
                  ->where('branch_id', Auth::user()->branch_id)
                  ->first();
          
              $product_id = $request->id[$i];
              $qty = $request->qty[$i];
          
              $estimates = Estimation::create([
                  'product_id' => $product_id,
                  'branch_id' => $request->branch_id,
                  'user_id' => Auth::user()->id,
                  'account_no' => $account_no,
                  'account_prefix' => strtoupper($client_prefix), // Use the variable with the prefix
                  'qty' => $qty,
                  'vat_amount' => $request->vat_amount,
                  'discount' => $request->discount[$i],
                  'total_discount' => (($request->discount[$i] / 100) * ($request->selling_price[$i] * $qty)),
                  'selling_price' => $request->selling_price[$i],
                  'valid_estimate_date' => NULL,
                  'profoma_invoice' => NULL,
                  'make' => $request->make,
                  'model' => $request->model,
                  'chassis' => $request->chassis,
                  'milleage' => $request->milleage,
                  'registration_year' => $request->registration_year,
                  'vehicle_reg' => $request->vehicle_reg,
                  'client_name' => $request->client_name,
                  'total_sales' => $request->total_charge[$i],
                  'reference' => $reference_no,
                  'created_date' => $request->created_date
              ]);
          
              // Calculate total bill for each product and accumulate
              $total_bill += $request->selling_price[$i] * $request->qty[$i];
          }
          
          $labour_charges = 0; // Initialize labour_charges as a number

          for ($j = 0; $j < count($request->labour_name); $j++) {
              $labours = LabourEstimation::create([
                  'labour_name' => $request->labour_name[$j],
                  'branch_id' => $request->branch_id,
                  'estimate_reference' => $reference_no, 
                  'charge' => $request->unit_charge[$j],
                  'qty' => $request->labour_qty[$j],
                  'total_amount' => $request->total_amount[$j],
                  'created_date' => $request->created_date
              ]);
          
              // Calculate total labour charges by accumulating total_amount
              $labour_charges += $request->total_amount[$j];
          }
          

          $date = date('Y-m-d');

          $invoices = Invoice::where('branch_id', Auth::user()->branch_id)->orderBy('id', 'desc')->first();
          if (!$invoices) {
              $invoice_no = 1;
          } else {
              $invoice_no = $invoices->invoice_number + 1; // Access object property directly,
          }
          
          if ($request->vat_amount == '0') {
              $vat_charges = 0.00;
          } else {
              $vat_charges = (0.18) * ($total_bill + $labour_charges);
          }
          
          $bill = ($total_bill + $labour_charges) + $vat_charges;
          
          $invoices = Invoice::create([
              'branch_id' => Auth::user()->branch_id,
              'user_id' => Auth::user()->id,
              'reference' => date('YmHis'),
              'account' => strtoupper(substr($client_ID->client_name, 0, 3)) . ' ' . $account_no, // Access object property directly
              'make' => $request->make,
              'estimate_ref' => $reference_no, 
              'client_id' => $request->client_name,
              'vehicle_reg' => $request->vehicle_reg,
              'job_card_no' => NULL,
              'invoice_number' => $invoice_no,
              'bill_amount' => $bill,
              'account_number' => NULL,
              'account_name' => NULL,
              'bank_name' => NULL,
              'branch_name' => NULL,
              'swift_code' => NULL,
              'payment_status' => 0,
              'created_date' => $date
          ]);
          
          return redirect('invoices/all-invoices')->with('message', 'You have Created New Invoices for '.$request->vehicle_reg);
      }

         public function editInvoice($id) {

            $invoice = DB::table('invoices')
                    ->select('invoices.client_id', 'clients.client_name', 'invoices.reference', 'invoices.account_name', 'invoices.account_number'
                              ,'invoices.branch_name', 'invoices.swift_code', 'invoices.invoice_number', 'invoices.bank_name', 'invoices.id')
                    ->join('clients', 'clients.id', 'invoices.client_id')
                    ->where('invoices.id', $id)
                    ->get();
    
           return $invoice;  
        
        }

        public function updateInvoice(Request $request)
        {
            
            $invoice = Invoice::findOrFail($request->id);
        
            // ✅ Check if invoice_number already exists (excluding current)
            if (
                Invoice::where('invoice_number', $request->invoice_number)
                    ->where('id', '!=', $invoice->id)
                    ->exists()
            ) {
                return redirect()->back()
                    ->with('error', 'Invoice number already exists.');
                    // ->withInput();
            }
        
            // ✅ Check if reference already exists (excluding current)
            if (
                Invoice::where('reference', $request->reference)
                    ->where('id', '!=', $invoice->id)
                    ->exists()
            ) {
                return redirect()->back()
                    ->with('error', 'Reference number already exists.');
                    // ->withInput();
            }
        
            // If passed, update record
            $invoice->update([
                'client_id' => $request->client_name,
                'invoice_number' => $request->invoice_number,
                'estimate_reference' => $request->reference,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'swift_code' => $request->swift_code,
            ]);
        
            return redirect()->back()->with('message', 'Invoice updated successfully!');
        }
        

     public function updateInvoice11(Request $request) {

      $invoices = DB::table('invoices')
            ->where('reference', $request->reference_no)
            ->where('invoice_number', $request->invoice_number)
            ->where('branch_id', Auth::user()->branch_id)
            ->update(["account_number" => $request->account_number, "account_name" => $request->account_name,
            "bank_name" => $request->bank_name, "branch_name" => $request->branch_name,
            "swift_code" => $request->swift_code, "client_id" => $request->client_name
            ]);

          return redirect()->back()->with('message', 'Invoice Details updated successful');

        }

      public function getInvoicePDF($id) {
    
            $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
       
              $sales = DB::table('estimations')
                          ->select('estimations.id as id', 'items.item_name', 'estimations.vat_amount', 
                              'estimations.account_no', 'estimations.account_prefix', 'brands.title', 
                              'invoices.invoice_number', 'products.purchase_unit',
                               'estimations.discount', 'invoices.bank_name', 'invoices.account_number',
                              'invoices.account_name', 'invoices.branch_name', 'invoices.swift_code', 
                              'estimations.vehicle_reg', 'estimations.reference', 'estimations.temesa_fee',
                              'estimations.model as emodel', 'estimations.make as emake',
                                'estimations.chassis', 'estimations.milleage', 'estimations.qty',
                                 'estimations.profoma_invoice', 'estimations.valid_estimate_date', 
                                  'clients.client_name as client_name', 'estimations.customer_name',
                                  'clients.place', 'invoices.created_date', 'estimations.reference', 
                                  'estimations.client_name as client',  'products.model', 'estimations.selling_price',
                                   'estimations.total_sales', 'products.purchase_unit')
                          ->join('products', 'products.pid', '=', 'estimations.product_id')
                          ->join('items', 'items.id', 'products.product_id')
                          ->join('invoices', 'invoices.estimate_ref', 'estimations.reference')
                          ->join('brands', 'brands.id', 'products.brand_id')
                          ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                          ->where('invoices.branch_id', Auth::user()->branch_id)
                          ->where('invoices.estimate_ref', '=', $id)
                          ->get();
                          
            $labours = DB::table('labour_estimations')
                          ->select('labour_estimations.estimate_reference',  'labour_estimations.labour_name', 'labour_estimations.total_amount')
                          ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                          ->where('labour_estimations.estimate_reference', '=', $id)
                          ->where('labour_name', '!=', NULL)                   
                          ->get();
    
            $total_labours = DB::table('labour_estimations')
                            ->where('labour_estimations.branch_id', Auth::user()->branch_id)
                            ->where('labour_estimations.estimate_reference', '=', $id)  
                            ->where('labour_name', '!=', NULL)                   
                            ->sum('total_amount');
    
             $total_sales = DB::table('estimations')
                           ->where('reference', $id)
                           ->where('estimations.branch_id', Auth::user()->branch_id)
                           ->sum('total_sales');
            
            $total_discounts = DB::table('estimations')
                           ->where('reference', $id)
                           ->where('estimations.branch_id', Auth::user()->branch_id)
                           ->sum('total_discount');
      
             $client_name = DB::table('invoices')
                          ->select('clients.client_name', 'clients.id', 'clients.place', 'clients.address', 'clients.email', 'clients.tin', 'clients.vrn')
                          ->leftjoin('clients', 'clients.id', '=', 'invoices.client_id')
                          ->where('invoices.estimate_ref', '=', $id)
                          ->where('invoices.branch_id', Auth::user()->branch_id)
                          ->distinct()
                          ->get();

            $vehicle = Estimation::select('vehicle_reg')
                        ->where('estimations.branch_id', Auth::user()->branch_id)
                        ->where('reference', $id)->distinct()
                        ->get();
      
            $products = DB::table('products')
                          ->select('*')
                          ->join('items', 'items.id', 'products.product_id')
                          ->join('brands', 'brands.id', 'products.brand_id')
                          ->where('products.branch_id', Auth::user()->branch_id)
                          ->get();
                                  
           $vat_calculations =  (0.18) * ($total_sales);
      
           if($sales[0]->vat_amount == '0') {
            $vat_charges = 0.00;
          } else {
            $vat_charges =  (0.18) * ($total_sales + $total_labours);
          }
    
           $temesa_fee =  ($sales[0]->temesa_fee ?? 0) * ($total_sales + $total_labours + $vat_charges);

         $grand_total_amount = ($total_sales + $total_labours + $temesa_fee + $vat_charges);
    
           $pdf = App::make('dompdf.wrapper');
           $pdf->loadView('estimations.invoice-pdf', compact('sales', 'total_discounts', 'vat_charges', 'grand_total_amount', 'temesa_fee',  'labours', 'total_labours', 'total_sales', 'id', 'products', 'client_name', 'vat_calculations', 'vehicle', 'settings'));
           return $pdf->stream();
             
          }


   public function deleteInvoice($id){

    $products = Invoice::where('reference', $id);

    $products->delete();

  return redirect()->back()->with('error', 'Invoice deleted successful');
  }

  public function deleteJobCard($id){
    
    $products = DB::table('job_cards')->where('estimate_reference', $id)->delete();

    $create_bill = DB::table('estimations')
    ->where('reference', $id)
    ->where('branch_id', Auth::user()->branch_id)
     ->update(["job_card_no" => NULL,
    ]);

  return redirect()->back()->with('error', 'Job Card deleted successful');
  }
}
