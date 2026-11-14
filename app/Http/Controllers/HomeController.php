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
        $branchId = Auth::user()->branch_id;
        $now      = Carbon::now();
        $yearNum  = $now->year;

        /* ============================================================
           INVENTORY
        ============================================================ */
        $products = Brand::all();

        $out_stocks = Product::where('quantity', '=', 0)
            ->where('products.branch_id', $branchId)
            ->get();

        $in_stocks = Product::where('quantity', '>', 0)
            ->where('products.branch_id', $branchId)
            ->get();

        $less_stocks = DB::table('products')
            ->whereColumn('products.quantity', '<=', 'products.alert_qty')
            ->where('products.branch_id', $branchId)
            ->count();

        $clients = DB::table('clients')
            ->select('id', 'client_name')
            ->orderBy('client_name', 'asc')
            ->where('clients.branch_id', $branchId)
            ->get();

        /* ============================================================
           YEAR RECORD
        ============================================================ */
        $year = Year::where('current_year', $yearNum)->first();

        /* ============================================================
           FINANCIAL TOTALS (year-to-date)
        ============================================================ */
        $total_inventory_purchases = DB::table('purchases')
            ->select(DB::raw('SUM((quantity * purchase_price) + ((quantity * purchase_price) * vat_amount)) as total_purchase_amount'))
            ->whereYear('created_date', $yearNum)
            ->where('branch_id', $branchId)
            ->get();

        $total_inventory_sales_value = DB::table('invoices')
            ->whereYear('created_date', $yearNum)
            ->where('branch_id', $branchId)
            ->sum('bill_amount');

        /* ============================================================
           MONTHLY TOTALS
        ============================================================ */
        $month_sales = Invoice::select([DB::raw('SUM(bill_amount) as total_month_sales')])
            ->whereMonth('created_date', $now->month)
            ->whereYear('created_date', $yearNum)
            ->where('branch_id', $branchId)
            ->get();

        $month_purchases = DB::table('purchases')
            ->select(DB::raw('SUM((quantity * purchase_price) + ((quantity * purchase_price) * vat_amount)) as total_month_purchases'))
            ->whereMonth('created_date', $now->month)
            ->whereYear('created_date', $yearNum)
            ->where('purchases.branch_id', $branchId)
            ->get();

        /* ============================================================
           APPROVAL INBOX
           Assumption: `approval_requests` (branch_id, status pending|approved|rejected)
        ============================================================ */
        $pending_approvals = $this->safeCount('approval_requests', [
            'branch_id' => $branchId, 'status' => 'pending',
        ]);
        $approved_requests = $this->safeCount('approval_requests', [
            'branch_id' => $branchId, 'status' => 'approved',
        ]);
        $rejected_requests = $this->safeCount('approval_requests', [
            'branch_id' => $branchId, 'status' => 'rejected',
        ]);

        /* ============================================================
           MESSAGES & NOTIFICATIONS
           Assumption: `messages` (recipient_id, is_read)
                       `notifications` (notifiable_id, read_at)
        ============================================================ */
        $unread_messages = $this->safeCount('messages', [
            'recipient_id' => Auth::id(),
            'is_read'      => 0,
        ]);

        $notifications_count = $this->safeCount('notifications', [
            'notifiable_id' => Auth::id(),
            'read_at'       => null,
        ]);

        /* ============================================================
           STAFF & PAYROLL
           Assumption: `employees` (branch_id)
                       `attendance` (branch_id, date, status)
                       `payrolls` (branch_id, status)
        ============================================================ */
        $total_employees = $this->safeCount('employees', [
            'branch_id' => $branchId,
        ]);

        $present_employees = $this->safeCount('attendance', [
            'branch_id' => $branchId,
            'date'      => $now->toDateString(),
            'status'    => 'present',
        ]);

        $pending_payroll = $this->safeCount('payrolls', [
            'branch_id' => $branchId,
            'status'    => 'pending',
        ]);

        /* ============================================================
           JOB CARDS
           Assumption: `job_cards` (branch_id, status, is_billed)
        ============================================================ */
        $pending_jobs = $this->safeCount('job_cards', [
            'branch_id' => $branchId, 'status' => 'pending',
        ]);
        $vehicles_in_service = $this->safeCount('job_cards', [
            'branch_id' => $branchId, 'status' => 'in_progress',
        ]);
        $completed_jobs = $this->safeCount('job_cards', [
            'branch_id' => $branchId, 'status' => 'completed',
        ]);
        $unbilled_jobs = $this->safeCount('job_cards', [
            'branch_id' => $branchId, 'is_billed' => 0,
        ]);

        /* ============================================================
           ACCOUNTING & FINANCE
           Assumption: `purchases.status`, `payments.status`, `invoices.status`
                       `invoices.amount_paid`, `purchases.amount_paid`
        ============================================================ */
        $pending_purchases = $this->safeCount('purchases', [
            'branch_id' => $branchId, 'status' => 'pending',
        ]);

        $pending_payments = $this->safeCount('payments', [
            'branch_id' => $branchId, 'status' => 'pending',
        ]);

        $pending_invoices = $this->safeCount('invoices', [
            'branch_id' => $branchId, 'status' => 'pending',
        ]);

