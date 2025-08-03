<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Year;
use Auth;

class AjaxController extends Controller
{
    //
    public function filterSalesByDate() {

        $start_date = $_GET['startdate'];
        $end_date = $_GET['enddate'];

        $output = '';

        $data = DB::table('sales')
                ->select('*')
                ->join('products', 'products.id', 'sales.product_id')
                ->whereBetween('created_date', array($start_date, $end_date))
                ->get();

                 $x = 1;
         foreach($data as $d) {

            $output .='<tr>'. 
                      '<td>'.$x++.'</td>'.
                      '<td>'.$d->product_name. '</td>'.
                      '<td>'.$d->model. '</td>'.
                      '<td>'.$d->qty. '</td>'.
                      '<td>'.number_format($d->purchasing_price, 2). '</td>'.
                      '<td>'.number_format($d->selling_price, 2). '</td>'.
                      '<td>'.$d->client_name. '</td>'.
                      '<td>'.$d->vehicle_reg. '</td>'.
                      '<td>'.$d->created_date. '</td>'.
                      '</tr>';
         }       

        return response($output);

    }

    
    public function fetchProductModels() {
        $output = '';
        $product_id = $_GET['product_id'];

        $sales = DB::table('item_models')
                 ->select('*')
                 ->join('brands', 'brands.id', '=', 'item_models.brand_id')
                 ->join('items', 'items.id', 'item_models.product_id')
                 ->where('item_models.product_id', '=', $product_id)
                 ->get();

                  foreach($sales as $d) {
                     $output .='<option value="'.$d->brand_id.'">'.$d->title.'</option>';
                  }       

             return response()->json($output);
         }


