<?php

namespace App\Http\Controllers;
use App\Models\StockDeduction;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Adjustment;
use App\Models\Sale;
use App\Models\Year;
use DB;
use PdfReport;
use PDF;
use Barryvdh\Snappy;
use App;
use Illuminate\Http\Request;
use Auth;
use App\Models\PriceList;

class StockController extends Controller
{
  
    public function currentStock() {

        $adjustments = Adjustment::all();

        $stocks = DB::table('products')
                  ->select('products.pid as id', 'items.item_name', 'products.part_number', 'products.purchase_unit', 'brands.title', 'products.quantity', 'products.model')
                  ->join('items', 'items.id', 'products.product_id')
                  ->join('brands', 'brands.id', 'products.brand_id')
                  ->where('products.branch_id', Auth::user()->branch_id)
                  ->get();

        return view('pages.current-stocks', compact('stocks', 'adjustments'));
    }

  public function getStockDeductions() {

    $products = DB::table('stock_deductions')
               ->select('stock_deductions.id', 'items.item_name', 'stock_deductions.reasons', 'stock_deductions.created_date')
               ->leftjoin('products', 'products.pid', 'stock_deductions.product_id')
               ->leftjoin('items', 'items.id', 'products.product_id')
               ->get();

    return view('pages.stock_deductions', compact('products'));

  }

    public function getCurrentStockPDF() {

      $branches = DB::table('branches')->where('id', Auth::user()->branch_id)->get();
      $stocks = DB::table('products')
                ->select('products.pid as id', 'items.item_name', 'products.part_number', 'products.purchase_unit', 'brands.title', 'products.quantity', 'products.model')
                ->join('items', 'items.id', 'products.product_id')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->where('products.branch_id', Auth::user()->branch_id)
                ->get();

                $pdf = App::make('dompdf.wrapper');
                $pdf->loadView('pages.current-stock-pdf', compact('stocks', 'branches'));
                return $pdf->stream();
  }


    public function saveClientPrice(Request $request) {

      $price_sheet = PriceList::create([
        'prod_id' => $request->product_id, 
        'branch_id' => Auth::user()->branch_id, 
        'client_id' => $request->client_id, 
        'sale_price'=> $request->selling_price,
        'status' => 1,
      ]);

      return redirect()->back()->with('message', 'Price added Successful!');

    }

