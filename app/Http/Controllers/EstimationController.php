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
use Illuminate\Support\Facades\Validator;
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
use App\Models\Module;
use App\Models\JobCard;
use App\Models\LabourEstimation;
use App\Models\Branch;
use App\Services\AuditLogger;


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


public function estimationSummary()
{
    $role = Role::find(Auth::user()->role_id);

// 1. Aggregate estimation materials and discount first
$estimationTotals = DB::table('estimations')
    ->select([
        'estimations.reference',

        // Gross Materials
        DB::raw("
            IFNULL(SUM(
                estimations.selling_price * estimations.qty
            ), 0) AS gross_materials
        "),

        // Total Discount
        DB::raw("
            IFNULL(SUM(estimations.total_discount), 0)
            AS total_discount
        "),

        // Other estimation details
        DB::raw('MAX(estimations.branch_id) AS branch_id'),
        DB::raw('MAX(estimations.product_id) AS product_id'),
        DB::raw('MAX(estimations.client_name) AS client_id'),
        DB::raw('MAX(estimations.user_id) AS user_id'),
        DB::raw('MAX(estimations.temesa_fee) AS temesa_fee'),
        DB::raw('MAX(estimations.vat_amount) AS vat_amount'),
        DB::raw('MAX(estimations.model) AS model'),
        DB::raw('MAX(estimations.job_card_no) AS job_card_no'),
        DB::raw('MAX(estimations.profoma_invoice) AS profoma_invoice'),
        DB::raw('MAX(estimations.created_date) AS created_date'),
        DB::raw('MAX(estimations.customer_name) AS customer_name'),
        DB::raw('MAX(estimations.vehicle_reg) AS vehicle_reg'),
        DB::raw('MAX(estimations.milleage) AS milleage'),
    ])
    ->where('estimations.branch_id', Auth::user()->branch_id)
    ->whereNotNull('estimations.profoma_invoice')
    ->groupBy('estimations.reference');


// 2. Aggregate labour separately
$labourTotals = DB::table('labour_estimations')
    ->select([
        'estimate_reference',
        DB::raw('SUM(total_amount) AS total_labour')
    ])
    ->groupBy('estimate_reference');


// 3. Aggregate job cards separately to prevent duplicate estimation rows
$jobCardTotals = DB::table('job_cards')
    ->select([
        'estimate_reference',
        DB::raw('MAX(amount) AS amount')
    ])
    ->groupBy('estimate_reference');


// 4. Get final estimation report
$sales = DB::query()
    ->fromSub($estimationTotals, 'e')

    ->leftJoinSub(
        $labourTotals,
        'l',
        function ($join) {
            $join->on('l.estimate_reference', '=', 'e.reference');
        }
    )

    ->leftJoinSub(
        $jobCardTotals,
        'jc',
        function ($join) {
            $join->on('jc.estimate_reference', '=', 'e.reference');
        }
    )

    ->leftJoin('clients', 'clients.id', '=', 'e.client_id')

    ->leftJoin('users', 'users.id', '=', 'e.user_id')

    ->select([

        // 1. Total Discount
        DB::raw("
            e.total_discount AS total_discount
        "),

        // 2. Gross Materials
        DB::raw("
            e.gross_materials AS gross_materials
        "),

        // 3. Net Materials After Discount
        DB::raw("
            (
                e.gross_materials - e.total_discount
            ) AS total_materials
        "),

        // 4. Total Labour
        DB::raw("
            IFNULL(l.total_labour, 0) AS total_labour
        "),

        // 5. TEMESA Fee
        DB::raw("
            (
                (
                    (e.gross_materials - e.total_discount)
                    + IFNULL(l.total_labour, 0)
                ) * IFNULL(e.temesa_fee, 0)
            ) AS temesa_fee_amount
        "),

        // 6. Total Before VAT
        DB::raw("
            (
                (e.gross_materials - e.total_discount)
                + IFNULL(l.total_labour, 0)
                +
                (
                    (
                        (e.gross_materials - e.total_discount)
                        + IFNULL(l.total_labour, 0)
                    ) * IFNULL(e.temesa_fee, 0)
                )
            ) AS total_before_vat
        "),

        // 7. VAT Amount
        DB::raw("
            (
                (
                    (e.gross_materials - e.total_discount)
                    + IFNULL(l.total_labour, 0)
                    +
                    (
                        (
                            (e.gross_materials - e.total_discount)
                            + IFNULL(l.total_labour, 0)
                        ) * IFNULL(e.temesa_fee, 0)
                    )
                ) * IFNULL(e.vat_amount, 0)
            ) AS vat_total
        "),

        // 8. Grand Total
        DB::raw("
            (
                (
                    (e.gross_materials - e.total_discount)
                    + IFNULL(l.total_labour, 0)
                    +
                    (
                        (
                            (e.gross_materials - e.total_discount)
                            + IFNULL(l.total_labour, 0)
                        ) * IFNULL(e.temesa_fee, 0)
                    )
                ) * (1 + IFNULL(e.vat_amount, 0))
            ) AS grand_total_amount
        "),

        // Other Details
        'jc.amount',
        'clients.place',
        'e.model',
        'e.job_card_no',
        'e.profoma_invoice',
        'users.fname',
        'users.lname',
        'e.reference',
        'e.created_date',
        'clients.client_name',
        'e.customer_name',
        'e.vehicle_reg',
        'e.milleage',
    ])

    ->orderBy('e.profoma_invoice', 'desc')

    ->get();


    $modules = Module::with('permissions')->get();

$assigned_permissions = $role->permissions
    ->pluck('id')
    ->map(fn($id) => (int) $id)
    ->toArray();

$permission_lookup = $modules->flatMap(function ($module) {
    return $module->permissions->mapWithKeys(function ($permission) {
        return [
            strtolower($permission->name) => (int) $permission->id
        ];
    });
});


    return view('estimations.estimations-history', compact('sales', 'role',  'modules',
    'assigned_permissions',
    'permission_lookup'));
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

    
$sales = DB::table('estimations')
    ->select([

        // 1. Total Discount
        DB::raw("
            IFNULL(SUM(estimations.total_discount), 0)
            AS total_discount
        "),

        // 2. Gross Materials
        DB::raw("
            IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
            AS gross_materials
        "),

        // 3. Total Materials After Discount
        DB::raw("
            (
                IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
                - IFNULL(SUM(estimations.total_discount), 0)
            ) AS total_materials
        "),

        // 4. Total Labour
        DB::raw("
            IFNULL(MAX(labour_totals.total_labour), 0)
            AS total_labour
        "),

        // 5. TEMESA Fee
        DB::raw("
            (
                (
                    IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
                    - IFNULL(SUM(estimations.total_discount), 0)
                    + IFNULL(MAX(labour_totals.total_labour), 0)
                ) * IFNULL(MAX(estimations.temesa_fee), 0)
            ) AS temesa_fee_amount
        "),

        // 6. Total Before VAT
        DB::raw("
            (
                (
                    IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
                    - IFNULL(SUM(estimations.total_discount), 0)
                )
                + IFNULL(MAX(labour_totals.total_labour), 0)
                +
                (
                    (
                        IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
                        - IFNULL(SUM(estimations.total_discount), 0)
                        + IFNULL(MAX(labour_totals.total_labour), 0)
                    ) * IFNULL(MAX(estimations.temesa_fee), 0)
                )
            ) AS total_before_vat
        "),

        // 7. VAT Amount
        DB::raw("
            (
                (
                    (
                        IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
                        - IFNULL(SUM(estimations.total_discount), 0)
                    )
                    + IFNULL(MAX(labour_totals.total_labour), 0)
                    +
                    (
                        (
                            IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
                            - IFNULL(SUM(estimations.total_discount), 0)
                            + IFNULL(MAX(labour_totals.total_labour), 0)
                        ) * IFNULL(MAX(estimations.temesa_fee), 0)
                    )
                ) * IFNULL(MAX(estimations.vat_amount), 0)
            ) AS vat_total
        "),

        // 8. Grand Total
        DB::raw("
            (
                (
                    (
                        IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
                        - IFNULL(SUM(estimations.total_discount), 0)
                    )
                    + IFNULL(MAX(labour_totals.total_labour), 0)
                    +
                    (
                        (
                            IFNULL(SUM(estimations.selling_price * estimations.qty), 0)
                            - IFNULL(SUM(estimations.total_discount), 0)
                            + IFNULL(MAX(labour_totals.total_labour), 0)
                        ) * IFNULL(MAX(estimations.temesa_fee), 0)
                    )
                ) * (1 + IFNULL(MAX(estimations.vat_amount), 0))
            ) AS grand_total_amount
        "),

        // Invoice details
        DB::raw('MAX(invoices.invoice_number) AS invoice_number'),
        DB::raw('MAX(invoices.created_date) AS created_date'),

        // Other details
        'clients.place',
        'estimations.model',
        'estimations.job_card_no',
        'estimations.profoma_invoice',
        'users.fname',
        'users.lname',
        'estimations.reference',
        'clients.client_name',
        'estimations.customer_name',
        'estimations.vehicle_reg',
        'invoices.id',

    ])

    ->join('products', 'products.pid', '=', 'estimations.product_id')

    ->leftJoin('clients', 'clients.id', '=', 'estimations.client_name')

    ->leftJoin('job_cards', 'job_cards.job_card_no', '=', 'estimations.job_card_no')

    ->join('invoices', 'invoices.estimate_ref', '=', 'estimations.reference')

    // Labour totals
    ->leftJoinSub(
        DB::table('labour_estimations')
            ->select(
                'estimate_reference',
                DB::raw('SUM(total_amount) AS total_labour')
            )
            ->groupBy('estimate_reference'),
        'labour_totals',
        function ($join) {
            $join->on(
                'labour_totals.estimate_reference',
                '=',
                'estimations.reference'
            );
        }
    )

    ->leftJoin('users', 'users.id', '=', 'estimations.user_id')

    ->where('estimations.branch_id', Auth::user()->branch_id)

    ->whereNotNull('estimations.profoma_invoice')

    ->groupBy([
        'estimations.reference',
        'estimations.job_card_no',
        'estimations.model',
        'estimations.profoma_invoice',
        'invoices.created_date',
        'estimations.customer_name',
        'estimations.vehicle_reg',
        'clients.place',
        'clients.client_name',
        'job_cards.amount',
        'users.fname',
        'users.lname',
        'invoices.id',
    ])

    ->orderBy('invoices.invoice_number', 'desc')
    ->get();


    $modules = Module::with('permissions')->get();

$assigned_permissions = $role->permissions
    ->pluck('id')
    ->map(fn($id) => (int) $id)
    ->toArray();

$permission_lookup = $modules->flatMap(function ($module) {
    return $module->permissions->mapWithKeys(function ($permission) {
        return [
            strtolower($permission->name) => (int) $permission->id
        ];
    });
});

     return view('estimations.all-invoices', 
     compact('sales', 
     'role', 'clients', 
     'products', 
     'labours', 
     'modules',
    'assigned_permissions',
    'permission_lookup'));
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
            'estimations.customer_name',
            'job_cards.delivery_date'
        ])
        ->leftjoin('estimations', 'estimations.job_card_no', '=', 'job_cards.job_card_no')
        ->leftJoin('invoices', 'invoices.estimate_ref', '=', 'estimations.reference') // Corrected leftJoin method parameter order
        ->leftJoin('users', 'users.id', '=', 'job_cards.user_id')
        ->leftJoin('clients', 'clients.id', '=', 'estimations.client_name')
        ->where('job_cards.branch_id', Auth::user()->branch_id)
        ->groupBy('job_cards.job_card_no', 'job_cards.created_date', 'clients.place', 
        'estimations.customer_name', 'users.fname', 'job_cards.job_card_ID',
        'users.lname', 'invoices.invoice_number', 'job_cards.status', 
        'estimations.reference', 'estimations.vehicle_reg', 'clients.client_name',
         'estimations.created_date', 'job_cards.delivery_date') // Removed duplicate grouping
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
            'temesa_fee'=> $accounts['temesa_fee'],
            'customer_name' => $accounts->customer_name, 
            'total_sales' => (($request['selling_price'][$i]*$request->qty[$i]) - (($request->discount[$i]/100) * ($request['selling_price'][$i]*$request->qty[$i]))),
            'reference' => $request->reference,
            'created_date' => $accounts['created_date']
            ]);
         }

        return redirect('/estimations/details/'.$request->reference)->with('message', 'You have Added New Part for '.$accounts['vehicle_reg']);
    }


public function exportEstimationsPdf(Request $request)
{
    $mode  = $request->query('mode', 'client');
    $value = trim((string) $request->query('value', ''));

    if (!in_array($mode, ['client', 'vehicle'], true) || $value === '') {
        abort(400, 'A client or vehicle filter is required to export.');
    }

    $branchId = Auth::user()->branch_id;

    $query = DB::table('estimations')
        ->leftJoin('clients', 'clients.id', '=', 'estimations.client_name')
        ->leftJoin('users', 'users.id', '=', 'estimations.user_id')
        ->select([
            'estimations.reference',
            DB::raw('MAX(estimations.vehicle_reg)     as vehicle_reg'),
            DB::raw('MAX(estimations.profoma_invoice) as profoma_invoice'),
            DB::raw('MAX(estimations.job_card_no)     as job_card_no'),
            DB::raw('MAX(estimations.customer_name)   as customer_name'),
            DB::raw('MAX(estimations.created_date)    as created_date'),
            DB::raw('MAX(estimations.model)           as model'),
            DB::raw('MAX(estimations.milleage)        as milleage'),
            DB::raw('MAX(clients.client_name)        as client_name'),
            DB::raw('MAX(clients.place)              as place'),
            DB::raw('MAX(users.fname)                as fname'),
            DB::raw('MAX(users.lname)                as lname'),
        ])
        ->where('estimations.branch_id', $branchId)
        ->groupBy('estimations.reference');

    if ($mode === 'client') {
        $query->where(function ($q) use ($value) {
            $q->where('clients.client_name', $value)
              ->orWhere('estimations.customer_name', $value);
        });
    } else {
        $query->where('estimations.vehicle_reg', $value);
    }

    $rows = $query->orderBy(DB::raw('MAX(estimations.created_date)'), 'desc')->get();

    // Branch name
    $branchName = config('app.name', 'Company');
    try {
        $branch = DB::table('branches')->where('id', $branchId)->first();
        if ($branch && !empty($branch->branch_name)) {
            $branchName = $branch->branch_name;
        }
    } catch (\Exception $e) {}

    $modeLabel   = $mode === 'client' ? 'Client' : 'Vehicle';
    $generatedAt = now()->format('d M Y, H:i');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'pages.estimations-report-pdf',
        compact('rows', 'mode', 'modeLabel', 'value', 'branchName', 'generatedAt')
    );

    // $pdf->setPaper('a4', 'landscape');
    $pdf->setOption('defaultFont', 'DejaVu Sans');
    $pdf->setOption('isHtml5ParserEnabled', true);

    $slug     = \Illuminate\Support\Str::slug($value);
    $filename =  strtoupper($slug) . '-' . now()->format('Ymd-His') . '.pdf';

    return $pdf->download($filename);
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
        
       

        $temesa_fee =  ($sales[0]->temesa_fee ?? 0) * ($total_sales + $total_labours);

         if($sales[0]->vat_amount == '0') {
            $vat_charges = 0.00;
        } else {
            $vat_charges =  (0.18) * ($total_sales + $total_labours + $temesa_fee);
        }

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
                    ->select('job_cards.id', 'job_cards.job_card_no', 'job_cards.estimate_reference',
                     'job_cards.delivery_date')
                    ->where('job_cards.branch_id', Auth::user()->branch_id)
                    ->where('job_cards.estimate_reference', $reference)
                    ->get();
        return $edit;  

    }

     public function updateDeliveryNote(Request $request) {

        $update = DB::table('job_cards')
                    ->where('estimate_reference', $request->reference_no)
                    ->where('branch_id', Auth::user()->branch_id)
                    ->update(["delivery_date" => $request->delivery_date
                            ]);
 
        return redirect()->back()->with('message', 'Delivery updated successful');
     } 
   
public function editProfomaDetails($id)
{
    $edit = DB::table('estimations')
                ->select(
                    'estimations.id',
                    'estimations.reference',
                    'estimations.vehicle_reg',
                    'estimations.profoma_invoice',
                    'estimations.make',
                    'estimations.model',
                    'estimations.created_date',
                    'estimations.chassis',
                    'estimations.milleage',
                    'estimations.client_name',   
                    'estimations.vat_amount',    
                    'estimations.temesa_fee'     
                )
                ->where('estimations.reference', $id)
                ->get();

    return $edit;
}

public function updateProfomaDetails(Request $request)
    {
        // ----- 1. Validation -----
        $request->validate([
            'reference'              => 'required|string|max:255',
            'profoma_invoice'        => 'nullable|string|max:255',
            'vehicle_reg'            => 'nullable|string|max:255',
            'make'                   => 'nullable|string|max:255',
            'model'                  => 'nullable|string|max:255',
            'chassis'                => 'nullable|string|max:255',
            'milleage'               => 'nullable|string|max:255',
            'created_date'           => 'nullable|date',
            'client_name'            => 'nullable',
            'vat_amount'             => 'nullable|numeric',
            'temesa_fee'             => 'nullable|numeric',
            'updated_by'             => 'nullable|integer',
            'proforma_change_reason' => 'nullable|string|max:2000',
        ]);

        // ----- 2. Find the row -----
        $query = DB::table('estimations')->where('reference', $request->reference);

        if (!$query->exists()) {
            return redirect()->back()->with('error', 'Proforma not found.');
        }

        // Snapshot BEFORE update
        $targetRowQuery = DB::table('estimations')
            ->when($request->filled('id'), function ($q) use ($request) {
                return $q->where('id', $request->id);
            })
            ->when(!$request->filled('id'), function ($q) use ($request) {
                return $q->where('reference', $request->reference);
            });

        $oldRow = (array) $targetRowQuery->first();

        // ----- 3. Duplicate proforma check -----
        $currentProforma    = $oldRow['profoma_invoice'] ?? null;
        $submittedProforma  = $request->filled('proforma_invoice')
                            ? trim($request->proforma_invoice)
                            : null;
        $currentTrimmed     = $currentProforma !== null ? trim($currentProforma) : null;
        $proformaChanged    = ($submittedProforma !== $currentTrimmed);

        if ($proformaChanged && $submittedProforma !== null && $submittedProforma !== '') {
            $duplicate = DB::table('estimations')
                ->where('profoma_invoice', $submittedProforma)
                ->where('reference', '!=', $request->reference)
                ->exists();

            if ($duplicate) {
                return redirect()->back()->withInput()
                    ->with('error', "Can't update to the existing Proforma. The Proforma Invoice # '" .
                                    $submittedProforma . "' already belongs to another estimation.");
            }

            if (!$request->filled('proforma_change_reason')) {
                return redirect()->back()->withInput()
                    ->with('error', 'A reason is required when changing the Proforma number.');
            }
        }

        // ----- 4. Build update payloads -----
        $updateData = [
            'client_name'     => $request->client_name,
            'profoma_invoice' => $request->proforma_invoice,
            'created_date'    => $request->created_date,
            'temesa_fee'      => $request->temesa_fee ?? 0,
            'vat_amount'      => $request->vat_amount ?? 0,
            'vehicle_reg'     => $request->vehicle_reg,
            'make'            => $request->make,
            'model'           => $request->model,
            'chassis'         => $request->chassis,
            'milleage'        => $request->milleage,
        ];

        $vehicleData = $updateData;

        DB::beginTransaction();
        try {
            DB::table('estimations')
                ->where('reference', $request->reference)
                ->update($updateData);

            if ($request->filled('id')) {
                DB::table('estimations')
                    ->where('id', $request->id)
                    ->update($vehicleData);
            }

            // Snapshot AFTER update
            $newRow = (array) DB::table('estimations')
                ->when($request->filled('id'), function ($q) use ($request) {
                    return $q->where('id', $request->id);
                })
                ->when(!$request->filled('id'), function ($q) use ($request) {
                    return $q->where('reference', $request->reference);
                })
                ->first();

            DB::commit();

            // ----- 5. WRITE THE AUDIT LOG -----
            if ($proformaChanged && $submittedProforma !== null && $submittedProforma !== '') {
                // CRITICAL: Proforma number was changed
                AuditLogger::log(
                    'Proforma number changed',
                    'update',
                    'estimations',
                    $request->id ?: ($oldRow['id'] ?? null),
                    $oldRow,
                    $newRow,
                    'Changed Proforma Invoice # from "' .
                        ($currentTrimmed ?: '—') . '" to "' . $submittedProforma .
                        '". Reason: ' . $request->proforma_change_reason,
                    true,                           // is_critical
                    $request->reference
                );
            } else {
                // Normal update
                AuditLogger::log(
                    'Proforma details updated',
                    'update',
                    'estimations',
                    $request->id ?: ($oldRow['id'] ?? null),
                    $oldRow,
                    $newRow,
                    'Updated proforma details for reference ' . $request->reference,
                    false,
                    $request->reference
                );
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('message', 'Proforma updated successfully for all related items!');
    }


   
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
  
       $temesa_fee =  ($sales[0]->temesa_fee ?? 0) * ($total_sales + $total_labours);

         if($sales[0]->vat_amount == '0') {
            $vat_charges = 0.00;
        } else {
            $vat_charges =  (0.18) * ($total_sales + $total_labours + $temesa_fee);
        }

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
                      ->select('estimations.id as id', 'estimations.product_id', 'estimations.qty', 'items.item_name',
                       'estimations.vat_amount', 'brands.title', 'estimations.vehicle_reg', 'estimations.reference',
                        'clients.client_name as client', 'estimations.client_name as client_id', 'estimations.client_name',
                        'estimations.model', 'estimations.make', 'estimations.chassis', 'estimations.milleage', 'temesa_fee',
                         'estimations.qty', 'estimations.profoma_invoice', 'estimations.valid_estimate_date', 
                         'estimations.created_date', 'clients.client_name as client_name', 'clients.place', 
                         'estimations.created_date', 'estimations.reference', 'estimations.client_name as client', 
                          'products.model', 'estimations.selling_price', 'estimations.total_sales')
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

        //  $grand_total_amount = ($total_sales + $total_labours + $vat_charges);
        $temesa_fee =  ($sales[0]->temesa_fee ?? 0) * ($total_sales + $total_labours + $vat_charges);

        $grand_total_amount = ($total_sales + $total_labours + $temesa_fee + $vat_charges);


        return view('estimations.create-job-cards', compact('sales', 'temesa_fee', 'grand_total_amount', 'job_card_no',  'invoice', 'total_bill_amount'));

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


         public function getJobCardData($reference)
{
    try {
        $branchId = Auth::user()->branch_id;
        
        // Next job card number
        $job_card = DB::table('job_cards')->max('job_card_ID');
        $job_card_no = $job_card ? ($job_card + 1) : 1;
        
        // Sales query
        $sales = DB::table('estimations')
            ->select(
                'estimations.id as id',
                'estimations.product_id',
                'estimations.qty',
                'estimations.vat_amount',
                'estimations.vehicle_reg',
                'estimations.reference',
                'estimations.client_name as client_id',
                'estimations.model',
                'estimations.make',
                'estimations.chassis',
                'estimations.milleage',
                'estimations.temesa_fee',
                'estimations.profoma_invoice',
                'estimations.created_date',
                'estimations.selling_price',
                'estimations.total_sales'
            )
            ->where('estimations.branch_id', $branchId)
            ->where('estimations.reference', $reference)
            ->get();
        
        if ($sales->isEmpty()) {
            return response()->json([
                'error' => "No estimation found for reference '{$reference}'.",
                'hint' => 'Ensure the estimation exists and belongs to this branch.',
            ], 404);
        }
        
        // Totals
        $total_labours = DB::table('labour_estimations')
            ->where('labour_estimations.branch_id', $branchId)
            ->where('labour_estimations.estimate_reference', $reference)
            ->sum('total_amount');
        
        $total_sales = DB::table('estimations')
            ->where('reference', $reference)
            ->where('estimations.branch_id', $branchId)
            ->sum('total_sales');
        
        // VAT
        $vat_charges = 0;
        if ($sales[0]->vat_amount != '0') {
            $vat_charges = 0.18 * ($total_sales + $total_labours);
        }
        
        // Temesa fee
        $temesa_fee = ($sales[0]->temesa_fee ?? 0) * ($total_sales + $total_labours + $vat_charges);
        
        // Grand total
        $grand_total_amount = $total_sales + $total_labours + $temesa_fee + $vat_charges;
        
        return response()->json([
            'client_id' => $sales[0]->client_id,
            'reference' => $sales[0]->reference,
            'vehicle_reg' => strtoupper($sales[0]->vehicle_reg),
            'grand_total_amount' => number_format($grand_total_amount, 2),
            'job_card_no' => $job_card_no,
            'items' => $sales->map(fn($s) => [
                'id' => $s->id,
                'product_id' => $s->product_id,
                'qty' => $s->qty,
            ])->values(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'reference' => $reference,
        ], 500);
    }
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


public function deleteEstimations($id)
{
    $user    = Auth::user();
    $roleId  = (int) ($user->role_id ?? 0);
    $isAdmin = in_array($roleId, [1, 2], true);

    // ----- 1. Fetch the rows BEFORE doing anything -----
    $estimationRows = DB::table('estimations')
        ->where('reference', $id)
        ->get();

    if ($estimationRows->isEmpty()) {
        return redirect()->back()->with('error', 'Estimation not found.');
    }

    // ----- 2. Job Card check -----
    // Estimations linked to a Job Card (completed or ongoing work) cannot be
    // deleted by regular users — only by role 1 or 2 (admin override).
    $hasJobCard = $estimationRows->contains(function ($row) {
        return !empty($row->job_card_no);
    });

    if ($hasJobCard && !$isAdmin) {
        return redirect()->back()->with(
            'error',
            'Cannot delete: this estimation has a Job Card attached (completed or ongoing activity). ' .
            'Only administrators can delete such records.'
        );
    }

    // ----- 3. Authorization — only role 1 or 2 may delete -----
    if (!$isAdmin) {
        return redirect()->back()->with('error', 'You are not authorized to delete this estimation.');
    }

    // ----- 4. Capture labour rows for the audit snapshot -----
    $labourRows = DB::table('labour_estimations')
        ->where('estimate_reference', $id)
        ->get();

    $firstRow   = $estimationRows->first();
    $clientName = $firstRow->client_name ?? $firstRow->customer_name ?? '—';
    $vehicle    = $firstRow->vehicle_reg ?? '—';
    $proforma   = $firstRow->profoma_invoice ?? '—';
    $jobCardNo  = $firstRow->job_card_no ?? null;

    // ----- 5. Delete inside a transaction -----
    DB::beginTransaction();
    try {
        $deleted = DB::table('estimations')
            ->where('reference', $id)
            ->delete();

        DB::table('labour_estimations')
            ->where('estimate_reference', $id)
            ->delete();

        DB::commit();

        // ----- 6. Write the CRITICAL audit log -----
        $description = 'Entire estimation with reference "' . $id . '" was permanently deleted. ' .
            'Client: ' . $clientName . ', Vehicle: ' . $vehicle . '. ' .
            'Removed ' . $deleted . ' estimation row(s) and ' . $labourRows->count() . ' labour line(s).';

        if ($jobCardNo) {
            $description .= ' ⚠ This estimation had a Job Card (#' . $jobCardNo . ') ' .
                'attached — deleted by admin override (role ' . $roleId . ').';
        }

        AuditLogger::log(
            'Estimation deleted' . ($jobCardNo ? ' (with Job Card override)' : ''),
            'delete',
            'estimations',
            null,
            [
                'reference'           => $id,
                'proforma_invoice'    => $proforma,
                'client'              => $clientName,
                'vehicle_reg'         => $vehicle,
                'job_card_no'         => $jobCardNo,
                'had_job_card'        => $hasJobCard,
                'estimations_deleted' => $deleted,
                'estimations'         => $estimationRows->toArray(),
                'labour'              => $labourRows->toArray(),
            ],
            [],
            $description,
            true,  // is_critical
            $id
        );

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->with('error', 'Delete failed: ' . $e->getMessage());
    }

    return redirect()->back()
        ->with('message', 'Estimation deleted successfully.');
}

public function getInvoice($reference)
{
    $branchId = Auth::user()->branch_id;

    /* ============================================================
     | 1. Next invoice number (scoped to branch)
     | ============================================================ */
    $lastInvoice = DB::table('invoices')
        ->where('branch_id', $branchId)
        ->max('invoice_number');

    $invoice_no   = $lastInvoice ? ((int) $lastInvoice + 1) : 1;
    $reference_id = date('YdHis');

    /* ============================================================
     | 2. Load job_card + estimations (including vat + temesa rates)
     | ============================================================ */
    $row = DB::table('job_cards')
        ->select(
            'job_cards.*',
            'estimations.reference   as estimate_reference',
            'estimations.client_name as estimation_client_id',
            'estimations.vehicle_reg as estimation_vehicle_reg',
            'estimations.make        as estimation_make',
            'estimations.model       as estimation_model',
            'estimations.vat_amount  as estimation_vat_amount',
            'estimations.temesa_fee  as estimation_temesa_fee'
        )
        ->leftJoin('estimations', 'estimations.reference', 'job_cards.estimate_reference')
        ->where('job_cards.branch_id', $branchId)
        ->where('job_cards.job_card_no', '=', $reference)
        ->first();

    if (!$row) {
        return response()->json(['error' => 'Job card not found.'], 404);
    }

    /* ============================================================
     | 3. Fallbacks: prefer job_cards, else estimations
     | ============================================================ */
    $clientId    = $row->client_id         ?? $row->estimation_client_id   ?? '';
    $vehicleReg  = $row->vehicle_reg       ?? $row->estimation_vehicle_reg ?? '';
    $make        = trim(($row->make ?? $row->estimation_make ?? '') . ' ' .
                        ($row->model ?? $row->estimation_model ?? ''));
    $estimateRef = $row->estimate_reference ?? '';
    $lpoNumber   = $row->lpo_number ?? '';

    /* ============================================================
     | 4. Amount breakdown
     |    total_materials = Σ(selling_price × qty)  — from estimations
     |    total_labour    = Σ total_amount          — from labour_estimations
     |    total_temesa    = (materials + labour) × temesa_rate
     |    VAT             = (materials + labour + temesa) × vat_rate
     |    grand_total     = materials + labour + temesa + VAT
     | ============================================================ */

    // ---- 4a. Materials — SUM(selling_price × qty) ----
    $totalMaterials = (float) DB::table('estimations')
        ->where('reference', $estimateRef)
        ->where('branch_id', $branchId)
        ->sum(DB::raw('selling_price * qty'));

    // ---- 4b. Labour ----
    $totalLabour = (float) DB::table('labour_estimations')
        ->where('estimate_reference', $estimateRef)
        ->sum('total_amount');

    // ---- 4c. Rates (fractions: 0 or 0.18 / 0 or 0.08) ----
    $vatRate    = (float) ($row->estimation_vat_amount ?? 0);
    $temesaRate = (float) ($row->estimation_temesa_fee  ?? 0);

    // ---- 4d. TEMESA ----
    $totalTemesa = ($totalMaterials + $totalLabour) * $temesaRate;

    // ---- 4e. VAT ----
    $vatBase  = $totalMaterials + $totalLabour + $totalTemesa;
    $vatTotal = $vatBase * $vatRate;

    // ---- 4f. Grand total ----
    $grandTotal = $totalMaterials + $totalLabour + $totalTemesa + $vatTotal;

    /* ============================================================
     | 5. Account formatting
     | ============================================================ */
    $accountNo     = (int) ($row->account_no ?? 0);
    $accountPrefix = $row->account_prefix ?? '';
    if ($accountNo < 10)         $formattedAcc = '00' . $accountNo;
    elseif ($accountNo < 100)    $formattedAcc = '0'  . $accountNo;
    else                         $formattedAcc = (string) $accountNo;

    /* ============================================================
     | 6. Status badge
     | ============================================================ */
    $status = (int) ($row->status ?? 0);
    if ($status === 0) {
        $statusBadge = '<span class="label label-info">In Progress</span>';
    } elseif ($status === 1) {
        $statusBadge = '<span class="label label-success">Completed</span>';
    } else {
        $statusBadge = '<span class="label label-warning">Cancelled</span>';
    }

    /* ============================================================
     | 7. Response
     | ============================================================ */
    return response()->json([
        'reference_id'  => $reference_id,
        'client_id'     => $clientId,
        'estimate_ref'  => $estimateRef,
        'vehicle_reg'   => $vehicleReg,
        'account'       => $accountPrefix . $formattedAcc,
        'make'          => $make,
        'job_card_no'   => $row->job_card_no ?? '',

        // ---- Bill amount = GRAND TOTAL ----
        'amount'         => $grandTotal,

        // ---- Breakdown ----
        'total_materials' => number_format($totalMaterials, 2, '.', ''),
        'total_labour'    => number_format($totalLabour,    2, '.', ''),
        'total_temesa'    => number_format($totalTemesa,    2, '.', ''),
        'total_vat'       => number_format($vatTotal,       2, '.', ''),
        'grand_total'     => number_format($grandTotal,     2, '.', ''),

        'vat_rate'        => $vatRate,
        'temesa_rate'     => $temesaRate,

        'invoice_no'    => $invoice_no,
        'status'        => $status,
        'status_badge'  => $statusBadge,
        'lpo_number'    => $lpoNumber,
    ]);
}

     public function saveInvoice(Request $request) {

        // return $request;

        $date = date('Y-m-d');

        // check if invoice number already exists
        // if (Invoice::where('invoice_number', $request->invoice_no)->exists()) {
        //     return redirect()->back()->with('error', 'Invoice number exists.');
        // }
    
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
            'created_date' => $date,
            'lpo_no' => $request->lpo_number
        ]);
    
        DB::table('job_cards')
            ->where('job_card_no', $request->job_card_no)
            ->where('branch_id', Auth::user()->branch_id)
            ->update([
                'invoice_no' => $invoice_no,
                'amount' => $request->bill_amount
            ]);
    
        return redirect()->back()->with('message', 'You have created Invoice '.$invoice_no);
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
                    ->select('invoices.client_id', 'clients.client_name', 'invoices.reference', 
                      'invoices.account_name', 'invoices.account_number', 'invoices.created_date'
                              ,'invoices.branch_name', 'invoices.swift_code', 'invoices.lpo_no',
                               'invoices.invoice_number', 'invoices.bank_name', 'invoices.id')
                    ->join('clients', 'clients.id', 'invoices.client_id')
                    ->where('invoices.id', $id)
                    ->get();
    
           return $invoice;  
        
        }

       public function updateInvoice(Request $request)
{
    // ----- 1. Validation -----
    $request->validate([
        'id'                     => 'required|integer',
        'client_name'            => 'nullable',
        'created_date'           => 'nullable|date',
        'reference_no'           => 'nullable|string|max:255',
        'invoice_number'         => 'nullable|string|max:255',
        'lpo_number'             => 'nullable|string|max:255',
        'account_number'         => 'nullable|string|max:255',
        'account_name'           => 'nullable|string|max:255',
        'bank_name'              => 'nullable|string|max:255',
        'branch_name'            => 'nullable|string|max:255',
        'swift_code'             => 'nullable|string|max:255',
        'invoice_change_reason'  => 'nullable|string|max:2000',
        'updated_by'             => 'nullable|integer',
    ]);

    // ----- 2. Locate the invoice -----
    $invoice = Invoice::findOrFail($request->id);

    // ----- 3. Snapshot before update (for audit) -----
    $oldValues = $invoice->toArray();

    $currentInvoiceNumber   = $invoice->invoice_number !== null
                                ? trim((string) $invoice->invoice_number)
                                : null;
    $submittedInvoiceNumber = $request->filled('invoice_number')
                                ? trim((string) $request->invoice_number)
                                : null;

    $invoiceChanged = ($submittedInvoiceNumber !== $currentInvoiceNumber);

    // ----- 4. Duplicate invoice-number check — only when changed -----
    if ($invoiceChanged && $submittedInvoiceNumber !== null && $submittedInvoiceNumber !== '') {
        $duplicate = Invoice::where('invoice_number', $submittedInvoiceNumber)
            ->where('id', '!=', $invoice->id)
            ->exists();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Can't update to the existing Invoice number. " .
                                "Invoice # '" . $submittedInvoiceNumber . "' already belongs to another record.");
        }

        // A reason must be supplied when the invoice number is being changed
        if (!$request->filled('invoice_change_reason')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A reason is required when changing the Invoice number.');
        }
    }

    // ----- 5. Persist the update inside a transaction -----
    DB::beginTransaction();
    try {
        $invoice->update([
            'client_id'          => $request->client_name,
            'created_date'       => $request->created_date,
            'estimate_reference' => $request->reference_no,       // ← was $request->reference (bug)
            'invoice_number'     => $request->invoice_number,     // now editable under unlock
            'lpo_no'         =>  $request->lpo_number,         // NEW
            'account_number'     => $request->account_number,
            'account_name'       => $request->account_name,
            'bank_name'          => $request->bank_name,
            'branch_name'        => $request->branch_name,
            'swift_code'         => $request->swift_code,
        ]);

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withInput()
            ->with('error', 'Update failed: ' . $e->getMessage());
    }

    // ----- 6. Audit log -----
    $newValues = $invoice->fresh()->toArray();

    if ($invoiceChanged && $submittedInvoiceNumber !== null && $submittedInvoiceNumber !== '') {
        // CRITICAL: invoice number was changed
        AuditLogger::log(
            'Invoice number changed',
            'update',
            'invoices',
            $invoice->id,
            $oldValues,
            $newValues,
            'Changed Invoice # from "' . ($currentInvoiceNumber ?: '—') .
                '" to "' . $submittedInvoiceNumber . '". ' .
                'Reason: ' . $request->invoice_change_reason,
            true,                                                     // is_critical
            $invoice->estimate_reference ?? null
        );
    } else {
        // Normal update
        AuditLogger::log(
            'Invoice details updated',
            'update',
            'invoices',
            $invoice->id,
            $oldValues,
            $newValues,
            'Updated invoice details for invoice #' . ($invoice->invoice_number ?? $invoice->id) . '.',
            false,
            $invoice->estimate_reference ?? null
        );
    }

    // ----- 7. Redirect -----
    return redirect()->back()->with('message', 'Invoice updated successfully!');
}

      public function getInvoicePDF($id) {
    
            $settings= DB::table('general_settings')->select('business_name', 'logo_file', 'type', 'address')->get();
       
              $sales = DB::table('estimations')
                          ->select('items.item_name', 'estimations.vat_amount', 
                              'estimations.account_no', 'estimations.account_prefix', 'brands.title', 
                              'invoices.invoice_number', 'products.purchase_unit',
                               'estimations.discount', 'invoices.bank_name', 'invoices.account_number',
                              'invoices.account_name', 'invoices.branch_name', 'invoices.swift_code', 
                              'estimations.vehicle_reg', 'estimations.reference', 'estimations.temesa_fee',
                              'estimations.model as emodel', 'estimations.make as emake', 'invoices.lpo_no',
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
      
           $temesa_fee =  ($sales[0]->temesa_fee ?? 0) * ($total_sales + $total_labours);

         if($sales[0]->vat_amount == '0') {
            $vat_charges = 0.00;
        } else {
            $vat_charges =  (0.18) * ($total_sales + $total_labours + $temesa_fee);
        }

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

  public function deleteJobCard($id)
    {
        $user    = Auth::user();
        $roleId  = (int) ($user->role_id ?? 0);
        $isAdmin = in_array($roleId, [1, 2], true);

        // ----- 1. Fetch the Job Card rows BEFORE deletion -----
        $jobRows = DB::table('job_cards')
            ->where('estimate_reference', $id)
            ->get();

        if ($jobRows->isEmpty()) {
            return redirect()->back()->with('error', 'Job Card not found.');
        }

        // ----- 2. Authorization — only role 1 or 2 may delete -----
        if (!$isAdmin) {
            return redirect()->back()->with('error', 'You are not authorized to delete this Job Card.');
        }

        // ----- 3. Snapshot estimations that will be reset -----
        $estimationRows = DB::table('estimations')
            ->where('reference', $id)
            ->where('branch_id', $user->branch_id)
            ->get();

        // ----- 4. Invoice guard (extra protection, admins still allowed) -----
        $hasInvoice = $estimationRows->contains(function ($r) {
            return !empty($r->invoice_number);
        });

        // ----- 5. Delete + reset inside a transaction -----
        DB::beginTransaction();
        try {
            $deleted = DB::table('job_cards')
                ->where('estimate_reference', $id)
                ->delete();

            DB::table('estimations')
                ->where('reference', $id)
                ->where('branch_id', $user->branch_id)
                ->update(['job_card_no' => null]);

            DB::commit();

            // ----- 6. Write the CRITICAL audit log -----
            $firstJob   = $jobRows->first();
            $jobCardNo  = $firstJob->job_card_no ?? '—';
            $clientName = $firstJob->client_name
                        ?? $firstJob->customer_name
                        ?? '—';
            $vehicle    = $firstJob->vehicle_reg ?? '—';

            $subject = 'Job Card deleted' . ($hasInvoice ? ' (with Invoice override)' : '');

            $description = 'Job Card "' . $jobCardNo . '" for reference "' . $id . '" ' .
                'was permanently deleted. Estimation rows were reset ' .
                '(job_card_no → NULL). Client: ' . $clientName . ', Vehicle: ' . $vehicle . '.';

            if ($hasInvoice) {
                $description .= ' ⚠ This Job Card had an Invoice attached — deleted via ' .
                    'administrator override (role ' . $roleId . ').';
            }

            AuditLogger::log(
                $subject,
                'delete',
                'job_cards',
                null,
                [                                  // old_values: full snapshot
                    'reference'          => $id,
                    'job_card_no'        => $jobCardNo,
                    'client'             => $clientName,
                    'vehicle_reg'        => $vehicle,
                    'had_invoice'        => $hasInvoice,
                    'job_cards_deleted'  => $deleted,
                    'job_cards'          => $jobRows->toArray(),
                    'estimations_reset'  => $estimationRows->toArray(),
                ],
                [],                                // new_values (empty on delete)
                $description,
                true,                              // is_critical = TRUE
                $id                                // reference
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Delete failed: ' . $e->getMessage());
        }

        // ----- 7. Redirect -----
        return redirect()->back()
            ->with('message', 'Job Card deleted successfully.');
    }


    public function exportJobCardPdf(Request $request)
    {
        $mode   = $request->query('mode', 'client');          // 'client' | 'vehicle'
        $value  = trim((string) $request->query('value', ''));
        $month  = (int) $request->query('month', 0);          // 1..12 or 0 = all
        $year   = (int) $request->query('year', 0);           // e.g. 2026 or 0 = all
        $inline = $request->boolean('inline', false);         // ← NEW

        if ($value === '') {
            return response()->json(
                ['error' => 'A client or vehicle must be selected before exporting.'],
                422
            );
        }

        $query = $this->jobCardsQuery();

        /* ---- Client / Vehicle filter ---- */
        if ($mode === 'vehicle') {
            $query->where('estimations.vehicle_reg', $value);
        } else {
            $query->where(function ($q) use ($value) {
                $q->where('clients.client_name', $value)
                  ->orWhere('estimations.customer_name', $value);
            });
        }

        /* ---- Month / Year filter (delivery date, falling back to created date) ---- */
        if ($month > 0) {
            $query->whereRaw(
                'MONTH(COALESCE(job_cards.delivery_date, estimations.created_date)) = ?',
                [$month]
            );
        }
        if ($year > 0) {
            $query->whereRaw(
                'YEAR(COALESCE(job_cards.delivery_date, estimations.created_date)) = ?',
                [$year]
            );
        }

        $sales = $query->orderByDesc('estimations.created_date')->get();

        /* ---- Branch name shown in the PDF header ---- */
        $branchName = optional(
            Branch::find(Auth::user()->branch_id)
        )->name ?? 'Head Office';

        /* ---- Meta strip shown at the top of the PDF ---- */
        $meta = [
            'mode'      => $mode,
            'value'     => $value,
            'month'     => $month,
            'year'      => $year,
            'generated' => now(),
            'total'     => $sales->count(),
            'completed' => $sales->filter(fn ($s) => (int) $s->status === 1)->count(),
            'ongoing'   => $sales->filter(fn ($s) => (int) $s->status === 0)->count(),
        ];

        $pdf = PDF::loadView('estimations.job-card-report', compact('sales', 'meta', 'branchName'));
                //   ->setPaper('a4', 'landscape');

        $filename = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $value) . '-'
                  . now()->format('Ymd_His')
                  . '.pdf';

        /* ---- inline (View) vs attachment (Download) ---- */
        if ($inline) {
            // Content-Disposition: inline → browser opens the PDF in a tab
            return $pdf->stream($filename, ['Attachment' => false]);
        }
        // Content-Disposition: attachment → triggers a download
        return $pdf->download($filename);
    }

    /* ============================================================
     |  SHARED QUERY  —  used by both summary + export
     | ============================================================ */
   private function jobCardsQuery()
{
    return DB::table('estimations')
        ->select([
            'estimations.reference',
            'estimations.created_date',
            'estimations.model',
            'estimations.customer_name',
            'estimations.vehicle_reg',
            'estimations.milleage',
            'estimations.job_card_no',
            'estimations.profoma_invoice as invoice_number',
            'job_cards.status',
            'job_cards.delivery_date',
            'clients.client_name',
            'clients.place',
        ])
        ->distinct()                                     // ← collapses line-item duplicates
        ->leftJoin('clients',   'clients.id',            '=', 'estimations.client_name')
        ->leftJoin('job_cards', 'job_cards.job_card_no', '=', 'estimations.job_card_no')
        ->where('estimations.branch_id', Auth::user()->branch_id)
        ->whereNotNull('estimations.job_card_no');
}
}
