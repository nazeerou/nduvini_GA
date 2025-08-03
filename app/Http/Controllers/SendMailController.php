<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use PDF;
use DB;
use Auth;
use App;
use App\Models\Estimation;
use App\Mail\ProfomaTaxInvoiceMail; 
use App\Mail\ProformaInvoiceMail; 


class SendMailController extends Controller
{
    //

    public function sendTaxInvoice($client, $id) {
        // find email of Client
        
        $clients = Client::where('client_name', $client)
                  ->select('email', 'client_name', 'place')->first();

        if($clients->email == NULL) {
           return redirect()->back()->with('warning', 'Client do not have email, Please Update the client Information');
        } else {
      // Generate PDF content
       
        $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
       
              $sales = DB::table('estimations')
                          ->select('estimations.id as id', 'items.item_name', 'estimations.vat_amount', 
                              'estimations.account_no', 'estimations.account_prefix', 'brands.title', 
                              'invoices.invoice_number',
                               'estimations.discount', 'invoices.bank_name', 'invoices.account_number',
                              'invoices.account_name', 'invoices.branch_name', 'invoices.swift_code', 
                              'estimations.vehicle_reg', 'estimations.reference', 'clients.client_name as client',
                               'estimations.client_name','estimations.model as emodel', 'estimations.make as emake',
                                'estimations.chassis', 'estimations.milleage', 'estimations.qty',
                                 'estimations.profoma_invoice', 'estimations.valid_estimate_date', 
                                  'clients.client_name as client_name',
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
                          ->join('clients', 'clients.id', '=', 'invoices.client_id')
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
    
           $tax = 'tax';
           $grand_total_amount = ($total_sales + $total_labours + $vat_charges);
    
    
        $fname = Auth::user()->fname;
        $lname = Auth::user()->lname;
        $place = Auth::user()->place;
      //  Send email with PDF attachment
        Mail::to($clients->email)->send(new ProfomaTaxInvoiceMail(
            $clients, $sales, $total_discounts, $vat_charges, $tax, $grand_total_amount, $labours,
            $total_labours, $total_sales, $id, $products, $client_name, $vat_calculations, $vehicle, $settings,
            $lname, $fname, $place
            ));
      

      return redirect()->back()->with('message', 'Email sent successfully to ' .$clients->client_name );
    
    }
  }





   public function sendProformaInvoice($client, $id) {
        // find email of Client
        
        $clients = Client::where('client_name', $client)
                  ->select('email', 'client_name', 'place')->first();

        if($clients->email == NULL) {
           return redirect()->back()->with('warning', 'Client do not have email, Please Update the client Information');
        } else {
      // Generate PDF content
       
         $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
   
          $sales = DB::table('estimations')
                      ->select('estimations.id as id', 'items.item_name', 'estimations.vat_amount',
                       'account_no', 'account_prefix', 'brands.title', 'estimations.discount',
                        'estimations.total_discount', 'estimations.vehicle_reg', 'estimations.reference',
                         'clients.client_name as client', 'estimations.client_name','estimations.model as emodel',
                          'estimations.make as emake', 'estimations.chassis', 'estimations.milleage',
                           'estimations.qty', 'estimations.profoma_invoice', 'estimations.vat_amount',
                           'estimations.valid_estimate_date', 'estimations.created_date',
                            'clients.client_name as client_name', 'clients.place', 
                            'estimations.created_date', 'estimations.reference',
                             'estimations.client_name as client', 'estimations.selling_price', 'products.purchase_unit',
                              'estimations.total_sales')
                      ->join('products', 'products.pid', '=', 'estimations.product_id')
                      ->join('items', 'items.id', 'products.product_id')
                      ->join('brands', 'brands.id', 'products.brand_id')
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
                              
       $vat_calculations =  $sales[0]->vat_amount * ($total_sales);
  
       if($sales[0]->vat_amount == '0') {
        $vat_charges = 0.00;
      } else {
        $vat_charges =  (0.18) * ($total_sales + $total_labours);
      }
         
       $tax = 'proforma';

       $grand_total_amount = ($total_sales + $total_labours + $vat_charges);

           $fname = Auth::user()->fname;
           $lname = Auth::user()->lname;
           $place = Auth::user()->place;
      //  Send email with PDF attachment
        Mail::to($clients->email)->send(new ProformaInvoiceMail(
            $clients, $sales, $total_discounts, $vat_charges, $tax, $grand_total_amount,
            $labours, $total_labours, $total_sales, $id, $products, $client_name, $vat_calculations, $vehicle, $settings,
            $fname, $lname, $place
            ));
      

      return redirect()->back()->with('message', 'Email sent successfully to ' .$clients->client_name );
    
    }
   }

}
