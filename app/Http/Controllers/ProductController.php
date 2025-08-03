<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Store;
use App\Models\Adjustment;
use DB;
use App\Models\ProductModel;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Supplier;
use App\Models\AdjustmentHistory;
use Auth;
use App\Models\PriceList;
use App\Models\Client;
use App;

class ProductController extends Controller
{
    //

    public function index() {

        $brands = Brand::all();
        $products = DB::table('products')
                  ->select('products.pid as id', 'products.part_number', 'products.selling_price', 'products.purchasing_price', 'products.total_purchase', 'products.purchase_unit', 'products.model', 'products.quantity', 'brands.title', 'items.item_name')
                  ->join('brands', 'brands.id', 'products.brand_id')
                  ->join('items', 'items.id', 'products.product_id')
                  ->where('products.branch_id', Auth::user()->branch_id)
                  ->get();

        $items = Item::all();
        $suppliers = Supplier::all();

        $productModels = ProductModel::distinct()->get();

        return view('pages.products', compact('brands', 'products', 'productModels', 'items', 'suppliers'));
    }
    


    public function getLabours() {

      $products = DB::table('labours')
                ->where('branch_id', Auth::user()->branch_id)
                ->select('labour', 'charge', 'rate', 'id')
                ->get();

      return view('pages.labours', compact('products'));
    }


    public function createProduct(Request $data){

        $data->validate([
            'product_id' => 'required',
            'brand_id' => 'required',
        ]);
   // $check = Product::where('product_id', $data->product_id)
                   // ->where('brand_id', $data->brand_id)
                   // ->where('part_number', $data->part_number)
     //               ->first();
  //  if($check != '') {
    //  return redirect()->back()->with('error', 'Error! This Product already exists !');
//    } else {
      $products = Product::create([
          'product_id' => $data['product_id'], 
          'branch_id' => $data['branch_id'], 
          'brand_id' => $data['brand_id'], 
          'supplier_id'=> $data['supplier_id'],
          'model'=> $data['model'],
          'part_number'=> $data['part_number'],
          'purchase_unit' => $data['purchase_unit'],
          'sale_unit' => $data['purchase_unit'],
          'purchasing_price' => $data['purchasing_price'],
          'selling_price' => $data['selling_price'],
          'quantity' => $data['quantity'],
          'alert_qty' =>  $data['alert_qty'],
          'minimum_stock' => $data['minimum_stock'],
          'maximum_stock' => $data['quantity'],
          'total_purchase' => $data['quantity'] * $data['purchasing_price'],
          'total_sale' => $data['quantity'] * $data['selling_price'],
          'features' => $data['description'],
          'is_active' => 1
        ]);

      $price_sheet = PriceList::create([
          'prod_id' => $products->id, 
          'branch_id' => $data['branch_id'], 
          'client_id' => 0, 
          'sale_price'=> $data['selling_price'],
          'status' => 0,
        ]);
    // }

      return redirect()->back()->with('message', 'Product added Successful!');

    }


    public function storeList() {

        $stores = Store::all();

        return view('pages.stores', compact('stores'));
    }


    public function productDetails($id) {
          
      $products = Product::where('pid', $id)->get();

       return ($products);

    }
    
    public function createStore(Request $data){

        $data->validate([
            'store_name' => 'required',
            'region' => 'required',
            'location' => 'required',
        ]);

      $stores = Store::create([
          'store_name' => $data['store_name'], 
          'region' => $data['region'], 
          'location'=> $data['location']
        ]);

      if(!$stores){
        return 'Error!';
      } 
      return redirect('/stores')->with('message', 'Store added Successful!');

    }


    public function adjustmentList() {

        $adjustments  = DB::table('products')
                      ->select('products.pid as id', 'products.part_number', 'products.selling_price', 'products.purchasing_price', 'products.total_purchase', 'products.purchase_unit', 'products.model', 'products.quantity', 'brands.title', 'items.item_name')
                      ->join('brands', 'brands.id', 'products.brand_id')
                      ->join('items', 'items.id', 'products.product_id')
                      ->where('products.branch_id', Auth::user()->branch_id)
                      ->get();


        return view('pages.adjustments', compact('adjustments'));
    }


     
    public function createItemAdjustment(Request $data){

        $data->validate([
            'reason' => 'required',
        ]);

      $adjustments = Adjustment::create([
          'reason' => $data['reason']
        ]);

      if(!$adjustments){
        return 'Error!';
      } 
      return redirect()->back()->with('message', 'Reason added Successful!');

    }



