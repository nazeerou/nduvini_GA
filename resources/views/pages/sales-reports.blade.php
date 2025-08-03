@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Sales Report </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1"> Sales Report </h4>
            </div>
            <div class="panel-body">
                <label for="product" class="col-sm-12 control-label">Filter </label><br/><br/>
              <div class="filter">
              <form id="search_form" method="GET" action="{{ url('sales/reports/pdf') }}" target="_blank">
                  @csrf
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group m-t-10">
                                <div class="input-daterange input-group" id="date-range" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control" name="startdate" id="startdate" placeholder="Start Date" />
                                    <span class="input-group-addon b-0 text-white" style="background: palevioletred">to</span>
                                    <input type="text" class="form-control" name="enddate" id="enddate" placeholder="End Date"/>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                                <button type="submit" style="background: #E74C3C; color: white;" class="btn btn-rounded pull-right m-b-20 m-t-10" id="search"><span class="pull-left" style="padding: 5px;"> Search </span>
                                <span class="pull-right"> <i class="fa fa-arrow-circle-o-right fa-2x"></i></span></button>
                        </div>
                          </div>
                        </form>
                     </div>
                   </div><br/><br/>
                </div>
            </div>
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

        $("#searchs").on('click', function () {
            var startdate = $("#startdate").val();
            var enddate = $("#enddate").val();
                $.ajax({
                    type: "POST",
                    url: `/sales/search/`,
                    data: { startdate: startdate, enddate: enddate },
                    success: function(response){
                        //  $('#display_results').append(response.start_date);
                        // $('#tbl_display').html(response);

                      }
                    });
                });
        });


</script>

@endsection