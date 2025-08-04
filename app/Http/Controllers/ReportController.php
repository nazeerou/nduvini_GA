<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Year;
use App\Models\ClientPayment;
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
use App\Models\Invoice;
use App\Models\AccountHistory;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Account;
use App\Models\PettyCashCategory;

class ReportController extends Controller
{
 
    public function getLabourChargeReport() {

        $years = Year::all();

        $product_id = Item::where('item_name', 'LABOUR CHARGE')->select('id')->first();

        $labour_charge_id = Product::where('product_id', $product_id['id'])->select('pid')->first();

        return view('reports.labour-charge-form', compact('years', 'labour_charge_id'));
    }


    public function labourChargeReportDetails(Request $request) {
       
        $month = $request->month;
        $labour_charge_id = $request->labour_charge_id;
        $date_time = Year::where('id', $request->year)->select('*')->first();

     if($request->year == '') {
            $clients = DB::table('sales')
            ->select([DB::raw("SUM((sales.total_sales * sales.vat_amount) + sales.total_sales) as total_sales"), 'clients.client_name as client_name', 'sales.bill_no', 'sales.created_date'])
            ->join('clients', 'clients.id', 'sales.client_name')
            ->groupBy('client_name', 'bill_no', 'created_date')
            ->orderBy('clients.client_name','asc')
            ->distinct()
            ->get();

        $total_sales_amount =  DB::table('clients')
                            ->select([DB::raw("SUM((sales.total_sales * sales.vat_amount) + sales.total_sales) as total_sales")])
                            ->join('sales', 'clients.id', 'sales.client_name')
                            ->get();
         }
        else {            
         $clients = DB::table('sales')
                    ->select([DB::raw("SUM((sales.qty * sales.selling_price)) as total_sales"), 'clients.client_name as client_name', 'sales.bill_no', 'sales.created_date'])
                    ->join('clients', 'clients.id', 'sales.client_name')
                    ->groupBy('client_name', 'bill_no', 'created_date')
                    ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                    ->whereMonth('sales.created_date', $request->month)
                    ->where('product_id', $labour_charge_id)
                    ->orderBy('clients.client_name','asc')
                    ->distinct()
                    ->get();


     $total_sales_amount =  DB::table('sales')
                        ->select([DB::raw("SUM((sales.total_sales * sales.vat_amount) + sales.total_sales) as total_sales")])
                        ->join('clients', 'clients.id', 'sales.client_name')
                        ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                        ->whereMonth('sales.created_date', $request->month)
                        ->where('product_id', $labour_charge_id)
                       ->get();
        }
        
        if($date_time) {
        $current_year= $date_time->current_year;
        $previous_year= $date_time->previous_year;
       } else {
       $current_year = 'YEAR';
       $previous_year = "ALL";
    }

       
    // echo $request->labour_charge_id."MONTH".$request->month."YEAR".$request->year;
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('reports.labour-charge-report-details-pdf', compact('clients',  'current_year', 'previous_year', 'total_sales_amount'));
        return $pdf->stream();

    }