    public function createNewItem(Request $data){

    $data->validate([
        'item_name' => 'required',
    ]);

    $check = Item::where('item_name', $data->item_name)->first();
    if($check != '') {
      return redirect('/settings/products')->with('error', 'Error! This Item already exists !');
    } else {
    $items = Item::create([
        'item_name' => $data['item_name']
      ]);
     }
    return redirect('/settings/products')->with('message', 'Item added Successful!');

    }



    public function assignItemMake(Request $request) {
        
      $request->validate([
        'item_id' => 'required',
        'brand_id' => 'required'
       ]);

       $item = $request->item_id;

       $check = ProductModel::where('product_id', $request->item_id)
              ->where('brand_id', $request->brand_id)->first();

       if($check != '') {
         return redirect('/settings/products')->with('error', 'Error! This Item already Assigned !');
       } else {
       for($i = 0; $i < count($item); $i++) {
              $input_items = $item[$i];

       $productmodels = ProductModel::create([
          'product_id' => $input_items,
          'brand_id' => $request['brand_id']
        ]);
       }
      }
      return redirect()->back()->with('message', ' Assigned Item successfull!');

    }



    public function createModel(Request $request) {

       $request->validate([
        'title' => 'required',
      ]);

      $check = Brand::where('title', $request->title)->first();
      if($check != '') {
        return redirect('/settings/products')->with('error', 'Error! This Brand already exists !');
      } else {
      $models = Brand::create([
          'title' => $request['title']
        ]);
      }
    return redirect('/settings/products')->with('message', 'added Successful!');

    }


    public function createNotification(Request $request) {

                  $request->validate([
                    'product_id' => 'required',
                    'alert_quantity' => 'required'
                  ]);

                  $notifications = Notification::create([
                      'product_id' => $request['product_id'],
                      'alert_quantity' => $request['alert_quantity']
                    ]);

                    if($notifications) {
                      Product::find($request->product_id)->update(['alert_qty'=> $request->alert_quantity, 'minimum_quantity'=> $request->alert_quantity]);
                    }

                  return redirect('/settings/products')->with('message', ' added Successful!');

    }


    public function deleteModel($id){

                $brands = Brand::find($id);

                $brands->delete();

                return redirect()->back()->with('message', 'Model deleted');

     }


  public function deleteProductItem($id){

                $items = ProductModel::find($id);

                $items->delete();

              return redirect()->back()->with('message', 'Product model deleted');

    }


  public function deleteItem($id){

              $items = Item::find($id);

              $items->delete();

          return redirect()->back()->with('message', 'Item deleted');

}


public function deleteAdjustmentReasons($id){

              $adjust = Adjustment::find($id);

              $adjust->delete();

              return redirect()->back()->with('message', 'Adjustment reason deleted');

}


public function getAdjustmentData($id) {

  $stocks = DB::table('products')
          ->select('products.pid', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'brands.title', 'products.quantity', 'products.model')
          ->join('items', 'items.id', 'products.product_id')
          ->join('brands', 'brands.id', 'products.brand_id')
          ->where('products.branch_id', Auth::user()->branch_id)
          ->where('products.pid', $id)
          ->get();

return response()->json($stocks);

}

  public function updateQuantity(Request $request) {

                  $product_id = $request->id;
                  $qty =  $request->new_qty;
                  $reason_id = $request->reason_id;
                  $instock_qty = $request->qty_in_stock;
                  $buying_price = $request->purchasing_price;
                  $selling_price = $request->selling_price;
                  $quantity = $request->qty_in_stock + $request->new_qty;
                  $subtotal_sales = $selling_price * $quantity;
                  $subtotal_purchases = $buying_price * $quantity; 

                  $adjustments = AdjustmentHistory::create([
                    'product_id' => $product_id,
                    'reason_id' => $reason_id,
                    'qty_in_stock' => $instock_qty,
                    'qty' => $quantity,
                  ]);

                  $dd = Product::where('pid', $product_id)
                  ->update(['quantity'=> $quantity, 'total_purchase' => $subtotal_purchases, 'total_sale' => $subtotal_sales]);
                 

             return redirect()->back()->with('message', 'Item Updated');

 }


 public function getAllProduct() {

      $reports = DB::table('products')
                ->select('*')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->join('items', 'items.id', 'products.product_id')
                ->where('products.branch_id', Auth::user()->branch_id)
                ->get();

              return view('pages.general_reports', compact('brands', 'reports', 'productModels', 'items', 'suppliers'));

  }


