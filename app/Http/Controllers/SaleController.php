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
use App;
use App\Models\Client;
use Carbon\Carbon;
use Auth;
use App\Models\Account;
use App\Models\AccountStatement;
use App\Models\LabourEstimation;

class SaleController extends Controller
{
 
    public function createEstimations() {

        $clients = Client::all();

        $products = DB::table('products')
                  ->select('*')
                  ->join('items', 'items.id', 'products.product_id')
                  ->join('brands', 'brands.id', 'products.brand_id')
                  ->where('products.branch_id', Auth::user()->branch_id)
                  ->get();
        
        return view('pages.pos', compact('products', 'clients'));
    }


    public function estimationSummary() {

        $sales = DB::table('estimations')
                 ->select([DB::raw("SUM(estimations.total_sales) as total_amount"), 'clients.place', DB::raw("SUM(estimations.total_sales * estimations.vat_amount) as vat_amount"), 'estimations.bill_no', 'estimations.paid_amount', 'estimations.reference', 'estimations.created_date', 'clients.client_name', 'estimations.vehicle_reg'])
                 ->join('products', 'products.pid', '=', 'estimations.product_id')
                 ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                 ->groupBy(DB::raw('estimations.bill_no'), 'estimations.paid_amount', 'clients.place',  'estimations.reference', 'estimations.vehicle_reg', 'clients.client_name', 'estimations.created_date')
                 ->where('estimations.branch_id', Auth::user()->branch_id)
                 ->orderBy('estimations.created_date', 'desc')
                 ->get();

             return view('estimations.estimations-history', compact('sales'));
    }

    public function saleSummary() {

        $products = Product::all();
        
        $clients = DB::table('clients')
                ->select('client_name', 'place', 'id')
                ->get();

        $sales = DB::table('sales')
                 ->select([DB::raw("SUM(sales.total_sales) as total_amount"), 'clients.place', DB::raw("SUM(sales.total_sales * sales.vat_amount) as vat_amount"), 'sales.bill_no', 'sales.paid_amount', 'sales.reference', 'sales.created_date', 'clients.client_name', 'sales.vehicle_reg'])
                 ->join('products', 'products.pid', '=', 'sales.product_id')
                 ->leftjoin('clients', 'clients.id', '=', 'sales.client_name')
                 ->groupBy(DB::raw('sales.bill_no'), 'sales.paid_amount', 'clients.place',  'sales.reference', 'sales.vehicle_reg', 'clients.client_name', DB::raw('sales.created_date'))
                 ->where('sales.branch_id', Auth::user()->branch_id)
                 ->orderBy('sales.created_date', 'desc')
                 ->get();

                return view('pages.sales-summary', compact('sales', 'products', 'clients'));
    }


    public function getProfitLoss() {

        $years = DB::table('years')
                ->select('*')
                ->orderby('id', 'desc')
                ->get();

        return view('reports.profit_loss_form', compact('years'));
    }
    

    public function getSalesReport() {

        $products = Product::all();

        $sales = DB::table('sales')
                ->select('*')
                ->join('products', 'products.pid', '=', 'sales.product_id')
                ->join('items', 'items.id', 'products.product_id')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->get();

        return view('pages.sales-reports', compact('sales', 'products'));
    }


    public function editPOS($id) {

        $products = DB::table('products')
                  ->select('*')
                  ->join('items', 'items.id', 'products.product_id')
                  ->join('brands', 'brands.id', 'products.brand_id')
                  ->get();

         $sales = DB::table('sales')
                 ->select('sales.id as id', 'items.item_name', 'brands.title', 'sales.qty',  'sales.bill_no', 'sales.created_date', 'sales.client_name', 'sales.lpo_number', 'products.model', 'sales.selling_price', 'sales.mobile')
                 ->join('products', 'products.pid', '=', 'sales.product_id')
                 ->join('items', 'items.id', 'products.product_id')
                 ->join('brands', 'brands.id', 'products.brand_id')
                 ->where('sales.id', $id)
                 ->get();

                 return view('pages.edit-pos', compact('sales', 'products'));

    }

   
 public function addSalePoint(Request $request) {

    $total_value[]=0;
    $request->validate([
        'qty' => 'required',
    ]);
     
 for($i = 0; $i < count($request->id); $i++){

        $product_id = $request->id[$i]; 
        $qty = $request->qty[$i]; 

       $sale = Product::where('pid', $request->id[$i])->first();
 
       $check_sale = Sale::where('reference', $request->reference)->first();

      $sales = Sale::create([
        'product_id' => $product_id, 
        'qty' => $qty, 
        'selling_price' => $sale['selling_price'],
        'lpo_number'=> $check_sale['lpo_number'],
        'client_name'=> $check_sale['client_name'],
        'total_sales' => $request['qty'][$i] * $sale['selling_price'],
        'reference' => $request->reference,
        'created_date' => $check_sale['created_date']
        ]);

        if($sale) {
            $update = DB::table('products')
                    ->where('pid', $request->id[$i])
                    ->where('model', '!=', 'HUDUMA')
                    ->update(["quantity" => $sale['quantity'] - $request->qty[$i]]);
        }
     }

    return redirect()->back()->with('message', 'You have Created Estimation for '.$request->vehicle_reg);
}