    public function priceList() {
      $clients = DB::table('clients')
              ->select('clients.client_name', 'clients.id')
              ->where('clients.branch_id', Auth::user()->branch_id)
              ->get();

        $stocks = DB::table('price_lists')
                ->select('products.pid', 'items.item_name', 'price_lists.id', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
                ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
                ->join('items', 'items.id', 'products.product_id')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
                ->where('products.branch_id', Auth::user()->branch_id)
                ->where('price_lists.client_id', '0')
                ->groupBy('products.pid', 'price_lists.id', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'products.selling_price', 'brands.title', 'products.quantity', 'products.model')
                ->orderBy('items.item_name', 'asc')
                ->get();
                
        $products = DB::table('products')
                ->select('products.pid as id', 'products.part_number', 'products.selling_price', 'products.purchasing_price', 'products.total_purchase', 'products.purchase_unit', 'products.model', 'products.quantity', 'brands.title', 'items.item_name')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->join('items', 'items.id', 'products.product_id')
                ->where('products.branch_id', Auth::user()->branch_id)
                ->get();

        return view('pages.price-lists', compact('stocks', 'products', 'clients'));
    }

public function getClientPriceList($id) {
      if($id == 0) {
        $id = 0;
        $clients = [["client_name" => "GENERAL", "id" => 0]];
      } else {
        $id = $id;
        $clients = DB::table('clients')
              ->select('clients.client_name', 'clients.id')
              ->where('clients.branch_id', Auth::user()->branch_id)
              ->where('id', $id)
              ->get();
      }       
        $stocks = DB::table('price_lists')
                ->select('products.pid', 'items.item_name', 'price_lists.id', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
                ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
                ->join('items', 'items.id', 'products.product_id')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
                ->where('products.branch_id', Auth::user()->branch_id)
                ->groupBy('products.pid', 'price_lists.id', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'products.selling_price', 'brands.title', 'products.quantity', 'products.model')
                ->orderBy('items.item_name', 'asc')
                ->where('price_lists.client_id', $id)
                ->get();
                
        $products = DB::table('products')
                ->select('products.pid as id', 'products.part_number', 'products.selling_price', 'products.purchasing_price', 'products.total_purchase', 'products.purchase_unit', 'products.model', 'products.quantity', 'brands.title', 'items.item_name')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->join('items', 'items.id', 'products.product_id')
                ->where('products.branch_id', Auth::user()->branch_id)
                ->get();

        return view('pages.clients-price-lists', compact('stocks', 'products', 'clients', 'id'));
    }
    
    public function getMinimumStock() {

        $adjustments = Adjustment::all();

         $less_stocks = DB::table('products')
                        ->select('products.pid as id', 'products.part_number', 'products.quantity', 'products.model', 'items.item_name', 'brands.title', 'products.alert_qty')
                        ->join('items', 'items.id', 'products.product_id')
                        ->join('brands', 'brands.id', 'products.brand_id')
                        ->whereColumn('products.quantity', '<=', 'products.alert_qty')
                        ->where('products.branch_id', Auth::user()->branch_id)
                        ->get();

        return view('pages.minimum-stocks', compact('less_stocks', 'adjustments'));
    }

    
    public function getOutStock() {

        $adjustments = Adjustment::all();

          $out_stocks = DB::table('products')
                        ->select('products.pid as id', 'products.part_number', 'products.quantity', 'products.model', 'items.item_name', 'brands.title')
                        ->join('items', 'items.id', 'products.product_id')
                        ->join('brands', 'brands.id', 'products.brand_id')
                        ->where('quantity', '=', 0)
                        ->where('products.branch_id', Auth::user()->branch_id)
                        ->get();

        return view('pages.out-stocks', compact('out_stocks', 'adjustments'));
    }
    

    public function getStockDetails($id) {

        $products_details = DB::table('products')
                            ->select('products.pid', 'products.part_number', 'products.selling_price', 'products.description', 'products.purchasing_price', 'products.alert_qty', 'products.total_purchase', 'products.purchase_unit', 'products.model', 'products.quantity', 'brands.title', 'items.item_name')
                            ->join('brands', 'brands.id', 'products.brand_id')
                            ->join('items', 'items.id', 'products.product_id')
                            ->where('products.pid', $id)
                            ->where('products.branch_id', Auth::user()->branch_id)
                            ->get();
        
    return view('pages.stock-details', compact('products_details'));

    }


    public function getStockReportByItem() {

        $years = Year::all();

        $items = DB::table('products')
                 ->select('products.pid as id', 'products.model', 'items.item_name')
                 ->join('items', 'items.id', 'products.product_id')
                 ->where('products.branch_id', Auth::user()->branch_id)
                 ->get();
        return view('reports.stock-report', compact('items', 'years'));
   }


   public function getAllStockReport() {

    $years = Year::all();

    return view('reports.all-stock-report', compact('years'));

  }


  public function getAllStockReportPDF(Request $request) {

    $settings= DB::table('general_settings')->select('business_name', 'type')->get();

    $date_time = Year::where('id', $request->year)->select('*')->first();

    $current = $date_time['current_year'];
    $previous = $date_time['previous_year'];

    $sale_items = DB::table('sales')
                ->select(DB::raw("SUM(sales.qty) as item_sold"), 'sales.selling_price', 'sales.qty_balance', 'brands.title', 'items.item_name', 'products.quantity', 'products.sale_unit', 'products.model')
                ->join('products', 'products.pid', 'sales.product_id')
                ->join('items', 'items.id', 'products.product_id')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->groupBy('items.item_name', 'sales.selling_price', 'sales.qty_balance','products.sale_unit','products.model', 'brands.title', 'products.quantity')
                ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                ->where('model', '!=', 'HUDUMA')
                ->where('products.branch_id', Auth::user()->branch_id)
                ->get();

    
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadView('reports.all-stock-report-pdf', compact('sale_items', 'settings', 'current', 'previous'));
    return $pdf->stream();

    // return view('reports.all-stock-report-pdf', compact('sale_items', 'current', 'previous'));
  }

   public function getStockByItem() {

    $years = Year::all();

    $items = DB::table('products')
             ->select('products.pid as id', 'products.model', 'items.item_name')
             ->join('items', 'items.id', 'products.product_id')
             ->where('products.branch_id', Auth::user()->branch_id)
             ->get();
    return view('reports.items-report', compact('items', 'years'));
  }


  public function getStockItemReportPDF(Request $request) {

      $settings= DB::table('general_settings')->select('business_name', 'type')->get();

    $date_time = Year::where('id', $request->year)->select('*')->first();

    $current = $date_time['current_year'];
    $previous = $date_time['previous_year'];

    $total_item_sold = Sale::where('product_id', $request->product_id)
                    ->whereBetween('created_date', [$date_time['first_date'], $date_time['second_date']])
                    ->where('products.branch_id', Auth::user()->branch_id)
                    ->sum('qty');

    $total_item_onstocks = Product::where('pid', $request->product_id)
                           ->select('quantity')
                           ->where('products.branch_id', Auth::user()->branch_id)
                           ->get();


    $item_name = DB::table('sales')
                ->select('items.item_name')
                ->join('products', 'products.pid', 'sales.product_id')
                ->join('items', 'items.id', 'products.product_id')
                ->where('sales.product_id', $request->product_id)
                ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                ->where('sales.branch_id', Auth::user()->branch_id)
                ->get();

    $makes = DB::table('products')
                ->select('model')
                ->where('pid', $request->product_id)
                ->where('products.branch_id', Auth::user()->branch_id)
                ->get();

    $sale_items = DB::table('sales')
            ->select('sales.created_date','sales.selling_price', 'sales.reference', 'sales.qty_balance', 'items.item_name', 'sales.qty', 'products.sale_unit', 'products.pid', 'clients.client_name as client_name', 'sales.client_name as client','sales.vehicle_reg', 'products.product_id')
            ->join('products', 'products.pid', 'sales.product_id')
            ->join('items', 'items.id', 'products.product_id')
            ->leftjoin('clients', 'clients.id', 'sales.client_name')
            ->where('sales.product_id', $request->product_id)
            ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
            ->where('sales.branch_id', Auth::user()->branch_id)
            ->get();

    
    $pdf = App::make('dompdf.wrapper');
    $pdf->loadView('reports.sales-items-report-pdf', compact('sale_items', 'settings', 'total_item_sold', 'total_item_onstocks', 'item_name', 'makes', 'current', 'previous'));
    return $pdf->stream();

    // return view('reports.sales-items-report-pdf', compact('sale_items', 'total_item_sold', 'total_item_onstocks'));
  }



  public function getReturnJob() {

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

    $stocks = DB::table('return_jobs')
               ->select('return_jobs.reasons', 'return_jobs.created_date', 'users.fname', 'users.lname', 'clients.client_name', 'return_jobs.vehicle_reg')
               ->leftjoin('clients', 'clients.id', 'return_jobs.client_id')
               ->leftjoin('users', 'users.id', 'return_jobs.user_id')
               ->groupBy('return_jobs.reasons', 'return_jobs.created_date', 'users.fname', 'users.lname', 'clients.client_name', 'return_jobs.vehicle_reg')
               ->distinct()
               ->get();

    $clients = DB::table('clients')
                ->select('*')
                ->where('clients.branch_id', Auth::user()->branch_id)
                ->get();

    return view('pages.return_jobs', compact('stocks', 'clients', 'products'));

  }


  public function saveReturnJob(Request $request) {
       
    // $request->validate([
    //     '' => 'required',
    // ]);
         
 for($i = 0; $i < count($request->id); $i++){

  $stock_qty = Product::where('pid', $request->id[$i])
              ->where('products.branch_id', Auth::user()->branch_id)
              ->select('quantity')->first();

        $product_id = $request->id[$i]; 
        $qty = $request->qty[$i]; 
        
        $estimates = ReturnJob::create([
        'product_id' => $product_id,
        'branch_id' => Auth::user()->branch_id, 
        'user_id' => Auth::user()->id,
        'qty' => $qty, 
        'vehicle_reg' => $request->vehicle_reg,
        'client_id' => $request->client_id,
        'reasons'=> $request['reasons'],
        'created_date' => $request['created_date']
        ]);

        $stocks = DB::table('products')
                ->where('pid', $request->id[$i])
                ->where('products.branch_id', Auth::user()->branch_id)
                ->update(["quantity" => $stock_qty['quantity'] - $request->qty[$i]]);    
     }
    
    return redirect('/return-jobs')->with('message', 'You have saved Return Job For '.$request->vehicle_reg);
}  
}