  public function getSingleProductDetails($id) {

    $products_details = DB::table('products')
                ->select('products.pid', 'products.part_number', 'products.selling_price', 'products.description', 'products.purchasing_price', 'products.alert_qty', 'products.total_purchase', 'products.purchase_unit', 'products.model', 'products.quantity', 'brands.title', 'items.item_name')
                ->join('brands', 'brands.id', 'products.brand_id')
                ->join('items', 'items.id', 'products.product_id')
                ->where('products.pid', $id)
                ->where('products.branch_id', Auth::user()->branch_id)
                ->get();
                
            return view('pages.view-product-details', compact('products_details'));

}


public function editStockProduct($id) {

          $items = Item::all();
          $brands = Brand::all();
          $products = DB::table('products')
                    ->select('products.pid as id', 'products.part_number', 'brands.id as brand_id', 'items.id as product_id', 'products.selling_price', 'products.purchasing_price', "products.part_number", 'products.alert_qty', 'products.total_purchase', 'products.purchase_unit', 'products.model', 'products.quantity', 'brands.title', 'items.item_name', 'products.description')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->join('items', 'items.id', 'products.product_id')
                    ->where('products.pid', $id)
                    ->get();

        return view('pages.edit-products', compact('products', 'items', 'brands'));

}

public function editPriceList($client_id, $product_id) {

  $items = Item::all();

  $products = DB::table('price_lists')
            ->select('price_lists.id', 'items.item_name', 'clients.client_name', 'price_lists.prod_id', 'price_lists.client_id', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->join('products', 'products.pid', 'price_lists.prod_id')
            ->join('items', 'items.id', 'products.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
            ->groupBy('id', 'clients.client_name', 'price_lists.client_id', 'price_lists.prod_id', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->where('price_lists.branch_id', Auth::user()->branch_id)
            ->where('price_lists.id', $product_id)
            ->where('price_lists.client_id', $client_id)
            ->get();

return view('pages.edit-price-list', compact('products', 'items'));

}

public function updateStockProduct(Request $request) {

           $id = $request->id;
           $sell = $request->selling_price;
           $buy = $request->purchasing_price;
           $subtotal_purchase = $request->quantity * $buy;
           $subtotal_sale = $request->quantity * $sell;

          $makes = DB::table('products')
                    ->where('pid', $request->id)
                    ->update([
                      "product_id" => $request['product_id'],
                      "brand_id" => $request['brand_id'],
                      "part_number" => $request['part_number'],
                      "selling_price" => $request['selling_price'],
                      "purchasing_price" => $request['purchasing_price'],
                      "purchase_unit" => $request['purchase_unit'],
                      "model" => $request['model'],
                      "description" => $request['description'],
                      "quantity" => $request['quantity'],
                      "alert_qty" => $request['alert_qty'],
                      "total_purchase" => $subtotal_purchase,
                      "total_sale" => $subtotal_sale
                      ]);
           

          return redirect()->back()->with('message', 'Item updated successful!');  

}

public function deleteProduct($id){

            $products = Product::where('pid', $id);

            $products->delete();

          return redirect()->back()->with('error', 'Product deleted');
}

public function deletePriceList($id){

  
  $product_1 = PriceList::where('id', $id)->first();   

  if($product_1['client'] == '0'){

  $product_id = $product_1['prod_id'];

  $product_2 = DB::table('products')->where('pid', $product_id)->get();

  $product_2->delete();
  $product_1->delete();

  } else {
       $product_1->delete();
  }


return redirect()->back()->with('error', 'Product deleted');

}

  public function editMake($id) {
          
                $makes = Brand::find($id);;

              return view('pages.edit-make', compact('makes'));

  }
  

  public function updateMake(Request $request) {

          $makes = DB::table('brands')
                    ->where('id', $request->id)
                    ->update(["title" => $request['title']]);

          return redirect('settings/product')->with('message', 'Make updated successful!');  

   }


   public function editItem($id) {
                  
            $items = Item::find($id);;

            return view('pages.edit-item', compact('items'));

  }
  

  public function updateItem(Request $request) {

          $items = DB::table('items')
                    ->where('id', $request->id)
                    ->update(["item_name" => $request['item_name']]);

          return redirect('settings/product')->with('message', 'Item updated successful!');  

   }


   public function fetchClientPrice($id) {
    $output = '';
  
    $products = DB::table('price_lists')
            ->select('price_lists.id', 'items.item_name', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->join('products', 'products.pid', 'price_lists.prod_id')
            ->join('items', 'items.id', 'products.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
            ->groupBy('price_lists.client_id', 'id', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->where('price_lists.branch_id', Auth::user()->branch_id)
            ->where('price_lists.client_id', $id)
            ->get();
  
              $output .='<option value="">'."--Select Part --".'</option>';
              foreach($products as $d) {
                 $output .='<option value="'.$d->id.'">'.$d->item_name.' - '.$d->model.' '.'|'.' '.$d->part_number.' : '. '('.$d->quantity.')'. '</option>';
              }       
         return response()->json($output);
  
  }

public function fetchProductModels(Request $request)
{
  
  $assignedItems = DB::table('item_models')
                  ->select('item_models.brand_id', 'brands.title')
                  ->leftJoin('items', 'items.id', 'item_models.product_id')
                  ->leftJoin('brands', 'brands.id', 'item_models.brand_id')
                  ->where('item_models.product_id', $request->product_id)
                  ->get();

    $html = '<option value="">Select Brand</option>';

    foreach ($assignedItems as $item) {
        $html .= '<option value="'.$item->brand_id.'">'.$item->title.'</option>';
    }

    return response()->json($html);
}

  public function displayClientPrice($id) {
    $output = '';
    $clients = Client::where('id', $id)->first();

    $products = DB::table('price_lists')
            ->select('price_lists.id', 'items.item_name', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
            ->join('items', 'items.id', 'products.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
            ->groupBy('price_lists.client_id', 'id', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->where('price_lists.branch_id', Auth::user()->branch_id)
            ->where('price_lists.client_id', $id)
            ->get();

           foreach ($products as $key => $d) {
              $output .= '<tr>' .
                  '<td style="text-align: center;">' . '<input type="checkbox" class="itemCheckbox" name="selectedItems[]" value="'.$d->id.'">'. '</td>' .
                  '<td>' . $d->item_name . '</td>' .
                  '<td>' . $d->model . ' | ' . $d->title . ' | ' . $d->part_number . '</td>' .
                  '<td>' . number_format($d->purchasing_price, 2) . '</td>' .
                  '<td>' . ($d->sale_price) . '</td>' .
                  '<td>'.'<a class="btn btn-sm btn-success" href="products/edit-price-list/' . $d->id . '"><i class="fa fa-edit"></i></a> ';
          
              if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2) {
                  $output .= '<a class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this item?\');" href="products/delete/' . $d->id . '"><i class="fa fa-trash"></i></a>';
              } else {
                  $output .= ''; // No delete button for other roles
              }
          
              $output .= '</td>' .
                  '</tr>';
          }       
  
           $data = array($clients, $output);

         return response()->json($data);
  
  }


  public function generateClientPricePDF(Request $request) {
    // $request->validate([
    //   'selectedItems' => 'required',
    //  ]);
     $clients = Client::where('id', $request->client)->select('client_name')->first();

      $products = DB::table('price_lists')
            ->select('price_lists.id', 'items.item_name', 'clients.client_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->leftjoin('products', 'products.pid', 'price_lists.prod_id')
            ->join('items', 'items.id', 'products.product_id')
            ->join('brands', 'brands.id', 'products.brand_id')
            ->leftjoin('clients', 'price_lists.client_id', 'clients.id')
            ->groupBy('price_lists.client_id', 'id', 'clients.client_name', 'price_lists.sale_price', 'items.item_name', 'products.part_number', 'products.purchasing_price', 'price_lists.sale_price', 'brands.title', 'products.quantity', 'products.model')
            ->where('price_lists.branch_id', Auth::user()->branch_id)
            ->whereIn('price_lists.id', $request->selectedItems)
            ->orderBy('items.item_name', 'asc')
            ->get();
          
           $pdf = App::make('dompdf.wrapper');
           $pdf->loadView('pages.price-list-pdf', compact('products', 'clients'));
           return $pdf->stream();
          }

          public function updatePriceList(Request $request) {
            
            
            if($request->client_id == '0') {
                  $qty = Product::where('pid', $request->product_id)->first();

                  $update_1 = DB::table('products')
                     ->where('pid', $request->product_id)
                     ->update([
                       "selling_price" => $request->selling_price,
                       "total_sale" => $request->selling_price * $qty['qty']
                       ]);

                  $update_2 = DB::table('price_lists')
                       ->where('id', $request->id)
                       ->update([
                         "sale_price" => $request->selling_price
                         ]);
            
            } else {
              $update_2 = DB::table('price_lists')
              ->where('id', $request->id)
              ->update([
                "sale_price" => $request->selling_price
                ]);
            }
 
           return redirect()->back()->with('message', 'Price updated successful!');  
 
 }
}