    public function CreateBill($vehicle, $date) {

        $total_bill_amount = DB::table('sales')
                       ->select([DB::raw("SUM((sales.total_sales * sales.vat_amount) + sales.total_sales) as total_billed_amount")])
                       ->where('lpo_number', $vehicle)
                       ->where('created_date', $date)
                       ->get();

        return view('pages.create-sale-bills', compact('vehicle', 'date', 'total_bill_amount'));

    }

    public function CreateReference($vehicle, $date) {

        return view('pages.create-sale-reference', compact('vehicle', 'date'));

    }

    public function addBill(Request $request) {

        $request->validate([
            'bill_number' => 'required',
        ]); 
        
        $create_bill = DB::table('sales')
                    ->where('lpo_number', $request->lpo_number)
                    ->where('created_date', $request->created_date)
                    ->update(
                        ["bill_no" => $request->bill_number,
                        "created_date" => $request->created_date,
                        "lpo_number" => $request->lpo_number
                    ]);
        
         if($create_bill) {
            return redirect('sales-summary')->with('message', 'Bill Created');
         } else {
            return redirect()->back()->with('message', 'Bill Not Created');
         }
    } 

    public function addReference(Request $request) {

        $request->validate([
            'reference' => 'required',
        ]); 
        
        $reference = DB::table('sales')
                    ->where('lpo_number', $request->lpo_number)
                    ->where('created_date', $request->created_date)
                    ->update(
                        ["reference" => $request->reference,
                        "created_date" => $request->created_date,
                        "lpo_number" => $request->lpo_number
                    ]);
        
         if($reference) {
            return redirect('sales-summary')->with('message', 'Reference Number Created');
         } else {
            return redirect()->back()->with('message', 'Reference Not Created');
         }
    } 


     public function updatePOS(Request $request) {

        $update = DB::table('sales')
                ->where('id', $request->product_id)
                ->update([
                        "qty" => $request->qty,
                        "selling_price" => $request->selling_price,
                        "lpo_number" => $request->lpo_number,
                        "client_name" => $request->client_name,
                        "bill_no" => $request->bill_no,
                        'total_sales' => $request['qty'] * $request['selling_price'],
                        "mobile" => $request->phone,
                        "created_date" => $request->created_date
                        ]);

                      return redirect()->back()->with('message', 'Sale Updated successful');

              }


    public function deleteSale($id){

            $sale = Sale::where('reference', $id);

            $sale->delete();

            return redirect()->back()->with('error', 'Sale Deleted');
    
    }
   

    public function deleteSaleItems($id){

        $sale = Sale::where('id', $id);

        $sale->delete();

        return redirect()->back()->with('error', 'Sale Item Deleted');

}

    public function editSparePartDetails($id) {

        $edit = DB::table('estimations')
                    ->select('estimations.id', 'items.item_name', 'estimations.discount', 'brands.title', 'estimations.discount', 'estimations.qty', 'estimations.created_date', 'estimations.vehicle_reg', 'estimations.reference', 'clients.client_name as client', 'estimations.client_name','estimations.model', 'estimations.make', 'estimations.chassis', 'estimations.milleage', 'estimations.selling_price', 'estimations.total_sales')
                    ->join('products', 'products.pid', '=', 'estimations.product_id')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                    ->where('estimations.branch_id', Auth::user()->branch_id)
                    ->where('estimations.id', $id)
                    ->get();

       return $edit;  

    }


