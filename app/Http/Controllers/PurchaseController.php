<?php

namespace App\Http\Controllers;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Payment;
use App\Models\Year;
use App\Models\PaymentStatus;
use App\Models\Invoice;
use Illuminate\Http\Request;
use DB;
use App;
use Carbon\Carbon;
use Auth;
use App\Models\Account;
use App\Models\AccountStatement;

class PurchaseController extends Controller
{
 
    public function index() {
        
        $purchases = Purchase::all();
        $items = Item::all();
        $suppliers = Supplier::all();

        return view('pages.create-purchases', compact('purchases', 'items', 'suppliers'));
    }


    public function createPurchases(Request $request)
    {
        
        $this->validate($request, [
            'invoice_number' => 'required',
            'product_id' => 'required',
            'supplier_id' => 'required',
            // 'invoice_file' => 'required|mimes:pdf|max:2048',
        ], [
            'invoice_number.required' => 'Invoice Number Should be Unique'
        ]);
        
         $product_id = $request->product_id;
         $supplier_id = $request->supplier_id;
         $invoice_no = $request->invoice_number;
         $create_at = $request->created_date;
       
         if ($request->hasFile('invoice_file')) {
            $image = $request->file('invoice_file');
            $invoice_file = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/attachments');
            $image->move($destinationPath, $invoice_file);
                } else {
                $invoice_file = '';
               }
        //  $fileName = time().'.'.$request->invoice_file;  
        //  $request->invoice_file->move(public_path('/attachments/'), $fileName);
    
        if($request->vat_type == "1") {
            $vat_amount = (0.18);
        } else {
           $vat_amount = 0;
        }

         $total_value[] = 0;

        for($i = 0; $i < count($product_id); $i++){
             $item_id = $request->product_id[$i];
             $make = $request->make[$i];
             $model = $request->model[$i];
             $quantity = $request->quantity[$i];
             $purchase_price = $request->purchase_price[$i];
             $total =  $request->quantity[$i] * $request->purchase_price[$i];

            $purchases = Purchase::create([
                'product_id' => $item_id,
                'branch_id' => Auth::user()->branch_id,
                'make' => $make,
                'model' => $model,
                'quantity' => $quantity,
                'purchase_price' => $purchase_price,
                'supplier_id' => $supplier_id,
                'invoice_number' => $invoice_no,
                'part_number' => $request->part_number[$i],
                'vat_type' => $request->vat_type,
                'vat_amount' => $vat_amount,
                'invoice_file' => $invoice_file ?: NULL,
                'total_purchase' => $total,
                'created_date' => $create_at,
                'status' => 1,
            ]);
        }

         return redirect('/create-purchases')->with('message', 'Purchase Added Successful!');
    }


    public function purchaseReport()
    {

        $purchases = DB::table('purchases')
                ->select([DB::raw("SUM(total_purchase) as total"), 'invoice_number', 'created_date','supplier_id', 'suppliers.supplier_name', DB::raw("SUM(quantity) as total_qty")])
                ->groupBy(DB::raw('invoice_number'), 'supplier_id', 'created_date', 'suppliers.supplier_name')
                ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                ->where('purchases.branch_id', Auth::user()->branch_id)
                ->orderBy('created_date')
                ->get();
       
        return view('pages.purchase-reports', compact('purchases'));

    }

    public function deletePurchaseInvoice($id){

        $purchase = DB::delete('delete from purchases where invoice_number = ?',[$id]);

        if($purchase)
          return redirect()->back()->with('error', 'Purchase Invoice deleted successful');

   }


   public function deletePurchaseItem($id){

    $purchase = Purchase::findOrFail($id);

    $purchase->delete();

    if(!$purchase) {
        return redirect()->back()->with('error', ' Item Not deleted');
    } else {
        return redirect()->back()->with('error', 'Purchase Item deleted successful');
    }

}

