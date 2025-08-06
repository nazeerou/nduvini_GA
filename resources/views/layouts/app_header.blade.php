<?php 
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Module;

    $alert_qty = DB::table('products')
                    ->select('products.quantity', 'products.alert_qty')
                    ->whereColumn('products.quantity', '<=', 'products.alert_qty')
                    ->where('products.branch_id', session('current_branch_id', Auth::user()->branch_id))
                    ->count();
    $less_stocks = DB::table('products')
                    ->select('products.pid as id', 'products.quantity', 'products.model', 'items.item_name', 'brands.title', 'products.alert_qty')
                    ->join('items', 'items.id', 'products.product_id')
                    ->join('brands', 'brands.id', 'products.brand_id')
                    ->whereColumn('products.quantity', '<=', 'products.alert_qty')
                    ->where('products.branch_id', session('current_branch_id', Auth::user()->branch_id))
                    ->get();

    $settings = DB::table('general_settings')
                    ->select('business_name', 'type')
                   ->get();

    $branches = DB::table('branches')
            ->select('branches.branch_name')
            ->where('branches.id', session('current_branch_id', Auth::user()->branch_id))
            ->get();

    $role = Role::findOrFail(Auth::user()->role_id);        
    $modules = Module::with('permissions')->get();
    $assigned_permissions = $role->permissions->pluck('id')->toArray();

    ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="shortcut icon" href="{{ asset('assets/images/logo_nduvini.jpeg') }}">
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


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
    /* width: 15px; */
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

@keyframes rotateHover {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(15deg); }
}

@keyframes pulseIcon {
    0%, 100% { transform: scale(1); }
    50%      { transform: scale(1.1); }
}

.nav-icon-rounded {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    /* background-color:rgb(104, 138, 164); */
    transition: all 0.3s ease;
    /* padding: 0 5px 0 10px !important; */
    /* color:rgb(83, 152, 248); */
}

.nav-icon-rounded { 
   margin: 0 6px 0 0;
}

li:hover .nav-icon-rounded {
    background-color:rgb(60, 129, 233);
    color: #fff;
    animation: rotateHover 0.3s ease;
}

.permission-link:hover i {
    animation: pulseIcon 0.4s ease;
    /* background-color: #ddd; */
}

#sidebar-menu ul li a i {
    display: inline-block !important;
    font-size: 16px !important;
    line-height: 17px !important;
    margin-left: 0px !important;
    margin-right: 0px !important;
    text-align: center !important;
    vertical-align: middle !important;
    /* width: 20px !important; */
}

#sidebar-menu > ul > li > a {
    /* color: #435966; */
    display: block;
    padding: 6px 20px !important;
    margin: 0px 0px;
    background-color: #ffffff;
    border-left: 3px solid transparent;
}

</style>
    </head>

    <body class="fixed-left">

        <!-- Begin page -->
        <div id="wrapper"> 
            <!-- Top Bar Start -->
            <div class="topbar"> 
                <!-- LOGO -->
                <div class="topbar-left">
              <a href="" class="logo"><span>{{ $settings[0]->business_name }}<span></span></span><i class="zmdi zmdi-layers"></i></a>
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
                                    
                                    Branch :  {{ $branches[0]->branch_name ?? ''}}
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
                        <h5><a href="#"> {{ Auth::user()->fname }} {{ Auth::user()->lname }} </a> </h5>
                        <ul class="list-inline">
                            <li>
                                <a href="{{ url('/user-profile') }}" >
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
                              <a href="/home" class="waves-1">
                              <span class="nav-icon-rounded">
                                <i class="fa fa-tachometer-alt"></i>
                                </span>
                             <span> Dashboard </span> </a>
                          </li>

                                @foreach ($modules as $module)
                                @php
                                    // Check if the user has any permissions for this module
                                    $moduleChecked = collect($module->permissions)->pluck('id')->intersect($assigned_permissions)->isNotEmpty();
                                @endphp
                                @if ($moduleChecked)
                                <li class="has_sub">
                                    <a class="has_sub ai-icon d-flex align-items-center gap-2 px-3 py-2" href="javascript:void(0);" aria-expanded="false">
                                    <span class="nav-icon-rounded">
                                        {!! $module->icon_link !!}
                                    </span>
                                    <span class="nav-text">{{ ucfirst($module->name) }}</span>
                                    <span class="ms-auto menu-arrow"></span>
                                </a>
                                <ul class="list-unstyled ps-2 mt-1">
                                    @foreach ($module->permissions as $permission)
                                        @if (in_array($permission->id, $assigned_permissions))
                                            <li>
                                                <a href="{{ is_string($permission->route) ? url($permission->route) : '#' }}"
                                                class="d-flex align-items-center gap-2 px-2 py-1 rounded permission-link">
                                                    <i class="ti-arrow-circle-right text-primary"></i>
                                                    {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                            @endif
                        @endforeach
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

                <!-- <footer class="footer text-right">
                 All right reserved. ©  <?php echo date('Y'); ?>  
                </footer> -->

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