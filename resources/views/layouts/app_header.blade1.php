<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="shortcut icon" href="assets/images/icon.jpg">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Garage Assistant System (GAS) </title>
        <link href="{{ url('assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css" />
        <!-- DataTables -->
        <link href="{{ url('assets/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/buttons.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/fixedHeader.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/datatables/scroller.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!--Morris Chart CSS -->
        <link href="{{ url('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/core.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/components.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/pages.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/menu.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/css/responsive.css') }}" rel="stylesheet" type="text/css" />
        <!-- <link href="assets/css/custom_style.css" rel="stylesheet" type="text/css" /> -->
        <link href="{{ url('assets/plugins/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- <link rel="stylesheet" href="lobipanel/lib/jquery-ui.min.css"/> -->
        <!-- <link rel="stylesheet" href="lobipanel/bootstrap/dist/css/bootstrap.min.css"/> -->
        <!-- <link rel="stylesheet" href="lobipanel/dist/css/lobipanel.min.css"/> -->
        <link href="{{ url('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
        <link href="{{ url('assets/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
        <link href="{{ url('assets/plugins/bootstrap-sweetalert/sweet-alert.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/multiselect/css/multi-select.css') }}"  rel="stylesheet" type="text/css" />
        <link href="{{ url('assets/plugins/select2/dist/css/select2.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ url('assets/plugins/select2/dist/css/select2-bootstrap.css') }}" rel="stylesheet" type="text/css">

        <link rel="stylesheet" href="{{ url('assets/plugins/morris/morris.css') }}">
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>

        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	

       <style>
    .step-indicator {
    margin-bottom: 20px;
    line-height: 30px;
}

.step {
    display: block;
    float: left;
    font-weight: bold;
    background: #f9f9f9;
    padding-right: 10px;
    height: 30px;
    line-height: 32px;
    margin-right: 33px;
    position: relative;
    text-decoration: none;
    color: #666;
    cursor: default;
}
.step:before {
    content: "";
    display: block;
    width: 0;
    height: 0;
    position: absolute;
    top: 0;
    left: -30px;
    border: 15px solid transparent;
    border-color: #f9f9f9;
    border-left-color: transparent;
}
.step:after {
    content: "";
    display: block;
    width: 0;
    height: 0;
    position: absolute;
    top: 0;
    right: -30px;
    border: 15px solid transparent;
    border-left-color: #f9f9f9;
}
.step:first-of-type {
    border-radius: 2px 0 0 2px;
    padding-left: 15px;
}
.step:first-of-type:before {
    display: none;
}
.step:last-of-type {
    border-radius: 0 2px 2px 0;
    margin-right: 0px;
    padding-right: 15px;
}
.step:last-of-type:after {
    display: none;
}
.step.completed {
    background: #5bc0de;
    color: #fff;
    cursor: pointer;
}
.step.completed:before {
    border-color: #5bc0de;
    border-left-color: transparent;
}
.step.completed:after {
    border-left-color: #5bc0de;
}
.step.completed:hover {
    background: #41A4F0;
    border-color: #41A4F0;
    color: #fff;
    text-decoration: none;
}
.step.completed:hover:before {
    border-color: #41A4F0;
    border-left-color: transparent;
}
.step.completed:hover:after {
    border-left-color: #41A4F0;
}

.slimScrollBar {
background: rgb(187, 187, 187);
    width: 15px;
    position: absolute;
    top: 232px;
    opacity: 0.4;
    display: none;
    border-radius: 7px;
    z-index: 99;
    right: 1px;
    height: 195.422px;
    visibility: visible;
}
.profile-pic{
    position: absolute;
    height:120px;
    width:120px;
    left: 50%;
    transform: translateX(-50%);
    top: 0px;
    z-index: 1001;
    padding: 1px;
}
.profile-pic img{
   
    border-radius: 50%;
    box-shadow: 0px 0px 5px 0px #c1c1c1;
    cursor: pointer;
    width: 100px;
    height: 100px;
}   