    public function editLabourDetails($id) {

        $edit = DB::table('labour_estimations')
                    ->select('id', 'labour_name', 'charge', 'qty', 'total_amount')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->where('id', $id)
                    ->get();

       return $edit;  

    }

    public function deleteLabourDetails($id){

        $labours = LabourEstimation::where('id', $id)->first();

        $labours->delete();

        return redirect()->back()->with('error', 'Labour Item Deleted');

}
    public function editSalesSummary($id) {

        $sale_details = DB::table('sales')
                        ->select('sales.reference', 'sales.created_date', 'sales.client_name', 'sales.lpo_number')
                        ->where('sales.reference', '=', $id)
                        ->get();

       return $sale_details;  

    }

    public function getSingleSalesDetails($id) {
        

        $sales = DB::table('sales')
                    ->select('sales.id as id', 'items.item_name', 'brands.title', 'sales.qty', 'sales.created_date', 'sales.reference', 'clients.client_name as client', 'sales.client_name', 'sales.lpo_number', 'products.model', 'sales.selling_price', 'sales.total_sales')
                    ->join('products', 'products.pid', '=', 'sales.product_id')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->leftjoin('clients', 'clients.id', 'sales.client_name')
                    ->where('sales.reference', '=', $id)
                    ->get();

       $total_sales = DB::table('sales')
                     ->where('reference', $id)
                     ->sum('total_sales');

       $client_name = DB::table('sales')
                    ->select('sales.client_name', 'clients.client_name as client', 'clients.place')
                    ->leftjoin('clients', 'clients.id', 'sales.client_name')
                    ->where('reference', '=', $id)
                    ->distinct()
                    ->get();

      $vehicle = Sale::select('lpo_number')->where('reference', $id)->distinct()->get();

      $products = DB::table('products')
                    ->select('*')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->get();
      
       $vat_calculations =  (0.18) * ($total_sales);

       if($id != ''){
         return  view('pages.sale-details', compact('sales', 'total_sales', 'id', 'products', 'client_name', 'vat_calculations', 'vehicle'));
        } else {
         return 'No Bill number available please Create';
       }
    }

    public function getEstimationsDetails($id) {
        

        $sales = DB::table('sales')
                    ->select('sales.id as id', 'items.item_name', 'brands.title', 'sales.qty', 'sales.created_date', 'sales.reference', 'clients.client_name as client', 'sales.client_name', 'sales.lpo_number', 'products.model', 'sales.selling_price', 'sales.total_sales')
                    ->join('products', 'products.pid', '=', 'sales.product_id')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->leftjoin('clients', 'clients.id', 'sales.client_name')
                    ->where('sales.reference', '=', $id)
                    ->get();

       $total_sales = DB::table('sales')
                     ->where('reference', $id)
                     ->sum('total_sales');

       $client_name = DB::table('sales')
                    ->select('sales.client_name', 'clients.client_name as client', 'clients.place')
                    ->leftjoin('clients', 'clients.id', 'sales.client_name')
                    ->where('reference', '=', $id)
                    ->distinct()
                    ->get();

      $vehicle = Sale::select('lpo_number')->where('reference', $id)->distinct()->get();

      $products = DB::table('products')
                    ->select('*')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->get();
      
       $vat_calculations =  (0.18) * ($total_sales);

       if($id != ''){
         return  view('pages.sale-details', compact('sales', 'total_sales', 'id', 'products', 'client_name', 'vat_calculations', 'vehicle'));
        } else {
         return 'No Bill number available please Create';
       }
    }
       public function getSingleSalesDetails1($id, $date) {
        
        $vehicle = Sale::select('lpo_number')->where('lpo_number', $id)->distinct()->get();

        $sales = DB::table('sales')
                    ->select('sales.id as id', 'items.item_name', 'brands.title', 'sales.qty', 'sales.created_date', 'sales.reference', 'sales.client_name', 'sales.lpo_number', 'products.model', 'sales.selling_price', 'sales.total_sales')
                    ->join('products', 'products.pid', '=', 'sales.product_id')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->where('sales.lpo_number', '=', $id)
                    ->where('sales.created_date', $date)
                    ->get();

       $total_sales = DB::table('sales')
                     ->where('lpo_number', $id)
                     ->where('created_date', $date)
                     ->sum('total_sales');
        

       $client_name = DB::table('sales')
                    ->select('client_name')
                    ->where('lpo_number', '=', $id)
                    ->distinct()
                    ->get();
      
    
       $vat_calculations =  (0.18) * ($total_sales);

       if($id != ''){
         return  view('pages.backup-sale-details', compact('sales', 'total_sales', 'vehicle', 'client_name', 'vat_calculations', 'id'));
        } else {
         return 'No Bill number available please Create';
       }

    }