    public function getPurchaseHistory()
    {
    
        $suppliers = Supplier::all();

        $purchases = DB::table('purchases')
                ->select([DB::raw("SUM(purchases.total_purchase) as total"),
                DB::raw("SUM(purchases.total_purchase * purchases.vat_amount) as calculated_vat_amount"),  "purchases.lpo_number", "purchases.lpo_file", 'purchases.invoice_number', 'invoice_file', 
                'created_date', 'suppliers.supplier_name'])
                ->groupBy('purchases.invoice_number', 'purchases.created_date',"purchases.lpo_number", "purchases.lpo_file", "purchases.invoice_file", "purchases.supplier_id", 'suppliers.supplier_name')
                ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                ->where('purchases.branch_id', Auth::user()->branch_id)
                ->orderBy('created_date', 'desc')
                ->get();

        return view('pages.purchase-summary', compact('purchases', 'suppliers'));

    }

    public function getPurchaseDetails(Request $request)
    {
        $items = Item::all();
        
        $id = $request->route('id');
        $date = $request->route('date');
        
        $check_vat = Purchase::where('invoice_number', $id)
                               ->select('vat_type')
                               ->where('vat_type', 1)
                               ->get();

        $purchases = DB::table('purchases')
                    ->select('purchases.id', 'purchases.product_id', 'purchases.make', 'purchases.part_number', 'purchases.vat_type', 'purchases.model', 'purchases.purchase_price', 'purchases.quantity', 'purchases.paid_amount', 'purchases.total_purchase', 'items.item_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->groupBy('purchases.created_date', 'purchases.id', 'purchases.part_number', 'purchases.product_id', 'purchases.make', 'purchases.vat_type', 'purchases.model', 'purchases.purchase_price', 'purchases.quantity', 'purchases.paid_amount', 'purchases.total_purchase', 'items.item_name')
                    ->where('purchases.invoice_number', '=', $id)
                    ->where('purchases.created_date', $date)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->get();

    $total_purchases = DB::table('purchases')->where('invoice_number', $id)->where('purchases.created_date', $date)->sum('total_purchase');
      
    if(count($check_vat) > 0) {
        $vat_calculations = (0.18) * $total_purchases;
       } else {
        $vat_calculations = '0.00';
       }

       $supplier_name = DB::table('purchases')
                    ->select('suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->groupBy('purchases.created_date', 'suppliers.supplier_name')
                    ->where('invoice_number', '=', $id)
                    ->where('purchases.created_date', $date)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->get();

        return view('pages.purchase-details', compact('purchases', 'id', 'total_purchases', 'vat_calculations', 'items', 'supplier_name'));

    }


    public function getSinglePurchase(Request $request)
    {
        $id = $request->route('id');
        
        $purchases = DB::table('purchases')
                    ->select('purchases.id as id', 'purchases.make', 'purchases.vat_amount', 'purchases.model', 'purchases.purchase_price', 'purchases.quantity', 'purchases.paid_amount', 'purchases.total_purchase', 'items.item_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('purchases.id', '=', $id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->get();

        return $purchases;

    }

    public function getPaymentDetails($purchase_id) {

        //   $purchase_id = $_GET['product_id'];

         $purchases = DB::table('purchases')
                    ->select('purchases.id as id', 'purchases.make', 'purchases.model','purchases.purchase_price', 'purchases.quantity', 'items.item_name')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('purchases.id', '=', $purchase_id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->get();

          return $purchases;

    } 


    public function editPurchaseDetails($purchase_id) {

         $purchases = DB::table('purchases')
                    ->select('purchases.id', 'purchases.make', 'purchases.model','purchases.purchase_price', 'purchases.quantity', 'items.item_name')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('purchases.id', '=', $purchase_id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->get();

          return $purchases;

    } 

    public function savePayment(Request $request) {

        $payment = DB::table('purchases')
                    ->where('id', $request->id)
                    ->update(["paid_amount" => $request['payment']]);

        return redirect()->back()->with('message', 'Amount Updated Successful!');

      }

      public function updatePurchase(Request $request) {

        $total_price = $request->purchase_price * $request->quantity;

        $payment = DB::table('purchases')
                    ->where('id', $request->id)
                    ->update(["make" => $request->make, "model" => $request->model,"quantity" => $request->quantity, "purchase_price" => $request->purchase_price,"total_purchase" => $total_price ]);

        return redirect()->back()->with('message', 'Purchase Updated Successful!');

      }
    

      public function updatePurchaseDetails(Request $request) {
       
        $this->validate($request, [
            // 'invoice_file' => 'required|mimes:pdf|max:2048',
        // ], [
        //     'invoice_number.required' => 'Invoice Number Should be Unique'
        ]);

        if($request->vat_type == 1) {
            $vat_amount = (0.18);
        } else {
           $vat_amount = 0;
        }

         $fileName = time().'.'.$request->invoice_file;  
         if($fileName != '') {
         } else {
            $request->invoice_file->move(public_path('/attachments/'), $fileName);
         }
        
        $update = DB::table('purchases')
                    ->where('invoice_number', $request->invoice_no)
                    ->update(["vat_type" => $request->vat_type, "supplier_id" => $request->supplier_id, "vat_amount" => $vat_amount, "invoice_file" => $fileName, "created_date" => $request->created_date]);

        return redirect()->back()->with('message', 'Purchase Updated Successful!');

      }


      public function purchaseReportPdf(Request $request) {

        $current_year = Carbon::now()->year;
        $settings= DB::table('general_settings')->select('business_name', 'type', 'logo_file', 'address')->get();

        $year = Year::where('current_year', $current_year)->select('*')->first();

        $total_inventory_purchases_value  = DB::table('purchases')->select(DB::raw('SUM(purchases.total_purchase) as total_purchase_amount'), 
                                            DB::raw('SUM(purchases.total_purchase * purchases.vat_amount) as vat_amount'))
                                           ->whereBetween('purchases.created_date', [$year->first_date, $year->second_date])
                                           ->where('purchases.branch_id', Auth::user()->branch_id)
                                           ->get();


        $purchases = DB::table('purchases')
                    ->select([DB::raw("SUM(purchases.total_purchase) as total"), DB::raw("SUM(purchases.total_purchase * purchases.vat_amount) as calculated_vat_amount"),  "purchases.lpo_number", "purchases.lpo_file", 'purchases.invoice_number', 'invoice_file', 
                    'created_date','purchases.supplier_id', 'purchases.vat_amount', 'suppliers.supplier_name'])
                    ->groupBy(DB::raw('purchases.invoice_number'), 'supplier_id', 'created_date', 'purchases.vat_amount',"purchases.lpo_number", "purchases.lpo_file", 'invoice_file', 'suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->whereBetween('created_date', [$request->startdate, $request->enddate])
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->orderBy('created_date', 'asc')
                    ->get();

        $startdate= $request->startdate;
        $enddate= $request->enddate;

        $total_purchases =  DB::table('purchases')
                            ->select([DB::raw("SUM(purchases.total_purchase) as total"), DB::raw("SUM(purchases.total_purchase * purchases.vat_amount) as calculated_vat_amount")])
                            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                            ->whereBetween('created_date', [$request->startdate, $request->enddate])
                            ->where('purchases.branch_id', Auth::user()->branch_id)
                            ->get();

        $total_item= DB::table('purchases')->whereBetween('created_date', [$request->startdate, $request->enddate])->sum('quantity');

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.purchase-report-pdf', compact('purchases', 'settings', 'startdate', 'enddate', 'total_purchases', 'total_item', 'total_inventory_purchases_value'));
        return $pdf->stream();


    }

    public function getSupplierPurchasePayments()
    {
        $purchases = DB::table('purchases')
        ->select(
            DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_amount"),
            DB::raw("COALESCE(payment_totals.total_paid_amount, 0) as total_paid_amount"),
            'purchases.created_date',
            'purchases.invoice_number',
            'suppliers.supplier_name'
        )
        ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
        ->leftJoin(DB::raw('(SELECT invoice_number, SUM(paid_amount) as total_paid_amount FROM payments GROUP BY invoice_number) as payment_totals'), 'payment_totals.invoice_number', '=', 'purchases.invoice_number')
        ->where('purchases.branch_id', Auth::user()->branch_id)
        ->groupBy(
            'purchases.invoice_number',
            'suppliers.supplier_name',
            'purchases.created_date',
            'payment_totals.total_paid_amount'
        )
        ->orderBy('purchases.created_date', 'desc')
        ->get();
    
       $accounts =  DB::table('accounts')
                    ->select('*')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->get();

        return view('pages.supplier-payments', compact('purchases', 'accounts'));

    }

public function getSingleSupplierPaymentsDetails($invoice) {

      
        $supplier_payments = Payment::where('invoice_number', $invoice)->get();

        $total_purchases = DB::table('payments')->where('invoice_number', $invoice)->sum('paid_amount');

        $supplier_name = DB::table('purchases')
                     ->select('suppliers.supplier_name')
                     ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                     ->join('items', 'items.id', 'purchases.product_id')
                     ->where('invoice_number', '=', $invoice)
                     ->where('purchases.branch_id', Auth::user()->branch_id)
                     ->distinct()
                     ->get();

        return view('pages.supplier-payment-details', compact('supplier_payments', 'invoice', 'total_purchases', 'supplier_name'));

    }
    
    public function getPurchaseDetailedValues($invoice_id)
    {
    
        $id = $invoice_id;
        $check_vat = Purchase::where('invoice_number', $id)
                    ->select('vat_type')
                    ->where('vat_type', 1)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->get();

        $purchases = DB::table('purchases')
                    ->select('purchases.id as id', 'purchases.make', 'purchases.vat_amount', 'purchases.model', 'purchases.purchase_price', 'purchases.quantity', 'purchases.paid_amount', 'purchases.total_purchase', 'items.item_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('invoice_number', '=', $id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->get();

       $total_purchases = DB::table('purchases')->where('invoice_number', $id)        
       ->where('purchases.branch_id', Auth::user()->branch_id)
       ->sum('total_purchase');

       if(count($check_vat) > 0) {
        $vat_calculations = (0.18) * $total_purchases;
       } else {
        $vat_calculations = '0.00';
       }

       $supplier_name = DB::table('purchases')
                    ->select('suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('invoice_number', '=', $id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->distinct()
                    ->get();


        return view('pages.purchase-details', compact('purchases', 'vat_calculations', 'id', 'total_purchases', 'supplier_name'));

    } 


    public function getPurchaseInvoiceValues($invoice_id)
    {
    
        $id = $invoice_id;
        
        $check_vat = Purchase::where('invoice_number', $id)
                    ->select('vat_type')
                    ->where('vat_type', 1)
                    ->get();

        $purchases = DB::table('purchases')
                    ->select('purchases.id as id', 'purchases.make', 'purchases.vat_amount', 'purchases.model', 'purchases.purchase_price', 'purchases.quantity', 'purchases.paid_amount', 'purchases.total_purchase', 'items.item_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->where('invoice_number', '=', $id)
                    ->get();

       $total_purchases = DB::table('purchases')->where('invoice_number', $id)
       ->where('purchases.branch_id', Auth::user()->branch_id)
       ->sum('total_purchase');

    //    return $total_purchases;

       if(count($check_vat) > 0) {
        $vat_calculations = (18/100) * $total_purchases;
       } else {
        $vat_calculations = '0.00';
       }

       $supplier_name = DB::table('purchases')
                    ->select('suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('invoice_number', '=', $id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->distinct()
                    ->get();


        return view('pages.invoice-details', compact('purchases', 'vat_calculations', 'id', 'total_purchases', 'supplier_name'));

    } 

    public function getPaymentForm(Request $request)
    {
    
        $id = $request->route('id');
        
        $purchases = DB::table('purchases')
                    ->select('purchases.id as id', 'purchases.make', 'purchases.vat_amount', 'purchases.model', 'purchases.purchase_price', 'purchases.quantity', 'purchases.paid_amount', 'purchases.total_purchase', 'items.item_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('invoice_number', '=', $id)
                    ->get();

       $total_purchases = DB::table('purchases')->where('invoice_number', $id)->sum('total_purchase');

       $supplier_name = DB::table('purchases')
                    ->select('suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('invoice_number', '=', $id)
                    ->distinct()
                    ->get();


        // return view('pages.create-payments', compact('purchases', 'id', 'total_purchases', 'supplier_name'));

    }


    public function getPaymentPurchaseDetails(Request $request)
    {
    
        $id = $request->route('id');
        
        $purchases = DB::table('purchases')
                    ->select([DB::raw("SUM(total_purchase) as total"), DB::raw("SUM(purchases.total_purchase * purchases.vat_amount) as calculated_vat_amount"), 'purchases.vat_amount', 'suppliers.supplier_name', 'purchases.supplier_id', 'purchases.invoice_number'])
                    ->groupBy(DB::raw('invoice_number'), 'suppliers.supplier_name', 'purchases.supplier_id','purchases.vat_amount')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('invoice_number', '=', $id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->distinct()
                    ->get();

        return $purchases;

    }

    
    public function savePurchasePayment(Request $request)
    {
        
        $request->validate([
            'paid_amount' => 'required',
         //   'invoice_number' => 'required'
        ]);
    
        $find_purchases = DB::table('purchases')
                         ->where('invoice_number', $request->invoice_number)->select('paid_amount')
                         ->first();
    
        $update = DB::table('purchases')
                 ->where('invoice_number', $request->invoice_number)
                 ->update(['paid_amount' => $find_purchases->paid_amount + $request->paid_amount]);
    if($update != '') {
            $purchases = Payment::create([
                'supplier_id' => $request->supplier_id,
                'branch_id' => Auth::user()->branch_id,
                'invoice_number' => $request->invoice_number,
                'amount' => $request->total,
                'paid_amount' => $request->paid_amount,
                'created_date' => $request->created_date
            ]);
        }
         return redirect()->back()->with('message', 'Supplier Payment added Successful!');
    }


    public function getAnnualPurchases() {

        $years = DB::table('years')
                ->select('*')
                ->orderby('id', 'asc')
                ->get();

        return view('pages.annual-purchases-form', compact('years'));
    }


    public function createLPO($invoice, $invoice_date) {
      
        $purchases = DB::table('purchases')
                   ->select("purchases.supplier_id", "suppliers.id", "suppliers.supplier_name", "purchases.invoice_number")
                   ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                   ->where('invoice_number', $invoice)
                   ->where('created_date', $invoice_date)
                   ->distinct()
                   ->get();
       
        return view('pages.create-lpo', compact('purchases', 'invoice_date'));
    }


    public function addLPO(Request $request)
    {
        
        $request->validate([
            'lpo_number' => 'required',
        ]);

         $fileName = time().'.'.$request->lpo_file;  
        //  $request->lpo_file->move(public_path('/attachments/'), $fileName);
         
        $find_purchases = Purchase::where('invoice_number', $request->invoice_number)->where('created_date', $request->invoice_date)->first();
    
       if($find_purchases){
        $update = DB::table('purchases')
        ->where('invoice_number', $request->invoice_number)
        ->where('created_date', $request->invoice_date)
        ->update(['lpo_number' => $request['lpo_number'], 'lpo_file' => $fileName]);
       }
        
         return redirect()->back()->with('message', 'LPO added Successful!');
    }


    public function getAnnualPurchasesReportPdf(Request $request) {

        $years = Year::find($request->year);
        $settings= DB::table('general_settings')->select('business_name', 'type', 'address', 'logo_file')->get();


         $total_inventory_purchases_value = DB::table('purchases')->select(DB::raw('SUM(purchases.total_purchase) as total_purchase_amount'), 
                                     DB::raw('SUM(purchases.total_purchase * purchases.vat_amount) as vat_amount'))
                                     ->whereBetween('purchases.created_date', [$years['first_date'], $years['second_date']])
                                     ->get();

         $purchases = DB::table('purchases')
                            ->select([DB::raw("SUM(purchases.total_purchase) as total"), DB::raw("SUM(purchases.total_purchase * purchases.vat_amount) as calculated_vat_amount"),  "purchases.lpo_number", "purchases.lpo_file", 'purchases.invoice_number', 'invoice_file', 
                            'created_date','purchases.supplier_id', 'purchases.vat_amount', 'suppliers.supplier_name'])
                            ->groupBy(DB::raw('purchases.invoice_number'), 'supplier_id', 'created_date', 'purchases.vat_amount',"purchases.lpo_number", "purchases.lpo_file", 'invoice_file', 'suppliers.supplier_name')
                            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                            ->whereBetween('purchases.created_date', [$years['first_date'], $years['second_date']])
                            ->orderBy('created_date', 'asc')
                            ->get();
                 

        $startdate= $years->current_year;
        $enddate= $years->previous_year;

        $total_purchases =  DB::table('purchases')
                            ->select([DB::raw("SUM(purchases.total_purchase) as total"), 
                            DB::raw("SUM(purchases.total_purchase * purchases.vat_amount) as vat_amount")])
                            ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                            ->whereBetween('purchases.created_date', [$years['first_date'], $years['second_date']])
                            ->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.annual-purchases-report-pdf', compact('purchases', 'settings',  'startdate', 'enddate', 'total_purchases', 'total_inventory_purchases_value'));
        return $pdf->stream();

    }
    

    public function editPurchaseDetailedValues($invoice_id)
    {
            
        $purchases = DB::table('purchases')
                    ->select('purchases.vat_amount', 'purchases.invoice_number', 'purchases.vat_type', 'purchases.created_date')
                    ->where('invoice_number', '=', $invoice_id)
                    ->get();
         
        return $purchases;

    } 


    public function getPurchaseDetailsPDF(Request $request)
    {
        $items = Item::all();
        
        $id = $request->route('id');
        
        $settings= DB::table('general_settings')->select('business_name', 'type', 'address', 'logo_file')->get();

        $check_vat = Purchase::where('invoice_number', $id)
                               ->select('vat_type')
                               ->where('vat_type', 1)
                               ->where('branch_id', Auth::user()->branch_id)
                               ->get();

        $purchases = DB::table('purchases')
                    ->select('purchases.id as id', 'purchases.product_id', 'purchases.make', 'purchases.part_number', 'purchases.vat_type', 'purchases.model', 'purchases.purchase_price', 'purchases.quantity', 'purchases.paid_amount', 'purchases.total_purchase', 'items.item_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('invoice_number', '=', $id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->get();

    $total_purchases = DB::table('purchases')->where('invoice_number', $id)
    ->where('purchases.branch_id', Auth::user()->branch_id)
    ->sum('total_purchase');
      
    if(count($check_vat) > 0) {
        $vat_calculations = (18/100) * $total_purchases;
       } else {
        $vat_calculations = '0.00';
       }

       $supplier_name = DB::table('purchases')
                    ->select('suppliers.supplier_name')
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->join('items', 'items.id', 'purchases.product_id')
                    ->where('invoice_number', '=', $id)
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->distinct()
                    ->get();

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('pages.purchase-details-pdf', compact('purchases', 'id', 'total_purchases', 'vat_calculations', 'items', 'supplier_name', 'settings'));
            return $pdf->stream();

    }


    public function getSupplierDeptors() {

        $years = Year::all();

        return view('reports.supplier-deptors', compact('years'));
    }

    public function getSupplierDeptorReport(Request $request) {

        $date_time = Year::where('id', $request->year)->select('*')->first();
        $settings= DB::table('general_settings')->select('business_name', 'type', 'address', 'logo_file')->get();


        $total_owed_amount = Purchase::whereBetween('purchases.created_date', [$date_time['first_date'], $date_time['second_date']])
                            ->sum('purchases.paid_amount');

        $total_paid_amount = Purchase::whereBetween('purchases.created_date', [$date_time['first_date'], $date_time['second_date']])
                            ->sum('purchases.paid_amount');

        $total_purchases_amount = DB::table('suppliers')
                                ->select([DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_purchases")])
                                ->join('purchases', 'suppliers.id', 'purchases.supplier_id')
                                ->whereBetween('purchases.created_date', [$date_time['first_date'], $date_time['second_date']])
                                ->get();


        $suppliers = DB::table('suppliers')
                    ->select([DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_purchases"), DB::raw("SUM(purchases.paid_amount) as paid_amount"), 'suppliers.supplier_name as supplier_name'])
                    ->join('purchases', 'suppliers.id', 'purchases.supplier_id')
                    ->groupBy('supplier_name' )
                    ->whereBetween('purchases.created_date', [$date_time['first_date'], $date_time['second_date']])
                    ->orderBy('suppliers.supplier_name','asc')
                    ->distinct()
                    ->get();
        
        $current_year= $date_time->current_year;
        $previous_year= $date_time->previous_year;
       
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('reports.supplier-deptor-report-pdf', compact('suppliers',  'current_year', 'previous_year', 'total_purchases_amount', 'settings', 'total_owed_amount', 'total_paid_amount'));
        return $pdf->stream();
    }

    public function getCreditorsReportFilter() {
        $years = Year::all();
        $suppliers = Supplier::all();

        return view('reports.creditor-report-filter', compact('years', 'suppliers'));
    }


    public function getCreditorsReportPDF (Request $request) {

        $date_time = Year::where('id', $request->year)->select('*')->first();
        
         $settings= DB::table('general_settings')->select('business_name', 'type', 'address', 'logo_file')->get();

     if($request->year == '') {
        $purchases = DB::table('purchases')
                    ->select([DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_purchases"), 'purchases.paid_amount', 'purchases.lpo_number', 'purchases.invoice_number'])
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->groupBy('lpo_number' , 'invoice_number', 'purchases.paid_amount')
                    ->where('purchases.supplier_id', $request->supplier_id)
                    ->orderBy('suppliers.supplier_name','asc')
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->distinct()
                    ->get();
                
        $total_paid_amount = Purchase::where('purchases.supplier_id', $request->supplier_id)
                        ->where('purchases.branch_id', Auth::user()->branch_id)
                        ->sum('purchases.paid_amount');

        $total_vat_amount = Purchase::where('supplier_id', $request->supplier_id)
                        ->where('purchases.branch_id', Auth::user()->branch_id)
                        ->sum('vat_amount');

       $total_purchases_amount = DB::table('suppliers')
                           ->select([DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_purchases")])
                           ->join('purchases', 'suppliers.id', 'purchases.supplier_id')
                           ->where('purchases.supplier_id', $request->supplier_id)
                           ->where('purchases.branch_id', Auth::user()->branch_id)
                           ->get();
        } else {
            $purchases = DB::table('purchases')
                    ->select([DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_purchases"), 'purchases.branch_id', 'purchases.paid_amount', 'purchases.lpo_number', 'purchases.invoice_number'])
                    ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                    ->groupBy('lpo_number' , 'invoice_number', 'purchases.paid_amount', 'purchases.branch_id')
                    ->whereBetween('purchases.created_date', [$date_time['first_date'], $date_time['second_date']])
                    ->where('purchases.supplier_id', $request->supplier_id)
                    ->orderBy('suppliers.supplier_name','asc')
                    ->where('purchases.branch_id', Auth::user()->branch_id)
                    ->get();

                    
        $total_paid_amount = DB::table('payments')->where('supplier_id', $request->supplier_id)
                                ->whereBetween('payments.created_date', [$date_time['first_date'], $date_time['second_date']])
                                ->where('payments.branch_id', Auth::user()->branch_id)
                                ->sum('paid_amount');

        $total_vat_amount = Purchase::where('supplier_id', $request->supplier_id)
                            ->whereBetween('purchases.created_date', [$date_time['first_date'], $date_time['second_date']])
                            ->where('purchases.branch_id', Auth::user()->branch_id)
                            ->sum('vat_amount');

        $total_purchases_amount = Purchase::where('supplier_id', $request->supplier_id)
                                ->whereBetween('purchases.created_date', [$date_time['first_date'], $date_time['second_date']])
                                ->where('purchases.branch_id', Auth::user()->branch_id)
                                ->sum('total_purchase');
                                
        }
        
        if($request->year == '') {
            $current_year = "YEAR";
            $previous_year = "ALL"; 
        } else {
        $current_year= $date_time->current_year;
        $previous_year= $date_time->previous_year;
        }

        $supplier_name = Supplier::where('id', $request->supplier_id)->select('supplier_name')->first();
       
        // return $total_paid_amount;
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('reports.creditors-report-filter-pdf', compact('purchases',  'current_year', 'previous_year', 'total_vat_amount', 'total_purchases_amount', 'total_paid_amount', 'supplier_name', 'settings'));
       return $pdf->stream();
    }

    public function getPurchasePayments($id){

        $payments = Payment::find($id);

        return $payments;

    }


    public function updatePurchasePayments(Request $request) {

        $payment = Payment::find($request->id);

        $payment->paid_amount = $request->paid_amount;
        $payment->save();


        $payments = DB::table('purchases')
                    ->where('id', $request->id)
                    ->update(["paid_amount" => ($payment->paid_amount + $request->paid_amount)]);


        return redirect('suppliers/payments/'.$payment->invoice_number)->with('message', 'Payment Updated successful');

    }

    public function deletePurchasePayments($id){

        $purchase = Payment::findOrFail($id);
    
        $purchase->delete();
    
        if(!$purchase) {
            return redirect()->back()->with('error', ' Item Not deleted');
        } else {
            return redirect()->back()->with('error', 'Payment deleted successful');
        }
    
    }

}