.profile-pic {
     max-width: 100px;
    max-height: 100px;
    display: block; 
}
#sidebar-menu  {
    font-size: 1.0em;
    /* font-family: "Century Gothic"; */
}
.side-menu h5 {
    font-size: 0.9em;
    font-family: "Century Gothic";   
}
#sidebar-menu .list-unstyled {
    font-size: 0.9em;
    font-family: "Century Gothic";
}
.topbar-left .logo {
    font-size: 0.8em;
    font-family: "Century Gothic";
}
.notification-box .badge {
    margin-bottom: 30px;
}
.notification-box .zmdi-notifications-none {
    margin-left: 0px;
}
.notification-box ul li a {
    font-size: 18px;
    /* color: #435966; */
    display: block;
    line-height: 70px;
}
.logo {
    font-size: 1.5em;
}
</style>
    </head>

    <body class="fixed-left">
    <?php 
         $alert_qty = DB::table('products')
                        ->select('products.quantity', 'products.alert_qty')
                        ->whereColumn('products.quantity', '<=', 'products.alert_qty')
                        ->count();
       $less_stocks = DB::table('products')
                        ->select('products.pid as id', 'products.quantity', 'products.model', 'items.item_name', 'brands.title', 'products.alert_qty')
                        ->join('items', 'items.id', 'products.product_id')
                        ->join('brands', 'brands.id', 'products.brand_id')
                        ->whereColumn('products.quantity', '<=', 'products.alert_qty')
                        ->get();
      $settings = DB::table('general_settings')
                 ->select('business_name', 'type')
                ->get();

      $branches = DB::table('users')
                ->select('branch_name')
                ->join('branches', 'branches.id', 'users.branch_id')
                ->WHERE('users.branch_id', Auth::user()->branch_id)
               ->get();
     
    ?>
        <!-- Begin page -->
        <div id="wrapper"> 
            <!-- Top Bar Start -->
            <div class="topbar">
                <!-- LOGO -->
                <div class="topbar-left">
              <a href="" class="logo"><span>{{ $settings[0]->business_name }} <span></span></span><i class="zmdi zmdi-layers"></i></a>
                </div>
                <!-- Button mobile view to collapse sidebar menu -->
                <div class="navbar navbar-default" role="navigation" style="background: white;">
                    <div class="container">
                        <!-- Page title -->
                        <ul class="nav navbar-nav navbar-left">
                            <li>
                                <button class="button-menu-mobile open-left">
                                    <i class="zmdi zmdi-menu"></i>
                                </button>
                            </li>
                            <li>
                                <h4 class="page-title"><i class="zmdi zmdi-view-dashboard"></i> &nbsp;  </h4> 
                            </li>
                        </ul>

                        <!-- Right(Notification and Searchbox -->
                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <!-- Notification -->
                                <div class="notification-box">
                                    <ul class="list-inline m-b-0">
                                    <li> Logged In : {{ Auth::user()->fname }} {{ Auth::user()->lname }} <br/>
                                    Branch :  {{ $branches[0]->branch_name }}
                                    </li>
                                    <li>
                                            <a href="javascript:void(0);" class="right-bar-toggle">
                                                <i class="zmdi zmdi-notifications-none"></i>
                                                 <span class="badge badge-danger">{{ $alert_qty }}</span>
                                            </a>
                                            <div class="noti-dot">
                                                <span class="dot"></span>
                                                <!-- <span class="pulse"></span> -->
                                            </div>
                                        </li>
                                    <li>
                                        <span class="hidden-xs">  </span> 
                                    </li>
                                    </ul>
                                </div>
                                <!-- End Notification bar -->
                            </li>
                        </ul>

                    </div><!-- end container -->
                </div><!-- end navbar -->
            </div>
            <!-- Top Bar End -->
            <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <div class="sidebar-inner slimscrollleft">
                <!-- User -->
                <div class="user-box" style="padding: 0px 0px 0px 0px; border-bottom: 1px solid #eee;">
                            <div class="user-img">
                            @if(!Auth::user()->profile_img)
                            <img src="{{ url('assets/images/users/profile.jpg') }}" alt="User profile" title="User" class="img-circle img-thumbnail img-responsive profile-pic">
                            @else
                            <img src="{{ url('/attachments/'.Auth::user()->profile_img) }}" alt="User profile" title="User" class="img-circle img-thumbnail img-responsive profile-pic">     
                            @endif
                           <!-- <div class="user-status offline"><i class="zmdi zmdi-dot-circle"></i></div> -->
                        </div>
                        <h5><a href="#"> {{ Auth::user()->fname }} {{ Auth::user()->lname }}</a> </h5>
                        <ul class="list-inline">
                            <li>
                                <a href="{{ url('user-profile') }}" >
                                    <i class="zmdi zmdi-settings"></i>
                                </a>
                            </li>

                            <li>
                                <a class="user-button" title="logout" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                       <i class="fa fa-power-off"></i>
                             </a>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
								@csrf
							</form>
                            </li>
                        </ul>
                    </div>
                    <!-- End User -->
                    <!--- Sidemenu -->

                    <div id="sidebar-menu">
                        <ul>
                          <li>
                              <a href="/home" class="waves-effect"><i class="zmdi zmdi-view-dashboard"></i> <span> Dashboard </span> </a>
                          </li>
                          <?php
                            $role = DB::table('roles')->find(Auth::user()->role_id);
                            $index_permission = DB::table('permissions')->where('name', 'estimations-index')->first();
                            $estimations_index_permission_active = DB::table('role_has_permissions')->where([
                                ['permission_id', $index_permission->id],
                                ['role_id', $role->id]
                            ])->first();
                            ?>
                          @if ($estimations_index_permission_active)
                          <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="ti-shopping-cart-full"></i> <span> Estimates  </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                     <?php 
                                        $create_estimates_permission = DB::table('permissions')->where('name', 'create-estimates')->first();
                                        $create_estimates_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $create_estimates_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    ?>
                                    @if ($create_estimates_permission_active)
                                    <li><a href="{{ url('create-estimations') }}"><i class="ti-arrow-circle-right"></i>Create Estimates </a></li>
                                    @endif
                                    <?php 
                                        $estimations_history_permission = DB::table('permissions')->where('name', 'estimations-history')->first();
                                        $estimations_history_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $estimations_history_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    ?>
                                     @if ($estimations_history_permission_active)
                                    <li><a href="{{ url('estimations-history') }}"><i class="ti-arrow-circle-right"></i>Estimations History</a></li>
                                    @endif
                                    <li><a href="{{ url('return-jobs') }}"><i class="ti-arrow-circle-right"></i> Return JOBS (RJ) </a></li>
                                </ul>
                              </li>
                            @endif
                                  <?php 
                                        $job_cards_invoices_permission = DB::table('permissions')->where('name', 'job-cards-invoices-index')->first();
                                        $job_cards_invoices_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $job_cards_invoices_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $job_cards_permission = DB::table('permissions')->where('name', 'all-job-cards')->first();
                                        $job_cards_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $job_cards_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $invoices_permission = DB::table('permissions')->where('name', 'all-invoices')->first();
                                        $invoices_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $invoices_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    ?>
                            @if ($job_cards_invoices_permission_active)
                              <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="ti-shopping-cart-full"></i> <span> Job Cards & Invoices </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                @if ($job_cards_invoices_permission_active)
                                    <li><a href="{{ url('job-cards') }}"><i class="ti-arrow-circle-right"></i>All Job Cards</a></li>
                                @endif
                                @if ($invoices_permission_active)
                                    <li><a href="{{ url('invoices/all-invoices') }}"><i class="ti-arrow-circle-right"></i>All Invoices </a></li>
                                @endif
                                </ul>
                              </li>     
                              @endif    

                            <?php 
                                        $purchases_permission = DB::table('permissions')->where('name', 'purchases-index')->first();
                                        $purchases_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $purchases_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $create_purchases_permission = DB::table('permissions')->where('name', 'create-purchases')->first();
                                        $create_purchases_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $create_purchases_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $purchase_history_permission = DB::table('permissions')->where('name', 'purchases-history')->first();
                                        $purchase_history_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $purchase_history_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    ?>
                            @if ($purchases_permission_active)
                              <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="ti-bag"></i> <span> Purchases </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                    @if ($create_purchases_permission_active)
                                    <li><a href="{{ url('create-purchases') }}"><i class="ti-arrow-circle-right"></i>Create Purchase </a></li>
                                    @endif
                                    @if ($purchase_history_permission_active)
                                    <li><a href="{{ url('purchase-history') }}"><i class="ti-arrow-circle-right"></i>Purchase History</a></li>
                                   @endif
                                </ul>
                            </li>
                            @endif
                            <?php 
                                        $inventory_index_permission = DB::table('permissions')->where('name', 'inventory-index')->first();
                                        $inventory_index_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $inventory_index_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $current_stocks_permission = DB::table('permissions')->where('name', 'current-stocks')->first();
                                        $current_stocks_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $current_stocks_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $price_sheet_permission = DB::table('permissions')->where('name', 'price-sheet')->first();
                                        $price_sheet_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $price_sheet_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $adjustment_history_permission = DB::table('permissions')->where('name', 'adjustment-history')->first();
                                        $adjustment_history_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $adjustment_history_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    ?>

                            @if ($inventory_index_permission_active)
                            <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="ti-layers-alt"></i> <span> Inventory </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                    @if ($current_stocks_permission_active)
                                    <li><a href="{{ url('current-stocks') }}"><i class="ti-arrow-circle-right"></i>Current Stock</a></li>
                                    @endif
                                    @if ($price_sheet_permission_active)
                                    <li><a href="{{ url('price-lists') }}"><i class="ti-arrow-circle-right"></i>Price Sheet</a></li>
                                    @endif 
                                    @if ($adjustment_history_permission_active)
                                    <li><a href="{{ url('/item-adjustments') }}"><i class="ti-arrow-circle-right"></i>Adjustment History</a></li>
                                    @endif
                                </ul>
                            </li>
                            @endif 

                            <?php 
                                        $customers_permission = DB::table('permissions')->where('name', 'customers-index')->first();
                                        $customers_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $customers_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $clients_permission = DB::table('permissions')->where('name', 'clients')->first();
                                        $clients_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $clients_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $suppliers_permission = DB::table('permissions')->where('name', 'suppliers')->first();
                                        $suppliers_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $suppliers_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    ?>
                            @if ($customers_permission_active)
                            <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-users"></i> <span> Customers </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                @if ($clients_permission_active)
                                <li><a href="{{ url('clients') }}"> <i class="ti-arrow-circle-right"></i> Clients List</a></li>
                                @endif 
                                @if ($suppliers_permission_active)
                                <li><a href="{{ url('suppliers') }}"> <i class="ti-arrow-circle-right"></i> Suppliers List</a></li>
                                 @endif    
                            </ul>
                            </li>
                            @endif

                                   <?php 
                                        $stocks_permission = DB::table('permissions')->where('name', 'stocks-index')->first();
                                        $stocks_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $stocks_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $stocks_parts_permission = DB::table('permissions')->where('name', 'stocks-parts')->first();
                                        $stocks_parts_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $stocks_parts_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $labour_settings_permission = DB::table('permissions')->where('name', 'labour-settings')->first();
                                        $labour_settings_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $labour_settings_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    ?>
                            
                            @if ($stocks_permission_active)
                            <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="ti-agenda"></i> <span> Stocks </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                    @if ($stocks_parts_permission_active)
                                    <li><a href="{{ url('parts') }}"><i class="ti-arrow-circle-right"></i>Stocks / Parts</a></li>
                                     @endif
                                     @if ($labour_settings_permission_active)
                                    <li><a href="{{ url('labours') }}"><i class="ti-arrow-circle-right"></i>Labour Settings</a></li>
                                     @endif
                                </ul>
                            </li>
                            @endif

                            
                            <?php 
                                        $accountings_permission = DB::table('permissions')->where('name', 'accountings-index')->first();
                                        $accountings_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $accountings_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $petty_cash_permission = DB::table('permissions')->where('name', 'petty-cash')->first();
                                        $petty_cash_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $petty_cash_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $clients_payments_permission = DB::table('permissions')->where('name', 'clients-payments')->first();
                                        $clients_payments_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $clients_payments_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $suppliers_payments_permission = DB::table('permissions')->where('name', 'suppliers-payments')->first();
                                        $suppliers_payments_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $suppliers_payments_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    
                                        $account_statement_permission = DB::table('permissions')->where('name', 'account-statement')->first();
                                        $account_statement_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $account_statement_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $account_list_permission = DB::table('permissions')->where('name', 'account-list')->first();
                                        $account_list_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $account_list_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                     $petty_cash_categories_permission = DB::table('permissions')->where('name', 'petty-cash-categories')->first();
                                     $petty_cash_categories_permission_active = DB::table('role_has_permissions')->where([
                                        ['permission_id', $petty_cash_categories_permission->id],
                                        ['role_id', $role->id]
                                    ])->first();
                            ?>
                             @if ($accountings_permission_active)
                            <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="fa fa-bank"></i> <span> Accounting </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                    @if ($petty_cash_permission_active)
                                     <li><a href="{{ url('accounts/expenditures/petty-cash') }}"><i class="ti-arrow-circle-right"></i> Petty Cash </a></li>
                                    @endif 
                                    @if ($clients_payments_permission_active)
                                     <li><a href="{{ url('clients/payments') }}"><i class="ti-arrow-circle-right"></i> Clients Payments</a></li>
                                     @endif 
                                     @if ($suppliers_payments_permission_active)
                                     <li><a href="{{ url('suppliers/payments') }}"><i class="ti-arrow-circle-right"></i>Suppliers Payments</a></li>
                                     @endif  
                                     @if ($account_list_permission_active)
                                     <li><a href="{{ url('accounts/account-list') }}"><i class="ti-arrow-circle-right"></i>Account List</a></li>
                                     @endif
                                     @if ($account_statement_permission_active)
                                     <li><a href="{{ url('accounts/account-statements/') }}"><i class="ti-arrow-circle-right"></i>Account Statement</a></li>
                                     @endif
                                     @if ($petty_cash_categories_permission_active)
                                     <li><a href="{{ url('accounts/petty-cash/settings') }}"><i class="ti-arrow-circle-right"></i>Petty Cash Settings</a></li>
                                     @endif
                                    </ul>
                            </li>
                           @endif
                           <?php 
                                        $reports_permission = DB::table('permissions')->where('name', 'reports-index')->first();
                                        $reports_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $reports_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $clients_statements_permission = DB::table('permissions')->where('name', 'clients-statements')->first();
                                        $clients_statements_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $clients_statements_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $suppliers_statements_permission = DB::table('permissions')->where('name', 'suppliers-statements')->first();
                                        $suppliers_statements_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $suppliers_statements_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $petty_cash_reports_permission = DB::table('permissions')->where('name', 'petty-cash-reports')->first();
                                        $petty_cash_reports_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $petty_cash_reports_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $purchases_reports_permission = DB::table('permissions')->where('name', 'purchases-reports')->first();
                                        $purchases_reports_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $purchases_reports_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    
                                        $annual_purchases_permission = DB::table('permissions')->where('name', 'annual-purchases-reports')->first();
                                        $annual_purchases_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $annual_purchases_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $annual_sales_permission = DB::table('permissions')->where('name', 'annual-sales-reports')->first();
                                        $annual_sales_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $annual_sales_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                                    ?>
                            @if ($reports_permission_active)
                            <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="ti-bar-chart"></i> <span> Reports </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                    @if ($clients_statements_permission_active)
                                    <li><a href="{{ url('accounts/client-statements') }}"><i class="ti-arrow-circle-right"></i>Clients Statement</a></li>
                                    @endif
                                    @if ($suppliers_statements_permission_active)
                                    <li><a href="{{ url('accounts/suppliers-statements') }}"><i class="ti-arrow-circle-right"></i>Suppliers Statement</a></li>
                                    @endif
                                    @if ($petty_cash_reports_permission_active)
                                    <li><a href="{{ url('accounts/petty-cash-reports') }}"><i class="ti-arrow-circle-right"></i>PETTY Cash Reports </a></li>
                                    @endif 
                                   @if ($purchases_reports_permission_active)
                                    <li><a href="{{ url('purchase-reports') }}"><i class="ti-arrow-circle-right"></i>Purchases Reports</a></li>
                                    @endif 
                                    @if ($annual_purchases_permission_active)
                                    <li><a href="{{ url('reports/annual-sales') }}"><i class="ti-arrow-circle-right"></i>Annual Sales</a></li>
                                    @endif 
                                    @if ($annual_sales_permission_active)
                                    <li><a href="{{ url('reports/annual-purchases') }}"><i class="ti-arrow-circle-right"></i>Annual Purchases</a></li>
                                    @endif
                                </ul>
                            </li>
                           @endif

                         <?php 
                                        $settings_permission = DB::table('permissions')->where('name', 'settings-index')->first();
                                        $settings_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $settings_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $product_settings_permission = DB::table('permissions')->where('name', 'products-settings')->first();
                                        $product_settings_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $product_settings_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $system_settings_permission = DB::table('permissions')->where('name', 'system-settings')->first();
                                        $system_settings_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $system_settings_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                            ?>

                        @if ($settings_permission_active)
                           <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-gear"></i><span> Settings </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                    @if ($product_settings_permission_active)
                                    <li><a href="{{ url('settings/product') }}"><i class="ti-arrow-circle-right"></i>Product Settings</a></li>
                                    @endif 
                                    @if ($system_settings_permission_active)
                                    <li><a href="{{ url('settings/system') }}"><i class="ti-arrow-circle-right"></i> System Settings</a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    
                        <?php 
                                        $manage_users_permission = DB::table('permissions')->where('name', 'manage-users')->first();
                                        $manage_users_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $manage_users_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $users_permission = DB::table('permissions')->where('name', 'users')->first();
                                        $users_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $users_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $roles_permissions_permission = DB::table('permissions')->where('name', 'roles-permissions')->first();
                                        $roles_permissions_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $roles_permissions_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();

                                        $activity_logs_permission = DB::table('permissions')->where('name', 'activity-logs')->first();
                                        $activity_logs_permission_active = DB::table('role_has_permissions')->where([
                                            ['permission_id', $activity_logs_permission->id],
                                            ['role_id', $role->id]
                                        ])->first();
                            ?>

                            @if ($manage_users_permission_active)
                            <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-user-group"></i><span> Manage Users </span> <span class="menu-arrow"></span></a>
                                <ul class="list-unstyled">
                                    @if ($users_permission_active)
                                    <li><a href="{{ url('users') }}"><i class="ti-arrow-circle-right"></i>Users </a></li>
                                    @endif
                                    @if ($roles_permissions_permission_active)
                                    <li><a href="{{ url('roles') }}"><i class="ti-arrow-circle-right"></i>Roles & Permission </a></li>
                                    @endif 
                                    @if ($activity_logs_permission_active)
                                    <li><a href="{{ url('settings/activity_logs') }}"><i class="ti-arrow-circle-right"></i> Activity Logs </a></li>
                                     @endif
                                </ul>
                            </li>
                            @endif
                            <hr/>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <!-- Sidebar -->
                    <div class="clearfix"></div>

                </div>

            </div>
            <!-- Left Sidebar End -->
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- Right Sidebar -->
            <div class="side-bar right-bar">
                <a href="javascript:void(0);" class="right-bar-toggle">
                    <i class="zmdi zmdi-close-circle-o"></i>
                </a>
                <h4 class="">Notifications</h4>
                <div class="notification-list nicescroll">
                    <ul class="list-group list-no-border user-list">
                        @foreach($less_stocks as $s)
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="user-desc">
                                    <span class="name">{{ $s->item_name }}</span>
                                    <span class="desc">Item exceeds alert qty  </span>
                                </div>
                            </a>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </div>
            <!-- /Right-bar -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">

                           @yield('content')

                    </div> <!-- container -->

                </div> <!-- content -->

                <footer class="footer text-right">
                 All right reserved. ©  <?php echo date('Y'); ?>  
                </footer>

            </div>

            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->


            <!-- Right Sidebar -->
            <!-- <div class="side-bar right-bar">
                <a href="javascript:void(0);" class="right-bar-toggle">
                    <i class="zmdi zmdi-close-circle-o"></i>
                </a>
                <h4 class="">Notifications</h4>
                <div class="notification-list nicescroll">
                    <ul class="list-group list-no-border user-list">
                        <li class="list-group-item">
                            <a href="#" class="user-list-item">
                                <div class="icon bg-pink">
                                    <i class="zmdi zmdi-comment"></i>
                                </div>
                                <div class="user-desc">
                                    <span class="name"> </span>
                                    <span class="time">1 day ago</span>
                                </div>
                            </a>
                        </li>

                    </ul>
                </div>
            </div> -->
            <!-- /Right-bar -->

        </div>
        <!-- END wrapper -->

        <script>
            var resizefunc = [];
        </script>

        <script src="{{ url('assets/js/modernizr.min.js') }}"></script>

        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/js/bootstrap.min.js') }}"></script>
        <script src="{{ url('assets/js/detect.js') }}"></script>
        <script src="{{ url('assets/js/fastclick.js') }}"></script>

        <script src="{{ url('assets/js/jquery.slimscroll.js') }}"></script>
        <script src="{{ url('assets/js/jquery.blockUI.js') }}"></script>
        <script src="{{ url('assets/js/waves.js') }}"></script>
        <script src="{{ url('assets/js/wow.min.js') }}"></script>
        <script src="{{ url('assets/js/jquery.nicescroll.js') }}"></script>
        <script src="{{ url('assets/js/jquery.scrollTo.min.js') }}"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

        <!-- Toastr js -->
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
        <!-- Datatables-->
        <script src="{{ url('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.bootstrap.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/jszip.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.fixedHeader.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/responsive.bootstrap.min.js') }}"></script>
        <script src="{{ url('assets/plugins/datatables/dataTables.scroller.min.js') }}"></script>

        <!-- Plugins Js -->
        <script src="{{ url('assets/plugins/switchery/switchery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
        <script type="text/javascript" src="{{ url('assets/plugins/multiselect/js/jquery.multi-select.js') }}"></script>
        <script type="text/javascript" src="{{ url('assets/plugins/jquery-quicksearch/jquery.quicksearch.js') }}"></script>
        <script src="{{ url('assets/plugins/select2/dist/js/select2.min.js') }}" type="text/javascript"></script>
        <script src="{{ url('assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js') }}" type="text/javascript"></script>
        <script src="{{ url('assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js') }}" type="text/javascript"></script>
        <script src="{{ url('assets/plugins/moment/moment.js') }}"></script>
        <script src="{{ url('assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{ url('assets/plugins/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
        <script src="{{ url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ url('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ url('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}" type="text/javascript"></script>

        <!-- Datatable init js -->
        <script src="{{ url('assets/pages/datatables.init.js') }}"></script>

        <!-- KNOB JS -->
        <!--[if IE]>
        <script type="text/javascript" src="assets/plugins/jquery-knob/excanvas.js"></script>
        <![endif]-->
        <script src="{{ url('assets/plugins/jquery-knob/jquery.knob.js') }}"></script>

        <!--Morris Chart-->

        <!-- Dashboard init -->

        <!-- App js -->
        <script src="{{ url('assets/js/jquery.core.js') }}"></script>
        <script src="{{ url('assets/js/jquery.app.js') }}"></script>


        <!-- <script src="lobipanel/lib/jquery.1.11.min.js"></script>
        <script src="lobipanel/lib/jquery-ui.min.js"></script>
        <script src="lobipanel/bootstrap/dist/js/bootstrap.min.js"></script> -->
        <!-- <script src="lobipanel/dist/js/lobipanel.min.js"></script> -->

        <script src="{{ url('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

        <!-- Sweet Alert js -->
        <script src="{{ url('assets/plugins/bootstrap-sweetalert/sweet-alert.min.js') }}"></script>
        <script src="{{ url('assets/pages/jquery.sweet-alert.init.js') }}"></script>

        <script src="{{ url('assets/pages/jquery.sweet-alert.init.js') }}"></script>

        <script>
            jQuery(document).ready(function() {

                //advance multiselect start
                $('#my_multi_select3').multiSelect({
                    selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                    selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
                    afterInit: function (ms) {
                        var that = this,
                            $selectableSearch = that.$selectableUl.prev(),
                            $selectionSearch = that.$selectionUl.prev(),
                            selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                            selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

                        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                            .on('keydown', function (e) {
                                if (e.which === 40) {
                                    that.$selectableUl.focus();
                                    return false;
                                }
                            });

                        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                            .on('keydown', function (e) {
                                if (e.which == 40) {
                                    that.$selectionUl.focus();
                                    return false;
                                }
                            });
                    },
                    afterSelect: function () {
                        this.qs1.cache();
                        this.qs2.cache();
                    },
                    afterDeselect: function () {
                        this.qs1.cache();
                        this.qs2.cache();
                    }
                });

                // Select2
                $(".select2").select2();

                $(".select2-limiting").select2({
                    maximumSelectionLength: 2
                });

            });

            // Time Picker
            jQuery('#timepicker').timepicker({
                defaultTIme : false
            });
            jQuery('#timepicker2').timepicker({
                showMeridian : false
            });
            jQuery('#timepicker3').timepicker({
                minuteStep : 15
            });

            //colorpicker start

            $('.colorpicker-default').colorpicker({
                format: 'hex'
            });
            $('.colorpicker-rgba').colorpicker();

            // Date Picker
            jQuery('#datepicker').datepicker();
            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            jQuery('#datepicker-inline').datepicker();
            jQuery('#datepicker-multiple-date').datepicker({
                format: "mm/dd/yyyy",
                clearBtn: true,
                multidate: true,
                multidateSeparator: ","
            });
            jQuery('#date-range').datepicker({
                toggleActive: true
            });

            //Date range picker
            $('.input-daterange-datepicker').daterangepicker({
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-default',
                cancelClass: 'btn-primary'
            });
            $('.input-daterange-timepicker').daterangepicker({
                timePicker: true,
                format: 'MM/DD/YYYY h:mm A',
                timePickerIncrement: 30,
                timePicker12Hour: true,
                timePickerSeconds: false,
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-default',
                cancelClass: 'btn-primary'
            });
            $('.input-limit-datepicker').daterangepicker({
                format: 'MM/DD/YYYY',
                minDate: '06/01/2016',
                maxDate: '06/30/2016',
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-default',
                cancelClass: 'btn-primary',
                dateLimit: {
                    days: 6
                }
            });

            $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));

            $('#reportrange').daterangepicker({
                format: 'MM/DD/YYYY',
                startDate: moment().subtract(29, 'days'),
                endDate: moment(),
                minDate: '01/01/2016',
                maxDate: '12/31/2016',
                dateLimit: {
                    days: 60
                },
                showDropdowns: true,
                showWeekNumbers: true,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                opens: 'left',
                drops: 'down',
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-success',
                cancelClass: 'btn-default',
                separator: ' to ',
                locale: {
                    applyLabel: 'Submit',
                    cancelLabel: 'Cancel',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom',
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    firstDay: 1
                }
            }, function (start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            });

            //Bootstrap-MaxLength
            $('input#defaultconfig').maxlength()

            $('input#thresholdconfig').maxlength({
                threshold: 20
            });

            $('input#moreoptions').maxlength({
                alwaysShow: true,
                warningClass: "label label-success",
                limitReachedClass: "label label-danger"
            });

            $('input#alloptions').maxlength({
                alwaysShow: true,
                warningClass: "label label-success",
                limitReachedClass: "label label-danger",
                separator: ' out of ',
                preText: 'You typed ',
                postText: ' chars available.',
                validate: true
            });

            $('textarea#textarea').maxlength({
                alwaysShow: true
            });

            $('input#placement').maxlength({
                alwaysShow: true,
                placement: 'top-left'
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#datatable').dataTable();
                $('#datatable-keytable').DataTable( { keys: true } );
                $('#datatable-responsive').DataTable();
                $('#datatable-scroller').DataTable( { ajax: "assets/plugins/datatables/json/scroller-demo.json", deferRender: true, scrollY: 380, scrollCollapse: true, scroller: true } );
                var table = $('#datatable-fixed-header').DataTable( { fixedHeader: true } );
            } );
            TableManageButtons.init();

        </script>

            <script>
            @if(Session::has('message'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.success("{{ session('message', 'title') }}");
            @endif

            @if(Session::has('error'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.error("{{ session('error') }}");
            @endif

            @if(Session::has('info'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.info("{{ session('info') }}");
            @endif

            @if(Session::has('warning'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.warning("{{ session('warning') }}");
            @endif
            </script>
        </body>
        </html>