    public function displaySalesReport(Request $request)
    {
        $sales = DB::table('sales')
                 ->select('sales.id as id', 'items.item_name', 'brands.title', 'sales.qty', 'sales.created_date', 'sales.client_name', 'sales.lpo_number', 'products.model', 'sales.selling_price')
                 ->join('products', 'products.pid', '=', 'sales.product_id')
                 ->join('items', 'items.id', 'products.product_id')
                 ->join('brands', 'brands.id', 'products.brand_id')
                 ->orderBy('sales.created_date','asc')
                 ->get();
  
        return view('pages.reports', compact('sales'));
        
    }


    public function salesReportPdf(Request $request) {
        
        $current_year = Carbon::now()->year;
        
        $settings= DB::table('general_settings')->select('business_name', 'type', 'address', 'logo_file')->get();


        $year = Year::where('current_year', $current_year)->select('*')->first();

      
        $sales = DB::table('invoices')
                        ->select('clients.place', 'invoices.bill_amount', 'job_cards.job_card_no', 'invoices.created_date', 'clients.client_name', 'estimations.vehicle_reg')
                        ->join('job_cards', 'job_cards.job_card_no', '=', 'invoices.job_card_no')
                        ->join('estimations', 'job_cards.job_card_no', '=', 'estimations.job_card_no')
                        ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                        ->groupBy('clients.place',  'job_cards.job_card_no', 'invoices.bill_amount', 'estimations.vehicle_reg', 'clients.client_name', 'invoices.created_date')
                        ->where('invoices.branch_id', Auth::user()->branch_id)
                        ->whereBetween('invoices.created_date', [$request->startdate, $request->enddate])
                        ->orderBy('invoices.created_date', 'desc')
                        ->get();

        
        $startdate= $request->startdate;
        $enddate= $request->enddate;


     $total_sales = DB::table('invoices')
                ->join('job_cards', 'job_cards.job_card_no', '=', 'invoices.job_card_no')
                ->join('estimations', 'job_cards.job_card_no', '=', 'estimations.job_card_no')
                ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                ->where('invoices.branch_id', Auth::user()->branch_id)
                ->whereBetween('invoices.created_date', [$request->startdate, $request->enddate])
                ->orderBy('invoices.created_date', 'desc')
                ->sum('bill_amount');

                
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.pdf_sales_reports', compact('sales',  'startdate', 'enddate', 'total_sales', 'settings'));
        return $pdf->stream();


    }


    public function getAnnualSales() {

        $years = DB::table('years')
                ->select('*')
                ->orderby('id', 'asc')
                ->get();

        return view('pages.annual-sales-form', compact('years'));
    }