    public function getClientStatement() {
        $clients = Client::where('branch_id', Auth::user()->branch_id)->get();
        return view('clients.client_statement', compact('clients'));
    }

 
public function getClientStatementPDF(Request $request) {
    $paymentStatus = $request->input('payment'); // Assuming payment type can be 'all', 'credit', or 'debit'
    $startDate = $request->input('startdate');
    $endDate = $request->input('enddate');


    $saless = DB::table('invoices')
    ->select([
        DB::raw("SUM(client_payments.paid_amount) as paid_amount"),
        'invoices.vehicle_reg',
        'invoices.invoice_number',
        'invoices.bill_amount',
        'clients.client_name',
        'invoices.created_date',
    ])
    ->leftJoin('client_payments', 'invoices.invoice_number', '=', 'client_payments.bill_no')
    ->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')
    ->leftJoin('estimations', 'estimations.reference', 'invoices.estimate_ref')
    ->where('invoices.branch_id', Auth::user()->branch_id)
    ->where('invoices.client_id', $request->client_id)
    ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
        return $query->whereBetween('invoices.created_date', [$startDate, $endDate]);
    })
    ->groupBy(
        'invoices.invoice_number',
        'invoices.vehicle_reg',
        'invoices.bill_amount',
        'clients.client_name',
        'invoices.created_date',
    
    )
    ->when($paymentStatus === '1', function ($query) {
        return $query->havingRaw('SUM(client_payments.paid_amount) > 0');
    })
    ->when($paymentStatus === '2', function ($query) {
        return $query->havingRaw('SUM(client_payments.paid_amount) IS NULL OR SUM(client_payments.paid_amount) = 0');
    })
    ->orderBy('invoices.invoice_number', 'desc')
    ->get();


    $query = DB::table('invoices')
    ->select([
        'client_payments.paid_amount',
        'invoices.account',
        'invoices.vehicle_reg',
        'invoices.invoice_number',
        'invoices.bill_amount',
        'clients.client_name',
        'clients.place',
        'invoices.created_date'
    ])
    ->leftjoin('client_payments', 'invoices.invoice_number', '=', 'client_payments.bill_no')
    ->leftjoin('clients', 'clients.id', '=', 'invoices.client_id')
    ->groupBy(
        'invoices.bill_amount',
        'invoices.vehicle_reg',
        'invoices.account',
        'invoices.invoice_number',
        'client_payments.bill_amount',
        'clients.client_name',
        'clients.place',
        'client_payments.paid_amount',
        'invoices.created_date'
    )
    ->where('invoices.branch_id', Auth::user()->branch_id)
    ->where('invoices.client_id', $request->client_id);

if ($paymentStatus == '1') {
    // For Paid invoices (paid_amount > 0)
    $query->where('client_payments.paid_amount', '>', 0);
} elseif ($paymentStatus == '2') {
    // For Unpaid invoices (paid_amount is NULL or 0)
    $query->where(function ($query) {
        $query->whereNull('client_payments.paid_amount')
              ->orWhere('client_payments.paid_amount', '=', 0);
    });
} elseif ($paymentStatus == '0') {
    // To show both Paid and Unpaid (when paid_amount is NULL or > 0)
    // This part handles showing both paid and unpaid invoices
    $query->where(function ($query) {
        $query->whereNull('client_payments.paid_amount')
              ->orWhere('client_payments.paid_amount', '>', 0);
    });
}

if (!empty($startDate)) {
    $query->whereDate('invoices.created_date', '>=', $startDate);
}

if (!empty($endDate)) {
    $query->whereDate('invoices.created_date', '<=', $endDate);
}

