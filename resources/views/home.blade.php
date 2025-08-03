@extends('layouts.app_header')

@section('content')
<style>
    .highcharts-credits {
        display: none;
    }
    .highcharts-figure, .highcharts-data-table table {
    min-width: 360px; 
    max-width: 700px;
    margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #EBEBEB;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }
    .highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
    }
    .highcharts-data-table th {
        font-weight: 600;
    padding: 0.5em;
    }
    .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
    }
    .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
    }
    .highcharts-data-table tr:hover {
    background: #f1f7ff;
    }

    #view-link {
        color: #ddd;
    }
</style>
<br/>
<!-- @if(auth()->user()->is_admin == 1 OR auth()->user()->is_admin == 0) -->
<div class="card-bx">
    <div class="row">
        <div class="col-lg-3 col-md-3 p-r-0">
            <div class="card-box" style="height: 80px; background-color: #85C1E9; color: white;">
                <div class="widget-chart-1">
                    <div class="widget-detail" style="">
                        <p class="text-muteds" style="color: white;">
                            Total Inventory Purchases Value </p>
                            @foreach($total_inventory_purchases as $v) 
                            <h5 class="p-t-4 m-b-0" style="color: white;">
                                {{ number_format(($v->total_purchase_amount),2) }}
                        </h5>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 p-r-0">
            <div class="card-box" style="height: 80px; background-color: #ea4c89; color: white;">
                <div class="widget-chart-1">
                    <div class="widget-detail" style="">
                        <p class="text-muteds" style="color: white;">
                            Total Inventory Sales Value </p> 
                        <h5 class="p-t-4 m-b-0" style="color: white;">
                        {{ number_format(($total_inventory_sales_value), 2) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 p-r-0">
            <div class="card-box" style="height: 80px; background-color: #2E86C1; color: white;">
                <div class="widget-chart-1">
                    <div class="widget-detail" style="">
                        <p class="text-muteds" style="color: white;">
                            Stock < Min.Requirement </p>
                        <h5 class="p-t-4 m-b-0" style="color: white;">
                          {{ $less_stocks }}  <span style="float: right"> <a href="minimum-stock" id="view-link"> <i class="fa fa-arrow-circle-o-right"></i>View </a> </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-lg-3 col-md-3 p-r-0">
            <div class="card-box" style="height: 80px; background-color: #4755ad; color: white;">
                <div class="widget-chart-1">
                    <div class="widget-detail" style="">
                        <p class="text-muteds" style="color: white;">
                            Out of Stocks </p>
                        <h5 class="p-t-4 m-b-0" style="color: white;">
                           
                           {{ count($out_stocks) }} <span style="float: right;"> <a href="stock-out" id="view-link"> <i class="fa fa-arrow-circle-o-right"></i>View </a> </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
    </div>
  <!-- @else  -->

  
 <!-- @endif -->
 @if(auth()->user()->is_admin == 0 || 1)
<div class="row m-t-5">
    <div class="col-md-8"><h4 class="header-title m-t-0 m-b-2"
                              style="font-size: 16px; color: #000000;"> Filters </h4></div>
    <div class="col-md-4"><h4 class="header-title m-t-0 m-b-2"
                              style="font-size: 16px; color: #000000;"> Summary Report </h4></div>
</div>

<!-- start filter -->
<div class="row">
    <div class="col-md-9 col-lg-8 m-t-5">
        <div class="card-box" style="border: 4px solid #CCCCCC; height: 176px;">
            <div class="row m-t-15 m-l-15 m-r-15" style="padding-top: 2px;">
               
                <div class="col-md-6 col-sm-3">
                     Client Name 
                    <select name="supplier_name" class="form-control select2" id="supplier_name" >
                        <option value=""> ------- Search ----- </option>
                        @foreach($clients as $c)
                        <option value="{{ $c->id }}"> {{ $c->client_name }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1"> OR </div>
                <div class="col-sm-5">
                     LPO Number
                     <input type="text" name="lpo_number" id="vehicle_reg" class="form-control" placeholder="Enter LPO Number ">  
                </div>
            </div>
            <div class="row">

            </div>

        </div>
      </div><!-- end col -->


    <div class="card-bx">
    <div class="col-lg-4 col-md-6 m-t-5">
        <div class="card-box" style="height: 80px; background-color: #CB0266;">
            <div class="widget-chart-1">
                <div class="widget-chart-box-1" style="padding: 10px 30px;">
                    <span style="color: #7B013E;"> <i class="fa fa-arrow-circle-o-right"></i>  </span>
                </div>
                <div class="widget-detail-1" style="padding-right: 0px;">
                    <p class="text-muteds" style="color: white;">This Month's Purchases </p>
                    @foreach($month_purchases as $month)
                    <h5 class="m-b-0"
                        style="color: white;" id="">
                        {{ number_format(($month->total_month_purchases), 2) }}
                    </h5>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card-box" style="height: 80px; background-color: #D35400;">
            <div class="widget-chart-1">
                <div class="widget-chart-box-1" style="padding: 10px 30px;">
                    <span style="color: #7B013E;"><i class="fa fa-arrow-circle-o-right"></i> </span>
                </div>
                <div class="widget-detail-1"  style="padding-right: 0px;">
                    <p class="text-muteds" style="color: white;">  This Month's Sales </p>
                   @foreach($month_sales as $month)
                    <h5 class="p-t-4 m-b-0"
                        style="color: white;" id="">
                        {{ number_format(($month->total_month_sales), 2) }}
                    </h5>
                    @endforeach
                </div>
            </div>
        </div>
    </div><!-- end col -->
   </div>
</div>   <!-- END OF Dashboard  -->

<div class="row">
    <div class="col-md-8 pull-left">
        <button name="filter_by_all" style="width: 150px; background: #E74C3C; color: white;" class="btn btn-rounded pull-right" id="search"><span class="pull-left" style="padding: 5px;"> Search </span>
            <span class="pull-right"> <i class="fa fa-arrow-circle-o-right fa-2x"></i></span></button>
    </div>
    <div class="col-md-4"></div>
</div>


<!-- Graph -->

    <!-- CHART DIV START HERE -->
        
         <div id="container-box-1" style="display: none;">
             <div class="row m-t-30">
                 <div class="col-md-12x">
                    <div class="card-box">
                    <h4> CLIENT DETAILS : </h4>
                    <table id="datatable1" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>DATE CREATED</th>
                                <th>LPO NUMBER</th>
                                <th>INVOICE NUMBER </th> 
                                <th>INVOICE AMOUNT </th>
                                <th>PAID AMOUNT </th>
                                <th> UNPAID AMOUNT </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                     </table>
                    </div> 
                </div><!-- end col -->
             </div>
         </div> 


         <div id="container-box-2" style="display: none;">
             <div class="row m-t-30">
                 <div class="col-md-12x">
                    <div class="card-box">
                    <h4> CLIENT DETAILS : </h4>
                    <table id="datatable1" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>CLIENT NAME</th>
                                <th>BILL NUMBER</th>
                                <th> BILL AMOUNT </th> 
                                <th> PAID AMOUNT </th>
                                <th> OWED AMOUNT </th>
                                <th> DATE CREATED </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                     </table>
                    </div> 
                </div><!-- end col -->
             </div>
         </div> 
         @endif

<br/><br/>
        

<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#search').click(function() {
         var supplier_name = $('#supplier_name').val();
         var vehicle_reg = $('#vehicle_reg').val();
         
         if(supplier_name) {
            $.ajax({
             url: `home/filters-by-supplier-name`,
             data: { supplier_name: supplier_name },
             type: "GET",
             success: function (response) {
                $('#container-box-1').show();
                $('#container-box-2').hide();
                $("tbody").html(response);
             }
          });
         } else {
            $.ajax({
             url: `home/filters-by-vehicle`,
             data: { vehicle_reg: vehicle_reg },
             type: "GET",
             success: function (response) {
                $('#container-box-2').show();
                $('#container-box-1').hide();
                $("tbody").html(response);
             }
           });
         }
      });
  });
</script>
@endsection
