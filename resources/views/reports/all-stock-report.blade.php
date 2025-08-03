@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
    th {
        color: #fff;
    }
</style>
<!-- <div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> >>
            <a class="step" href="#"> Sales Report </a>
        </div>
    </div>
</div> -->
<div class="row m-t-10">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> All Stock Report   </h3>
            </div>
            <div class="panel-body">
                <!-- <label for="product" class="col-sm-12 control-label">Filter </label> -->
              <div class="filter">   
                <form action="{{ url('stocks/all-stock-reports') }}" method="GET" target="_blank">    
                @csrf           
                <div class="row">
                        <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Year   * </label>
                                <select name="year" class="form-control select2" id="year">
                                <option value=""> --- Select Year --- </option>
                                 @foreach($years as $year)
                                   <option value="{{ $year->id }}">  {{ $year->previous_year }} /  {{ $year->current_year  }}</option>
                                 @endforeach
                                </select>
                             </div>
                    </div>
                     <div class="col-md-2 m-t-10">
                        <div class="form-group">
                                <button type="submit" id="search" style="background: #E74C3C; color: white;" class="btn  btn-rounded pull-right m-b-0 m-t-5"><span class="pull-left" style="padding: 4px;"> GENERATE </span>
                                <span class="pull-right"> <i class="fa fa-arrow-circle-o-right fa-2x"></i></span></button>
                        </div>
                    </div>
                </div>
              </form>
               </div><br/><br/>
     
            <div id="container-box" style="display: none;">
            <!-- <a href="{{ url('stocks/view-report/pdf/') }}" target="_blank" class="btn btn-sm btn-pink pull-right"> <i class="fa fa-file-pdf-o"> </i> &nbsp; PDF  </a>  -->
             <div class="row m-t-30">
                 <div class="col-md-12">
                    <div class="card-box">
                   
                    </div> 
                   
                </div><!-- end col -->
             </div>
         </div> 
    </div>
<!-- END  -->


<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#product_id').on('change', function() {
         var product_id = $('#product_id').val();
             $.ajax({
                    type: "get",
                    url: "/product/fetch-assigned-items",
                    data: { product_id: product_id },
                    success: function(response){
                        $('#brand_id').html(response);
                    }
                });
            });
        
         $('#search').click(function() {
         var product_id = $('#product_id').val();
         var brand_id = $('#brand_id').val();
         $.ajax({
             url: `../sales/sales-report-by-item/`,
             data: { product_id: product_id, brand_id: brand_id },
             type: "GET",
             success: function (response) {
                $('#container-box').show();
                $("tbody").html(response);
             }
         });
    });
 });
</script>
@endsection