$sales = $query->orderBy('invoices.created_date', 'desc')->get();



    $total_credit = ClientPayment::
        where('client_id', $request->client_id)
        ->where('branch_id', Auth::user()->branch_id)
        ->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->whereDate('created_date', '>=', $startDate);
        })
        ->when(!empty($endDate), function ($query) use ($endDate) {
            return $query->whereDate('created_date', '<=', $endDate);
        })
        ->sum('paid_amount');

    $total_charges = Invoice::
        where('branch_id', Auth::user()->branch_id)
        ->where('client_id', $request->client_id)
        ->when(!empty($startDate), function ($query) use ($startDate) {
            return $query->whereDate('created_date', '>=', $startDate);
        })
        ->when(!empty($endDate), function ($query) use ($endDate) {
            return $query->whereDate('created_date', '<=', $endDate);
        })
        ->sum('bill_amount');

    $settings = DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();

    $clients = DB::table('clients')->select('client_name', 'address', 'place', 'vrn', 'tin')
        ->where('id', $request->client_id)
        ->where('branch_id', Auth::user()->branch_id)
        ->get();
    $payment = $paymentStatus;
   $startdate = $startDate;
   $enddate = $endDate;
    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadView('clients.client_statement_pdf',
 compact('settings', 'payment', 'clients', 'total_credit', 'total_charges', 'sales', 'startdate', 'enddate'));
    return $pdf->stream();
}



        public function getPettyCashReports () {
            $clients = Supplier::all();
            $categories = DB::table('petty_cash_categories')
                        ->select('*')
                        ->orderBy('name', 'asc')
                        ->get();

            return view('clients.petty_cash_reports', compact('clients', 'categories'));
        }
    

      public function getPettyCashReportsPDF(Request $request) {
                 
            $startdate= $request->startdate;
            $enddate= $request->enddate;
            $expense_category = $request->expense_category_id;

            $petty = PettyCashCategory::where('id', $request->expense_category_id)->select('name')->first();

            $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();

            if($expense_category == NULL) {
                if($expense_category == NULL) {
                    $petty_cash_query = DB::table('expenses')
                        ->select(DB::raw("SUM(expenses.amount) as amount"), 'expenses.reference_no', 'expenses.paid_to', 'expenses.user_id', 'expenses.note', 'expenses.voucher_no', 'expenses.account_id', 'users.fname', 'users.lname', 
                            DB::raw("COALESCE(expenses.created_date, '0000-00-00') as created_date")) // Handle NULL created_date as empty
                        ->leftJoin('users', 'users.id', 'expenses.user_id')
                        ->where('expenses.branch_id', Auth::user()->branch_id);
                
                    // Add whereBetween only if both startdate and enddate are provided
                    if (!empty($request->startdate) && !empty($request->enddate)) {
                        $petty_cash_query->whereBetween('expenses.created_date', [$request->startdate, $request->enddate]);
                    }
                
                    $petty_cash = $petty_cash_query
                        ->groupBy('expenses.paid_to', 'expenses.user_id', 'expenses.reference_no', 'expenses.note', 'expenses.voucher_no', 'expenses.account_id', 'users.fname', 'users.lname', 'expenses.created_date')
                        ->orderBy('expenses.created_date', 'asc')
                        ->get();
                
                    // Total amounts
                    $total_amounts_query = DB::table('expenses')
                        ->select(DB::raw("SUM(amount) as total_amount"));
                
                    // Apply date filter only if startdate and enddate are provided
                    if (!empty($request->startdate) && !empty($request->enddate)) {
                        $total_amounts_query->whereBetween('created_date', [$request->startdate, $request->enddate]);
                    }
                
                    $total_amounts = $total_amounts_query->get();
                }
                
            }   else {     
                $petty_cash_query = DB::table('expenses')
                               ->select(DB::raw("SUM(expenses.amount) as amount"), 'expenses.reference_no', 'expenses.paid_to', 'expenses.user_id', 'expenses.note', 'expenses.voucher_no', 'expenses.account_id', 'users.fname', 'users.lname', 
                                   DB::raw("COALESCE(expenses.created_date, '0000-00-00') as created_date")) // Handle NULL created_date as empty
                               ->leftJoin('users', 'users.id', 'expenses.user_id')
                               ->where('expenses.branch_id', Auth::user()->branch_id)
                                ->where('expense_category_id', $expense_category);

                           // Add whereBetween only if both startdate and enddate are provided
                           if (!empty($request->startdate) && !empty($request->enddate)) {
                               $petty_cash_query->whereBetween('expenses.created_date', [$request->startdate, $request->enddate]);
                           }
                       
                           $petty_cash = $petty_cash_query
                               ->groupBy('expenses.paid_to', 'expenses.user_id', 'expenses.reference_no', 'expenses.note', 'expenses.voucher_no', 'expenses.account_id', 'users.fname', 'users.lname', 'expenses.created_date')
                               ->orderBy('expenses.created_date', 'asc')
                               ->get();
                       
                           // Total amounts
                           $total_amounts_query = DB::table('expenses')
                               ->select(DB::raw("SUM(amount) as total_amount"))
                               ->where('expense_category_id', $expense_category);
                
                       
                           // Apply date filter only if startdate and enddate are provided
                           if (!empty($request->startdate) && !empty($request->enddate)) {
                               $total_amounts_query->whereBetween('created_date', [$request->startdate, $request->enddate]);
                           }
                       
                           $total_amounts = $total_amounts_query->get();
            }

                       
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('clients.petty_cash_report_pdf', 
        compact('startdate', 'enddate', 'petty_cash', 'settings', 'total_amounts', 'petty'));
        return $pdf->stream();

        }


      public function getSupplierStatement() {
            $clients = Supplier::all();
            return view('clients.supplier_statement', compact('clients'));
        }
    
        public function getSupplierStatementPDF(Request $request)
        {
            $startdate = $request->startdate;
            $enddate = $request->enddate;
            $payment = $request->payment;
            $supplierId = $request->supplier_id;
            $branchId = Auth::user()->branch_id;
        
            // Base purchase query
            $purchases = DB::table('purchases')
            ->select(
                DB::raw("((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_amount"),
                'purchases.total_purchase',
                'purchases.vat_amount',
                'purchases.invoice_number',
                'purchases.lpo_number',
                'purchases.paid_amount',
                'suppliers.supplier_name',
                'suppliers.address',
                'suppliers.place',
                'purchases.created_date'
            )
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->where('purchases.branch_id', Auth::user()->branch_id)
            ->where('purchases.supplier_id', $request->supplier_id)
            ->orderBy('purchases.created_date', 'desc')
            ->groupBy(
                'purchases.invoice_number',
                'purchases.paid_amount',
                'suppliers.address',
                'suppliers.place',
                'purchases.lpo_number',
                'suppliers.supplier_name',
                'purchases.created_date',
                'purchases.total_purchase',
                'purchases.vat_amount'
            )
            ->get();
        
        
            // Total charges (VAT inclusive)
            $totalChargesQuery = DB::table('purchases')
                ->select(DB::raw("SUM((total_purchase * vat_amount) + total_purchase) as total_amount"))
                ->where('supplier_id', $supplierId);
        
            if (!empty($startdate) && !empty($enddate)) {
                $totalChargesQuery->whereBetween('created_date', [$startdate, $enddate]);
            }
        
            $total_charges = $totalChargesQuery->value('total_amount') ?? 0;
        
            // return $total_charges;
            
            // // Total credit (payments)
            // $totalCreditQuery = DB::table('payments')
            //     ->select(DB::raw("SUM(paid_amount) as paid_amount"))
            //     ->where('supplier_id', $supplierId);
        
            // if (!empty($startdate) && !empty($enddate)) {
            //     $totalCreditQuery->whereBetween('created_date', [$startdate, $enddate]);
            // }
        
            // $total_credit = $totalCreditQuery->value('paid_amount') ?? 0;
        
// $total_credit = DB::table('payments')
//     ->where('supplier_id', $request->supplier_id)
//     ->whereBetween('created_date', [$startdate, $enddate])
//     ->sum('paid_amount');

// $total_charges = DB::table('purchases')
//     ->where('supplier_id', $request->supplier_id)
//     ->whereBetween('created_date', [$startdate, $enddate])
//     ->sum(DB::raw("(total_purchase * vat_amount) + total_purchase"));

$query = DB::table('payments')->where('supplier_id', $request->supplier_id);

if (!empty($startdate) && !empty($enddate)) {
    $query->whereBetween('created_date', [$startdate, $enddate]);
}

$total_credit = $query->sum('paid_amount');


// For purchases

$query2 = DB::table('purchases')->where('supplier_id', $request->supplier_id);

if (!empty($startdate) && !empty($enddate)) {
    $query2->whereBetween('created_date', [$startdate, $enddate]);
}

$total_charges = $query2->sum(DB::raw("(total_purchase * vat_amount) + total_purchase"));

            // Generate PDF
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('clients.supplier_statement_pdf', compact(
                'startdate',
                'enddate',
                'payment',
                'purchases',
                'total_credit',
                'total_charges'
            ));
        
            return $pdf->stream();
        }
        
        
    //     public function getSupplierStatementPDF(Request $request) {
                 
    //         $startdate= $request->startdate;
    //         $enddate= $request->enddate;
    //         $payment = $request->payment;
    //         $purchases = DB::table('purchases')
    //                 ->select(DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_amount"), 'purchases.invoice_number', 'purchases.lpo_number', 'purchases.paid_amount', 'suppliers.supplier_name', 'suppliers.address', 'suppliers.place', 'purchases.created_date')
    //                 ->leftjoin('suppliers', 'suppliers.id', 'purchases.supplier_id')
    //                 ->where('purchases.branch_id', Auth::user()->branch_id)
    //                 ->whereBetween('purchases.created_date', [$request->startdate, $request->enddate])
    //                 ->where('purchases.supplier_id', $request->supplier_id)
    //                 ->groupBy('purchases.invoice_number', 'purchases.paid_amount', 'suppliers.address', 'suppliers.place', 'purchases.lpo_number', 'suppliers.supplier_name', 'purchases.created_date')
    //                 ->orderBy('purchases.created_date', 'desc')
    //                 ->distinct()
    //                 ->get();

    //                 $total_charges = DB::table('purchases')
    //                            ->select(DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_amount"))
    //                            ->where('supplier_id', $request->supplier_id)
    //                            ->whereBetween('created_date', [$request->startdate, $request->enddate])
    //                            ->get();      
                       
    //                 $total_credit = DB::table('payments')
    //                          ->select(DB::raw("SUM(paid_amount) as paid_amount"))
    //                          ->where('supplier_id', $request->supplier_id)
    //                          ->whereBetween('created_date', [$request->startdate, $request->enddate])
    //                          ->get();

    
    //  if ($startdate  == '' && $enddate == '') {

    // $purchases = DB::table('purchases')
    //             ->select(DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_amount"), 'purchases.invoice_number', 'purchases.lpo_number', 'purchases.paid_amount', 'suppliers.supplier_name', 'suppliers.address', 'suppliers.place', 'purchases.created_date')
    //             ->leftjoin('suppliers', 'suppliers.id', 'purchases.supplier_id')
    //             ->where('purchases.branch_id', Auth::user()->branch_id)
    //             ->where('purchases.supplier_id', $request->supplier_id)
    //             ->groupBy('purchases.invoice_number', 'purchases.paid_amount', 'suppliers.address', 'suppliers.place', 'purchases.lpo_number', 'suppliers.supplier_name', 'purchases.created_date')
    //             ->orderBy('purchases.created_date', 'desc')
    //             ->distinct()
    //             ->get();

    //             $total_charges = DB::table('purchases')
    //                        ->select(DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_amount"))
    //                        ->where('supplier_id', $request->supplier_id)
    //                        ->get();      
                   
    //             $total_credit = DB::table('payments')
    //                      ->select(DB::raw("SUM(paid_amount) as paid_amount"))
    //                      ->where('supplier_id', $request->supplier_id)
    //                      ->get();

    //     }   // End Condition
        
    //     $pdf = App::make('dompdf.wrapper');
    //     $pdf->loadView('clients.supplier_statement_pdf', compact('startdate', 'enddate', 'payment', 'purchases', 'total_credit', 'total_charges'));
    //     return $pdf->stream();

    //     }
    
          public function getDeptorStatement() {
            $clients = Client::where('branch_id', Auth::user()->branch_id)->get();
            return view('accounts.deptor_statement', compact('clients'));
        }
    
        public function getDeptorStatementPDF(Request $request) {
    
            // return $request;
            $startdate= $request->startdate;
            $enddate= $request->enddate;
    
            $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
            $clients = DB::table('clients')->select('client_name', 'address', 'place')
                      ->where('id', $request->client_id)
                      ->where('branch_id', Auth::user()->branch_id)
                      ->get();
                      
        $sales = DB::table('invoices')
            ->select([DB::raw("SUM(client_payments.paid_amount) as paid_amount"), 'estimations.account_no', 'estimations.account_prefix', 'estimations.make', 'estimations.model', 'job_cards.vehicle_reg', 'invoices.invoice_number', 'invoices.bill_amount', 'clients.client_name', 'clients.place', 'invoices.created_date'])
            ->leftjoin('client_payments', 'invoices.invoice_number', '=', 'client_payments.bill_no')
            ->leftjoin('job_cards', 'job_cards.job_card_no', '=', 'invoices.job_card_no')
            ->join('estimations', 'estimations.client_name', '=', 'client_payments.client_id')
            ->leftjoin('clients', 'clients.id', '=', 'job_cards.client_id')
            ->groupBy('invoices.bill_amount', 'job_cards.vehicle_reg', 'estimations.account_no', 'estimations.account_prefix', 'estimations.make', 'estimations.model', 'invoices.invoice_number', 'client_payments.bill_amount', 'clients.client_name',  'clients.place', 'invoices.created_date')
            ->where('invoices.branch_id', Auth::user()->branch_id)
            ->where('client_payments.client_id', $request->client_id)
            ->whereBetween('invoices.created_date', [$request->startdate, $request->enddate])
            ->orderBy('invoices.created_date', 'desc')
            ->get();

            $pdf = App::make('dompdf.wrapper');
                $pdf->loadView('accounts.deptors_statement_pdf', compact('settings', 'clients', 'sales', 'startdate', 'enddate'));
                return $pdf->stream();
              }


          public function getClientInfo($id) {

          $sales = DB::table('estimations')
                ->select(['vehicle_reg', 'make', 'model', 'created_date', 'client_name'])
                ->where('branch_id', Auth::user()->branch_id)
                ->where('client_name', $id)
                ->groupBy('vehicle_reg', 'make', 'model', 'created_date', 'client_name')
                ->orderBy('created_date', 'desc')
                ->get();

                return $sales;

  }

  public function getVehicleInfo($id) {

    $sales = DB::table('estimations')
                ->select(['estimations.vehicle_reg', 'estimations.make', 'estimations.model', 'clients.client_name', 'estimations.created_date', 'clients.id', 'estimations.chassis', 'estimations.registration_year', 'estimations.milleage'])
                ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                ->where('estimations.branch_id', Auth::user()->branch_id)
                ->where('estimations.vehicle_reg', $id)
                ->groupBy('estimations.vehicle_reg', 'estimations.make', 'clients.client_name', 'estimations.model', 'estimations.created_date', 'clients.id', 'estimations.chassis', 'estimations.registration_year', 'estimations.milleage')
                ->orderBy('created_date', 'desc')
                ->get();

                return $sales;

  }

  public function getClientInfo1($id) {
    $output = '';

    $adjustments = DB::table('estimations')
                ->select(['vehicle_reg', 'make', 'model', 'created_date', 'client_name'])
                ->where('branch_id', Auth::user()->branch_id)
                ->where('client_name', $id)
                ->groupBy('vehicle_reg', 'make', 'model', 'created_date', 'client_name')
                ->orderBy('created_date', 'desc')
                ->get();


                if($adjustments)
                {
                foreach ($adjustments as $key => $adjust)
                {
                $output.='<tr>'.
                '<td>'.++$key.'</td>'.
                '<td>'.$adjust->vehicle_reg.'</td>'.
                '<td>'.$adjust->make.' '.'|'.$adjust->make.'</td>'.
                '<td>'.$adjust->created_date.'</td>'.
                '</tr>';
                }
                return Response($output);
                }

    }


    public function editAccount($id) {
      
            $accounts = DB::table('accounts')
              ->select('*')
              ->where('id', $id)
              ->get();
            
            return $accounts;
    }

    public function addAccount(Request $data){
        // $data->validate([        
        // ]);
      $accounts = Account::create([
          'name' => $data['account_name'], 
          'branch_id' => Auth::user()->branch_id, 
          'account_no' => $data['account_no'], 
          'initial_balance'=> $data['initial_balance'],
          'total_balance'=> $data['initial_balance'],
          'note'=> $data['note'],
          'is_active' => 0
        ]);


        return redirect()->back()->with('message', 'Account Details saved successful');

    }
    
    public function addCashAccount(Request $request) {

        $accounts = DB::table('accounts')
          ->where('id', $request->id)
          ->where('branch_id', Auth::user()->branch_id)
          ->update(["total_balance" => $request->total_balance + $request->new_amount]);
        
            $accounts_ = AccountHistory::create([
                'amount' => $request->new_amount, 
                'branch_id' => Auth::user()->branch_id, 
                'account_id' => $request->id, 
                'created_date'=> $request->created_date,
              ]);

          return redirect()->back()->with('message', 'Amount Added successful');

     }

    public function getAccountHistories($id) {

        $accounts = DB::table('account_histories')
                  ->select('account_histories.amount', 'account_histories.id', 'account_histories.created_date', 'accounts.account_no', 'accounts.name')
                  ->leftjoin('accounts', 'accounts.id', 'account_histories.account_id')
                  ->where('account_histories.account_id', $id)
                  ->where('account_histories.branch_id', Auth::user()->branch_id)
                  ->get();

        return view('account.account_histories', compact('accounts'));

    }

    public function updateAccount(Request $request) {
              
        $accounts = DB::table('accounts')
          ->where('id', $request->id)
          ->where('branch_id', Auth::user()->branch_id)
          ->update(["initial_balance" => $request->initial_balance, "total_balance" => $request->total_balance,
          "name" => $request->account_name, "account_no" => $request->account_no, "note" => $request->note ]);
        
          return redirect()->back()->with('message', 'Account Details updated successful');

     }
}