$receivables = (float) DB::table('invoices')
    ->leftJoinSub(
        DB::table('client_payments')
            ->select('bill_no', DB::raw('SUM(COALESCE(paid_amount, 0)) as paid_total'))
            ->groupBy('bill_no'),
        'cp',
        'cp.bill_no',
        '=',
        'invoices.invoice_number'
    )
    ->where('invoices.branch_id', $branchId)
    // ->whereIn('invoices.status', ['pending', 'partial', 'unpaid'])
    // ->whereNull('invoices.deleted_at')
    ->selectRaw('COALESCE(SUM(invoices.bill_amount - COALESCE(cp.paid_total, 0)), 0) as balance')
    ->value('balance');

        // Payables = amounts you owe suppliers (purchase total minus what's been paid)
        $payables = (float) DB::table('purchases')
    ->leftJoinSub(
        DB::table('payments')
            ->select('invoice_number', DB::raw('SUM(COALESCE(paid_amount, 0)) as paid_total'))
            ->groupBy('invoice_number'),
        'pp',
        'pp.invoice_number',
        '=',
        'purchases.invoice_number'
    )
    ->where('purchases.branch_id', $branchId)
    // ->whereNotIn('purchases.status', ['paid', 'cancelled', 'void'])
    // ->whereNull('purchases.deleted_at')
    ->selectRaw(
        'COALESCE(SUM(
            ((purchases.quantity * purchases.purchase_price)
             + ((purchases.quantity * purchases.purchase_price) * purchases.vat_amount))
            - COALESCE(pp.paid_total, 0)
        ), 0) as balance'
    )
    ->value('balance');

        /* ============================================================
           RECENT ACTIVITY & MESSAGES
           Assumption: `activity_logs` (branch_id, description, user_name, created_at)
                       `messages` (recipient_id, sender_name, message, is_read, created_at)
        ============================================================ */
        $recent_activities = $this->safeGet('activity_logs', [
            'branch_id' => $branchId,
        ], 6);

        $recent_messages = $this->safeGet('messages', [
            'recipient_id' => Auth::id(),
        ], 5);

        /* ============================================================
           CURRENCY
        ============================================================ */
        $currency = optional(Auth::user()->branch)->currency ?? 'TZS';

        /* ============================================================
           RETURN
        ============================================================ */
        return view('home', compact(
            // Inventory
            'products',
            'out_stocks',
            'in_stocks',
            'less_stocks',
            'clients',
            'year',
            // Financial
            'total_inventory_purchases',
            'total_inventory_sales_value',
            'month_sales',
            'month_purchases',
            // Approvals
            'pending_approvals',
            'approved_requests',
            'rejected_requests',
            // Messages
            'unread_messages',
            'notifications_count',
            // Staff
            'total_employees',
            'present_employees',
            'pending_payroll',
            // Job cards
            'pending_jobs',
            'vehicles_in_service',
            'completed_jobs',
            'unbilled_jobs',
            // Accounting
            'pending_purchases',
            'pending_payments',
            'pending_invoices',
            'receivables',
            'payables',
            // Activity / messages feed
            'recent_activities',
            'recent_messages',
            // Config
            'currency'
        ));
    }

    /* ================================================================
       HELPERS
       ================================================================ */

    /**
     * Safe count — returns 0 if the table doesn't exist
     * or if the where-clause references a missing column.
     */
    private function safeCount(string $table, array $where = []): int
    {
        try {
            if (!Schema::hasTable($table)) {
                return 0;
            }
            return (int) DB::table($table)->where($where)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Safe fetch — returns an empty collection on failure.
     * Orders by `created_at` desc if that column exists, otherwise by id desc.
     */
    private function safeGet(string $table, array $where = [], int $limit = 5)
    {
        try {
            if (!Schema::hasTable($table)) {
                return collect();
            }

            $query = DB::table($table)->where($where);

            $orderColumn = Schema::hasColumn($table, 'created_at') ? 'created_at' : 'id';

            return $query->orderByDesc($orderColumn)->limit($limit)->get();
        } catch (\Throwable $e) {
            return collect();
        }
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
