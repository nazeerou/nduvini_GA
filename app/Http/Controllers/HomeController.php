<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use DB;
use App;
use App\Models\Brand;
use App\Models\Item;
use App\Models\GeneralSetting;
use App\Models\Adjustment;
use DataTables;
use App\Models\ProductModel;
use Carbon\Carbon;
use App\Models\Year;
use Auth;
use App\Models\Invoice;
use App\Models\Labour;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
          $products = Brand::all();

        $out_stocks = Product::where('quantity', '=', 0)
                     ->where('products.branch_id', Auth::user()->branch_id)
                     ->get();
        
        $in_stocks = Product::where('quantity', '>', 0)
                    ->where('products.branch_id', Auth::user()->branch_id)
                    ->get();

        $less_stocks = DB::table('products')
                    ->select('products.quantity', 'products.alert_qty', 'products.branch_id')
                    ->whereColumn('products.quantity', '<=', 'products.alert_qty')
                    ->where('products.branch_id', Auth::user()->branch_id)
                    ->count();

        $clients = DB::table('clients')
                ->select('id', 'client_name')
                ->orderBy('client_name', 'asc')
                ->where('clients.branch_id', Auth::user()->branch_id)
                ->get();

        $current_year = Carbon::now()->year;

        $year = Year::where('current_year', $current_year)->select('*')->first();

        $total_inventory_purchases = DB::table('purchases')
                                    ->select(DB::raw('SUM((quantity * purchase_price) + ((quantity * purchase_price) * vat_amount)) as total_purchase_amount'))
                                 //   ->whereMonth('created_date', Carbon::now()->month)
                                    ->whereYear('created_date', Carbon::now()->year)
                                    ->where('branch_id', Auth::user()->branch_id)
                                    ->get();


        $total_inventory_sales_value = DB::table('invoices')
                                   // ->whereMonth('created_date', Carbon::now()->month)
                                    ->whereYear('created_date', Carbon::now()->year)
                                    ->where('branch_id', Auth::user()->branch_id)
                                    ->sum('bill_amount');

        $month_sales =  Invoice::select([DB::raw('SUM(bill_amount) as total_month_sales')])
                       ->whereMonth('created_date', Carbon::now()->month)
                       ->whereYear('created_date', Carbon::now()->year)
                       ->where('branch_id', Auth::user()->branch_id)
                       ->get();
        
        $month_purchases = DB::table('purchases')
                         ->select(DB::raw('SUM((quantity * purchase_price) + ((quantity * purchase_price) * vat_amount)) as total_month_purchases'))
                         ->whereMonth('created_date', Carbon::now()->month)
                         ->whereYear('created_date', Carbon::now()->year)
                         ->where('purchases.branch_id', Auth::user()->branch_id)
                         ->get();
                         
        return view('home', compact('products', 'out_stocks', 'in_stocks', 'less_stocks', 'total_inventory_purchases', 'total_inventory_sales_value','month_sales', 'month_purchases', 'clients'));
    }

    public function userList() {

        $users = DB::table('users')
                 ->select('users.id', 'users.fname', 'users.lname', 'users.email', 'users.mobile', 'users.role_id', 'roles.name', 'branches.branch_name')
                 ->leftjoin('branches', 'branches.id', 'users.branch_id')
                 ->leftjoin('roles', 'roles.id', 'users.role_id')
                 ->where('branch_id', Auth::user()->branch_id)              
                 ->get();

        $branches = DB::table('branches')
                   ->select('*')
                   ->get();

        $roles = DB::table('roles')
                   ->select('*')
                   ->get();

        return view('pages.users', compact('users', 'branches', 'roles'));
    }



      public function getAllItems()
    {
        $items = Item::select(['id','item_name']);

        return Datatables::of($items)
                 ->addIndexColumn()
                 ->addColumn('Actions', function($data) {
                    $actions = '<a href="../products/items/edit/'.$data->id.'" class="btn btn-info btn-sm"> <i class="fa fa-edit"></i> </a>';
                    if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2) {
                        $actions .= '&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteItem"><i class="fa fa-trash"></i></button>';
                    } 
                        return $actions;
                })
                ->rawColumns(['Actions'])
                ->make(true);
                
    }

    public function getAllMakes()
    {
        $make = Brand::select(['id', 'title']);

        return Datatables::of($make)
                ->addColumn('Actions', function($data) {
                    $actions = '<a href="../product-brands/edit/'.$data->id.'" class="btn btn-info btn-sm"> <i class="fa fa-edit"></i> </a>';   
                    if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2) {
                        $actions .= '&nbsp;<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteItem"><i class="fa fa-trash"></i></button>';
                    } 
                        return $actions;             
                })
                ->rawColumns(['Actions'])
                ->make(true);
    }

    public function getAssignedMake()
    {
        $item_models = DB::table('item_models')
                    ->select('item_models.id', 'items.item_name', 'brands.title')
                    ->join('brands', 'brands.id', 'item_models.brand_id')
                    ->join('items', 'items.id', 'item_models.product_id')
                    ->get();

        return Datatables::of($item_models)
                ->addColumn('Actions', function($data) {
                    if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2) {
                    return '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteAssignMake"><i class="fa fa-trash"></i></button>';
                    } 
                })
                ->rawColumns(['Actions'])
                ->make(true);
    }

    public function createUser(Request $data) {
        
            $users = User::create([
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'branch_id' => $data['branch_id'],
                'password' => Hash::make(strtoupper($data['lname'])),
            ]);
    
            return redirect('/users');
    }


    public function userProfile() {

        $users = User::where('id', auth()->user()->id)->get();

        return view('pages.profile', compact('users'));
    }


    public function editUser($id){

        $users = User::find($id);

        return view('pages.edit_user', compact(['users']));

    }

    public function getUser($id){

        $users = User::find($id);

        return $users;

    }

    public function updateUser(Request $request) {

        $user = User::find($request->id);

        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->save();

        return redirect('users')->with('message', 'User Updated successful');

    }

    public function updateUserRoles(Request $request) {

        $user = User::find($request->id);
        $user->role_id = $request->role_id;
        $user->save();

        return redirect('users')->with('message', 'User Role Updated');

    }

    public function deleteUser($id){

        $user = User::where('id', $id);

        $user->delete();

        return redirect()->back()->with('message', 'User Deleted');
        
    }

    public function updateUserProfile(Request $request){

        $request->validate([
            'profile_img' => ['required'],
        ]);
   
        $fileName = time().'.'.$request->profile_img->extension();  
        $request->profile_img->move(public_path('/attachments/'), $fileName);

        $user =  User::find(auth()->user()->id)->update(['profile_img'=> $fileName]);
   
        return redirect()->back()->with('message', 'Profile Photo updated');

     }

    public function passwordReset(Request $request){

        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'confirm_password' => ['same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        return redirect()->back()->with('message', 'Password change successfully.');

     }


     public function settings() {

        $brands = Brand::all();
        $adjustments  = Adjustment::all();

        $items = DB::table('items')
                     ->select('item_name', 'id')
                     ->OrderBy('item_name', 'desc')
                     ->get();
                     
        $notifications = DB::table('notifications')
                         ->select('*')
                         ->get();

        $item_models = DB::table('item_models')
                      ->select('item_models.id', 'items.item_name', 'brands.title')
                      ->join('brands', 'brands.id', 'item_models.brand_id')
                      ->join('items', 'items.id', 'item_models.product_id')
                      ->get();

        return view('pages.settings', compact('brands', 'item_models', 'items', 'notifications', 'adjustments'));
     }


     public function deleteItem($id){

        $items = Item::find($id);

        $items->delete();

        return redirect()->back()->with('message', 'Item Deleted');
        
    }
   

    public function deleteMake($id){

        $make = Brand::find($id);

        $make->delete();

        return redirect()->back()->with('message', 'Make Deleted');
        
    }
   
    public function deleteAssignItems($id){

        $products = ProductModel::find($id);

        $products->delete();

        return redirect()->back()->with('message', 'Item Deleted');
        
    }
    
    
      public function generalSettings() {
          
        $settings = DB::table('general_settings')
                         ->select('*')
                         ->get();

        return view('pages.system-settings', compact('settings'));
     }

     public function editGeneralSettings($id){
        $settings = GeneralSetting::find($id);
        return view('pages.edit-system-settings', compact(['settings']));
    }

    public function updateGeneralSettings(Request $request) {
        
        $fileName = time().'.'.$request->logo_file->extension();  
        $request->logo_file->move(public_path('/attachments/'), $fileName);

            
        $settings =  GeneralSetting::find($request->id)->update(['logo_file'=> $fileName, 'business_name' => $request->business_name, 'type' => $request->type, 'address' => $request->address]);

        return redirect()->back()->with('message', 'Settings Updated successful');
    }
   

    public function commingSoon() {

        return view('pages.comming_soon');

    }

    public function testPDF() {

       $pdf = App::make('dompdf.wrapper');
       $pdf->loadView('settings.test');
       return $pdf->stream();

      }

      public function editLabour($id) {
        
        $labours = Labour::where('id', $id)->first();

        return $labours;
      }

      public function saveLabour(Request $data) {
        
        $users = Labour::create([
            'branch_id' => $data['branch_id'],
            'labour' => $data['labour'],
            'rate' => $data['rate'],
            'charge' => $data['charge'],
        ]);

        return redirect('/labours')->with('message', 'Labour Setting created successful');
    }

    public function updateLabour(Request $request) {

        $labour = Labour::find($request->id);

        $labour->labour = $request->labour;
        $labour->charge = $request->charge;
        $labour->rate = $request->rate;
        $labour->save();

        return redirect()->back()->with('message', 'Labour setting Updated successful');

    }


    public function deleteLabour($id){

        $labour = Labour::find($id);

        $labour->delete();

        return redirect()->back()->with('error', 'Labour setting Deleted');
        
    }
}