    public function getProductDetails($id) {
            
        $stocks = DB::table('price_lists')
                ->select('products.pid as id', 'items.item_name', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
                ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
                ->join('items', 'items.id', 'products.product_id')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
                ->groupBy('products.pid', 'id', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'products.selling_price', 'brands.title', 'products.quantity', 'products.model')
                ->where('price_lists.branch_id', Auth::user()->branch_id)
                ->where('price_lists.prod_id', $id)
                ->get();

            return response()->json($stocks);
        }


        public function getLabourDetails($id) {
            
            $reports = DB::table('labours')
                        ->select('id', 'labour', 'rate', 'charge')
                        ->where('id', $id)
                        ->get();
    
                return response()->json($reports);
            }

    
    public function fetchProductAdjustment($id) {
            $output = '';

            $adjustments = DB::table('adjustment_histories')
                        ->select('adjustment_histories.qty', 'adjustment_histories.qty_in_stock', 'adjustment_histories.created_at', 'adjustments.reason')
                        ->join('products', 'products.pid', 'adjustment_histories.product_id')
                        ->join('adjustments', 'adjustments.id', 'adjustment_histories.reason_id')
                        ->where('adjustment_histories.product_id', $id)
                        ->get();


                        if($adjustments)
                        {
                        foreach ($adjustments as $key => $adjust)
                        {
                        $output.='<tr>'.
                        '<td>'.++$key.'</td>'.
                        '<td>'.$adjust->reason.'</td>'.
                        '<td>'.$adjust->qty_in_stock.'</td>'.
                        '<td>'.$adjust->qty.'</td>'.
                        '<td>'.$adjust->created_at.'</td>'.
                        '</tr>';
                        }
                        return Response($output);
                        }
    
            }

    public function filterSupplierByName(Request $request) {

        $output = '';

        $supplier_name = $request->supplier_name;
        
        $purchases = DB::table('purchases')
                        ->select([DB::raw("SUM((purchases.total_purchase * purchases.vat_amount) + purchases.total_purchase) as total_purchases"), DB::raw("SUM(purchases.paid_amount) as paid_amount"), 'purchases.lpo_number', 'purchases.invoice_number', 'purchases.created_date'])
                        ->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
                        ->groupBy('lpo_number' , 'invoice_number', 'created_date')
                        ->where('purchases.supplier_id', $supplier_name)
                        ->orderBy('suppliers.supplier_name','asc')
                        ->distinct()
                        ->get();  

                        if($purchases != '')
                        {
                        foreach ($purchases as $key => $adjust)
                        {
                        $output.='<tr>'.
                        '<td width="50px">'.++$key.'</td>'.
                        '<td width="100px">'.$adjust->created_date.'</td>'.
                        '<td width="100px">'.$adjust->lpo_number.'</td>'.
                        '<td width="100px">'.$adjust->invoice_number.'</td>'.
                        '<td width="160px">'.number_format($adjust->total_purchases,2).'</td>'.
                        '<td width="160px">'.number_format($adjust->paid_amount, 2).'</td>'.
                        '<td width="160px">'.number_format(($adjust->total_purchases - $adjust->paid_amount),2).'</td>'.
                        '</tr>';
                        }
        
                        return Response($output);
                    } 
    
            }
      public function filterSalesByVehicleReg(Request $request) {

             $output = '';

              $vehicle_id = $request->vehicle_reg;

               $sales = DB::table('sales')
                        ->select([DB::raw("SUM((sales.total_sales * sales.vat_amount) + sales.total_sales) as total_sales"), DB::raw("SUM(sales.paid_amount) as paid_amount"), 'clients.client_name as client_name', 'sales.bill_no', 'sales.created_date'])
                        ->join('clients', 'clients.id', 'sales.client_name')
                        ->groupBy('client_name', 'bill_no', 'created_date')
                        ->where('sales.vehicle_reg', $vehicle_id)
                        ->orderBy('clients.client_name','asc')
                        ->distinct()
                        ->get();
                        if($sales != '')
                        {
                        foreach ($sales as $key => $sale)
                        {
                        $output.='<tr>'.
                        '<td width="50px">'.++$key.'</td>'.
                        '<td width="100px">'.$sale->client_name.'</td>'.
                        '<td width="100px">'.$sale->bill_no.'</td>'.
                        '<td width="100px">'.number_format($sale->total_sales, 2).'</td>'.
                        '<td width="100px">'.number_format($sale->paid_amount, 2).'</td>'.
                        '<td width="100px">'.number_format(($sale->total_sales - $sale->paid_amount), 2).'</td>'.
                        '<td width="100px">'.$sale->created_date.'</td>'.
                        '</tr>';
                        }
                        return Response($output);
                     } 
    
            }


            public function searchStockByItem(Request $request) {

                $output = '';
               
               $date_time = Year::where('id', $request->year)->select('*')->first();
               
               $sales = DB::table('sales')
                        ->select('sales.created_date','sales.selling_price', 'sales.reference', 'sales.qty_balance', 'items.item_name', 'sales.qty', 'products.sale_unit', 'products.pid', 'clients.client_name as client_name', 'sales.client_name as client','sales.vehicle_reg', 'products.product_id')
                        ->join('products', 'products.pid', 'sales.product_id')
                        ->join('items', 'items.id', 'products.product_id')
                        ->leftjoin('clients', 'clients.id', 'sales.client_name')
                        ->where('sales.product_id', $request->product_id)
                        ->whereBetween('sales.created_date', [$date_time['first_date'], $date_time['second_date']])
                        ->get();
         
                             if($sales != '')
                             {
                             foreach ($sales as $key => $adjust)
                             {
                             $output.='<tr>'.
                             '<td width="50px">'.++$key.'</td>'.
                             '<td width="100px">'.$adjust->created_date.'</td>'.
                             '<td width="100px">'.strtoupper($adjust->reference).'</td>'.
                             '<td width="100px">'.strtoupper($adjust->client_name).'</td>'.
                             '<td width="100px">'.strtoupper($adjust->vehicle_reg).'</td>'.
                             '<td width="160px">'.number_format($adjust->selling_price, 2).'</td>'.
                             '<td width="50px">'.$adjust->qty." ".$adjust->sale_unit.'</td>'.
                             '<td width="100px">'.$adjust->qty_balance." ".$adjust->sale_unit.'</td>'.
                             '</tr>';
                             }
             
                             return Response($output);
                             } 
         
                 }


                 public function searchCustomer($query) {

                    $output = "";
                   
                    $search = DB::table('estimations')
                            ->select('clients.client_name', 'estimations.make', 'estimations.chassis', 'estimations.model', 'estimations.milleage', 'registration_year', 'clients.id')
                            ->leftjoin('clients', 'clients.id', 'estimations.client_name')
                            ->where('estimations.branch_id', Auth::user()->branch_id)
                            ->where('estimations.vehicle_reg', 'LIKE','%'.$query.'%')
                            ->distinct()
                            ->get();
             
                                 if($search != '')
                                 {
                                 foreach ($search as $k)
                                 {
                                 $output.='<tr>'.
                                '<td>'.strtoupper($k->client_name).'</td>'.
                                '<td>'.strtoupper($k->make).'</td>'.
                                '<td>'.strtoupper($k->model).'</td>'.
                                '<td>'.strtoupper($k->chassis).'</td>'.
                                '<td>'.'<input type="text" value="'.strtoupper($k->milleage).'">'.'</td>'.
                                '<td>'.strtoupper($k->registration_year).'</td>'.
                                '</tr>';
                                 }
                             return response($output);
                        } 
             
            }
}