    public function annualSalesReportPdf(Request $request) {


            $sales_report = Year::find($request->year);
            $startdate= $sales_report->current_year;
            $enddate= $sales_report->previous_year;
            
            $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();

            $total_inventory_sales_value = DB::table('sales')->select(DB::raw('SUM(qty * selling_price) as total_sales'),
                                            DB::raw('SUM(sales.total_sales * sales.vat_amount) as vat_amount'))
                                           ->whereBetween('sales.created_date', [$sales_report->first_date, $sales_report->second_date])
                                           ->get();

                   $sales = DB::table('invoices')
                            ->select('clients.place', 'invoices.bill_amount', 'job_cards.job_card_no', 'invoices.created_date', 'clients.client_name', 'estimations.vehicle_reg')
                            ->join('job_cards', 'job_cards.job_card_no', '=', 'invoices.job_card_no')
                            ->join('estimations', 'job_cards.job_card_no', '=', 'estimations.job_card_no')
                            ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                            ->groupBy('clients.place',  'job_cards.job_card_no', 'invoices.bill_amount', 'estimations.vehicle_reg', 'clients.client_name', 'invoices.created_date')
                            ->where('invoices.branch_id', Auth::user()->branch_id)
                            ->whereBetween('invoices.created_date', [$sales_report->first_date, $sales_report->second_date])
                            ->orderBy('invoices.created_date', 'desc')
                            ->get();


            // $sales = DB::table('sales')
            //         ->select([DB::raw("SUM(sales.total_sales) as total_amount"), 'clients.place', DB::raw("SUM(sales.total_sales * sales.vat_amount) as vat_amount"), 'sales.bill_no', 'sales.paid_amount', 'sales.reference', 'sales.created_date', 'clients.client_name', 'sales.lpo_number'])
            //         ->join('products', 'products.pid', '=', 'sales.product_id')
            //         ->leftjoin('clients', 'clients.id', '=', 'sales.client_name')
            //         ->groupBy(DB::raw('sales.bill_no'), 'sales.paid_amount', 'clients.place',  'sales.reference', 'sales.lpo_number', 'clients.client_name', DB::raw('sales.created_date'))
            //         ->whereBetween('sales.created_date', [$sales_report->first_date, $sales_report->second_date])
            //         ->orderBy('sales.created_date', 'asc')
            //         ->get();
            
            // $total_sales =  DB::table('sales')
            //                 ->select([DB::raw("SUM(sales.total_sales) as total_amount"), DB::raw("SUM(sales.total_sales * sales.vat_amount) as vat_amount")])
            //                 ->leftjoin('clients', 'clients.id', '=', 'sales.client_name')
            //                 ->whereBetween('sales.created_date', [$sales_report->first_date, $sales_report->second_date])
            //                 ->get();
            $total_sales = DB::table('invoices')
                        ->join('job_cards', 'job_cards.job_card_no', '=', 'invoices.job_card_no')
                        ->join('estimations', 'job_cards.job_card_no', '=', 'estimations.job_card_no')
                        ->leftjoin('clients', 'clients.id', '=', 'estimations.client_name')
                        ->where('invoices.branch_id', Auth::user()->branch_id)
                        ->whereBetween('invoices.created_date', [$sales_report->first_date, $sales_report->second_date])
                        ->orderBy('invoices.created_date', 'desc')
                        ->sum('bill_amount');
                        
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('pages.annual-sales-report-pdf', compact('sales',  'startdate', 'enddate', 'total_sales', 'settings', 'total_inventory_sales_value'));
            return $pdf->stream();
    }

public function getClientPayments() {

$clients =  DB::table('clients')->select('*')
            ->where('clients.branch_id', Auth::user()->branch_id)
            ->get();

$sales = DB::table('invoices')
        ->select([DB::raw("SUM(client_payments.paid_amount) as paid_amount"), 'invoices.vehicle_reg', 'invoices.invoice_number', 'invoices.bill_amount', 'clients.client_name', 'clients.place', 'invoices.created_date'])
        ->leftjoin('client_payments', 'invoices.invoice_number', '=', 'client_payments.bill_no')
        ->leftjoin('clients', 'clients.id', '=', 'invoices.client_id')
        ->groupBy('invoices.bill_amount', 'invoices.vehicle_reg', 'invoices.invoice_number', 'client_payments.bill_amount', 'clients.client_name',  'clients.place', 'invoices.created_date')
        ->where('invoices.branch_id', Auth::user()->branch_id)
        ->orderBy('invoices.created_date', 'desc')
        ->get();

        $accounts = Account::where('is_active', '!=', '')->get();

        return view('pages.client-payments', compact('sales', 'accounts', 'clients'));

    }

    public function saveClientDebit(Request $request) {

        // return $request->client_id;

            $accounts = Estimation::where('client_name', $request->client_id)->where('branch_id', Auth::user()->branch_id)->orderBy('account_no', 'desc')->first();
            $profoma_id = Estimation::where('branch_id', Auth::user()->branch_id)->orderBy('id', 'desc')->first();
           
            if(!$profoma_id) {
                $profoma = 1;
            } else {
                $profoma = $profoma_id['profoma_invoice'] + 1 ? : 1;
            }
            if (!$accounts) {
                $account_no = 1;
            } else {
            $account_no = $accounts['account_no'] + 1 ? $accounts['account_no'] + 1: 1;
            }

             $client_ID = Client::where('id', $request->client_id)->where('branch_id', Auth::user()->branch_id)->first();
            
           $account = substr($client_ID['client_name'], 0,3).'-'.$account_no;

        $add = Invoice::create([
            'branch_id' => Auth::user()->branch_id, 
            'user_id' => Auth::user()->id, 
            'client_id' => $request->client_id,
            'reference' => date('Ymdis'),
            'make' => $request->make,
            'account' => $account,
            'bill_amount' => $request['invoice_amount'],
            'invoice_number'=> $request['invoice_no'],
            'vehicle_reg'=> $request['vehicle_reg'],
            'created_date' => $request['created_date']
            ]);

            return redirect()->back()->with('message', 'Debit saved successfull!');

    }


    public function getSingleClientPaymentDetails($id) {

        $client_details = DB::table('invoices')
                         ->select('invoices.invoice_number', 'invoices.bill_amount', 'invoices.client_id')
                         ->where('invoices.invoice_number', '=', $id)
                         ->where('invoices.branch_id', Auth::user()->branch_id)
                         ->distinct()
                         ->get();

        return $client_details;

   }


   public function updateSaleValue(Request $request) {

            $request->validate([
                'quantity' => 'required',
            ]);

            $find_sales = Sale::where('id', $request->id)->first();

            $update = DB::table('sales')
                    ->where('id', $request->id)
                    ->update(
                        ['qty' => $request->quantity,
                         'selling_price' => $request->selling_price,
                         'total_sales' => $request->quantity * $request->selling_price
                        ]);

           return redirect()->back()->with('message', 'Sale updated successfull!');
            
   }


   public function updateSaleSummary(Request $request) {

    $request->validate([
        'client_name' => 'required',
    ]);

    $find_sales = Sale::where('reference', $request->reference_no)->first();

    $update = DB::table('sales')
            ->where('reference', $find_sales['reference'])
            ->update(
                ['client_name' => $request->client_name,
                 'lpo_number' => $request->vehicle_reg,
                 'created_date' => $request->created_date
                ]);

   return redirect()->back()->with('message', 'Sale Updated Successful!');
    
}


   public function addClientPayments(Request $request) {

    $request->validate([
        'paid_amount' => 'required',
    ]);

        $account_balance = Account::where('name', 'SALES ACCOUNT')->where('branch_id', Auth::user()->branch_id)->first();
        $client_name = Client::where('id', $request->client_id)->first();
        $balance =  $account_balance['total_balance'] + $request->paid_amount;

        $name = $client_name['client_name'];

        $accounting = DB::table('accounts')
                    ->where('id', $request->account_id)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->update(["total_balance" => $balance]);

        $account_statement = AccountStatement::create([
            'branch_id' => Auth::user()->branch_id,
            'account_id' => $request->account_id,
            'reference' => date('Yhis'), 
            'name' => $name,
            'debit' => '',
            'credit' => $request->paid_amount,
            'initial_balance' => $account_balance['total_balance'],
            'balance' => $balance,
            'created_at' => $request->created_date
        ]);
        $add = ClientPayment::create([
            'branch_id' => Auth::user()->branch_id, 
            'client_id' => $request->client_id,
            'bill_amount' => $request['bill_amount'],
            'bill_no'=> $request['invoice_number'],
            'paid_amount'=> $request['paid_amount'],
            'created_date' => $request['created_date']
            ]);

            return redirect()->back()->with('message', 'Payment saved Successful!');

        }


  public function getPriceDetails($id) {

    $stocks = DB::table('price_lists')
            ->select('products.pid', 'items.item_name', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
            ->join('items', 'items.id', 'products.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
            ->groupBy('products.pid', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'products.selling_price', 'brands.title', 'products.quantity', 'products.model')
            ->where('price_lists.branch_id', Auth::user()->branch_id)
            ->where('price_lists.id', $id)
            // ->where('price_lists.client_id', '!=', '0')
            ->get();

return response()->json($stocks);

}

  public function createPOS() {

    }

    public function getClientPaymentDetails($bill) {
                   
        $client_payments = ClientPayment::where('bill_no', $bill)->get();

        $total_paid = DB::table('client_payments')->where('bill_no', $bill)->sum('paid_amount');

        $client_name = DB::table('client_payments')
                     ->select('client_payments.bill_no', 'estimations.client_name', 'clients.client_name as client_name', 'clients.place')
                     ->join('invoices', 'invoices.invoice_number', 'client_payments.bill_no')
                     ->join('estimations', 'estimations.job_card_no', 'invoices.job_card_no')
                     ->join('clients', 'clients.id', 'estimations.client_name')
                     ->where('bill_no', $bill)
                     ->distinct()
                     ->get();

        return view('pages.client-payment-details', compact('client_payments', 'bill', 'total_paid', 'client_name'));
    }

    // REport 

    public function getSingleSalesDetailsPDF($id) {
        

      $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
 
        $sales = DB::table('sales')
                    ->select('sales.id as id', 'items.item_name', 'brands.title', 'sales.qty', 'clients.client_name as client_name', 'clients.place', 'sales.created_date', 'sales.reference', 'sales.client_name as client', 'sales.lpo_number', 'products.model', 'sales.selling_price', 'sales.total_sales')
                    ->join('products', 'products.pid', '=', 'sales.product_id')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->leftjoin('clients', 'clients.id', 'sales.client_name')
                    ->where('sales.reference', '=', $id)
                    ->get();

       $total_sales = DB::table('sales')
                     ->where('reference', $id)
                     ->sum('total_sales');

       $client_name = DB::table('sales')
                    ->select('clients.client_name', 'clients.id', 'clients.place')
                    ->leftjoin('clients', 'clients.id', '=', 'sales.client_name')
                    ->where('reference', '=', $id)
                    ->distinct()
                    ->get();

      $vehicle = Sale::select('lpo_number')->where('reference', $id)->distinct()->get();

      $products = DB::table('products')
                    ->select('*')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->get();
      
     $vat_calculations =  (0.18) * ($total_sales);

     $pdf = App::make('dompdf.wrapper');
     $pdf->loadView('pages.sale-details-pdf', compact('sales', 'total_sales', 'id', 'products', 'client_name', 'vat_calculations', 'vehicle', 'settings'));
     return $pdf->stream();
       
    }

    public function getClientDeptors() {

        $years = Year::all();

        return view('reports.client-deptors', compact('years'));
    }

    public function getClientDeptorReport(Request $request) {
        
        $settings= DB::table('general_settings')->select('business_name', 'type', 'address', 'logo_file')->get();


        $date_time = Year::where('id', $request->year)->select('*')->first();

        $total_owed_amount = Sale::whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                            ->sum('sales.paid_amount');

        $total_paid_amount = Sale::whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                            ->sum('sales.paid_amount');

        $total_sales_amount =  DB::table('clients')
                            ->select([DB::raw("SUM((sales.total_sales * sales.vat_amount) + sales.total_sales) as total_sales")])
                            ->join('sales', 'clients.id', 'sales.client_name')
                            ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                            ->get();


         $clients = DB::table('clients')
                    ->select([DB::raw("SUM((sales.total_sales * sales.vat_amount) + sales.total_sales) as total_sales"), DB::raw("SUM(sales.paid_amount) as paid_amount"), 'clients.client_name as client_name'])
                    ->join('sales', 'clients.id', 'sales.client_name')
                    ->groupBy('client_name' )
                    ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                    ->orderBy('clients.client_name','asc')
                    ->distinct()
                    ->get();
        
        $current_year= $date_time->current_year;
        $previous_year= $date_time->previous_year;

       
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('reports.client-deptor-report-pdf', compact('clients',  'current_year', 'previous_year', 'total_owed_amount', 'settings', 'total_paid_amount', 'total_sales_amount'));
        return $pdf->stream();

      }
 

    public function getDeptorsReportFilter() {

        $clients = Client::all();
        $years = Year::all();

        return view('reports.deptor-report-filter', compact('clients', 'years'));
    }


    public function getDeptortReportByClient(Request $request) {

        $settings= DB::table('general_settings')->select('business_name', 'type', 'address', 'logo_file')->get();

        $date_time = Year::where('id', $request->year)->select('*')->first();

            $clients = DB::table('job_cards')
                    ->select(['clients.place', 'job_cards.amount', 'job_cards.invoice_no', 'invoices.bill_amount', 'job_cards.job_card_no', 'estimations.reference', 'estimations.created_date', 'job_cards.status', 'clients.client_name', 'estimations.vehicle_reg'])
                    ->join('estimations', 'estimations.reference', '=', 'job_cards.estimate_reference')
                    ->join('invoices', 'invoices.job_card_no', '=', 'job_cards.job_card_no')
                    ->join('clients', 'clients.id', '=', 'estimations.client_name')
                    ->groupBy('job_cards.job_card_no', 'clients.place',  'invoices.bill_amount', 'amount', 'invoice_no', 'job_cards.status', 'estimations.reference', 'estimations.vehicle_reg', 'job_cards.status', 'clients.client_name', 'estimations.created_date')
                    ->where('job_cards.branch_id', Auth::user()->branch_id)
                    ->where('estimations.client_name', $request->client_id)
                    ->orderBy('job_cards.created_date', 'desc')
                    ->get();

      $total_paid_amount = Sale::whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                        ->where('vehicle_reg', $request->vehicle_reg)
                        ->sum('sales.paid_amount');

     $total_sales_amount =  DB::table('clients')
                        ->select([DB::raw("SUM((sales.total_sales * sales.vat_amount) + sales.total_sales) as total_sales")])
                        ->join('sales', 'clients.id', 'sales.client_name')
                        ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                        ->where('vehicle_reg', $request->vehicle_reg)
                        ->get();        
        if($date_time) {
        $current_year= $date_time->current_year;
        $previous_year= $date_time->previous_year;
       } else {
       $current_year = 'YEAR';
       $previous_year = "ALL";
    }

       
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('reports.deptor-report-by-clients-pdf', compact('clients',  'current_year', 'previous_year', 'total_paid_amount', 'settings', 'total_sales_amount', 'vehicle_reg'));
        return $pdf->stream();

    }
    
    public function profitReportPdf(Request $request) {

        $startdate= $request->startdate;
        $enddate= $request->enddate;

        $current_year = Carbon::now()->year;

        $settings= DB::table('general_settings')->select('business_name', 'type')->get();

        $year = Year::where('current_year', $current_year)->select('*')->first();

        $total_sales = DB::table('sales')->select(DB::raw('SUM(qty * selling_price) as total_sales'))
                                       ->whereBetween('sales.created_date', [$request->startdate, $request->enddate])
                                       ->get();

        $total_quantity = DB::table('sales')->select(DB::raw('SUM(qty) as quantity'))
                                       ->whereBetween('sales.created_date', [$request->startdate, $request->enddate])
                                       ->get();
    
	    $total_profits =  DB::table('sales')
                        ->select([DB::raw("SUM((sales.selling_price * sales.qty) - (products.purchasing_price * sales.qty)) as profit")])
                        ->leftjoin('products', 'products.pid', '=', 'sales.product_id')
                        ->whereBetween('sales.created_date', [$request->startdate, $request->enddate])
                        ->get();
   
       $total_loss =  DB::table('sales')
                        ->select([DB::raw("SUM((products.purchasing_price * sales.qty) - (sales.selling_price * sales.qty)) as loss")])
                        ->leftjoin('products', 'products.pid', '=', 'sales.product_id')
                        ->whereBetween('sales.created_date', [$request->startdate, $request->enddate])
                        ->get();

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('reports.profit_loss_pdf', compact('settings', 'total_profits',  'total_loss', 'startdate', 'enddate', 'total_sales', 'total_quantity'));
            return $pdf->stream();
    }

   public function fetchClientPayment($id){

        $payments = ClientPayment::find($id);

        return $payments;
    }

    public function updateClientPayment(Request $request) {

        $payment = ClientPayment::find($request->id);

        $payment->paid_amount = $request->paid_amount;
        $payment->created_date = $request->created_date;

        $payment->save();

        return redirect()->back()->with('message', 'Payment Updated successful');

    }

    public function deleteClientPayment($id){

        $pay = ClientPayment::findOrFail($id);
    
        $pay->delete();
    
        if(!$pay) {
            return redirect()->back()->with('warning', ' Payment Not deleted');
        } else {
            return redirect()->back()->with('error', 'Payment deleted successful');
        }
    }
  }
