@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
</style>
<!-- whole sale setting -->
<br/>

<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> General Report </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> General Report </h3>
            </div>
            <div class="panel-body">
               <br/><br/>
        
                    <div class="row">
                        <div class="col-md-12">
                            <div id="display_results">
                            <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th> ID</th>
                                    <th>Supplier Name</th>
                                    <th> Total Amount </th>
                                    <th> Total Amount Paid </th>
                                    <th> Payment Status </th>
                                    <th> Action </th>
            
                                </tr>
                            </thead>
                            <tbody id="tbl_display">
                                @foreach($reports as $key=>$report)
                                <tr>
                                <td>{{ $key+1 }} </td>
                                <td>{{ $report->supplier_name }} </td>
                                <td>{{ number_format($report->total_purchase, 2) }} </td>
                                <td>{{ number_format(($report->total_purchase - $report->total_purchase), 2) }} </td>
                                <td> <span class="label label-warning">Not Paid</span>  </td>
                                <td>
                                <button type="button" name="submit" data-id="{{ $report->id }}" class="btn-rounded btn-success waves-effect waves-light show-details">Details </button>
                                <button type="button" name="submit" data-id="{{ $report->id }}" class="btn-rounded btn-info waves-effect waves-light show-details">Payment </button>
                                </td>
                                </tr>
                                @endforeach
                            </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
     
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

        $("#user").on('change', function () {

            var user_id = $("#user").val();

            console.log(user_id);

            $.ajax({
                type: "GET",
                url: "./ajax.php?f=processors/user_reports.php",
                data: "user_id="+user_id,
                success: function(response){
                    // var len = response.length;
                    // for(var i=0; i<len; i++){
                    //     var name = response[i].medicine_name;
                    //     var type = response[i].type;
                    //     var category = response[i].category;
                    //     var quantity = response[i].quantity;
                    //     var price = response[i].price;
                    //
                    //     var tr_str = "<tr>" +
                    //         "<td align='center'>" + (i+1) + "</td>" +
                    //         "<td align='center'>" + name + "</td>" +
                    //         "<td align='center'>" + type + "</td>" +
                    //         "<td align='center'>" + category + "</td>" +
                    //         "<td align='center'>" + quantity + "</td>" +
                    //         "<td align='center'>" + price + "</td>" +
                    //         "</tr>";
                    //
                    //     $("#display_results").append(tr_str);
                    // }
                     $('#display_result').html(response);
                }
            });
        })


        $("#search").on('click', function () {
            var startdate = $("#startdate").val();
            var enddate = $("#enddate").val();
                $.ajax({
                    type: "GET",
                    url: "/sales/search",
                    data: { startdate: startdate, enddate: enddate },
                    success: function(response){
                        //  $('#display_results').append(response.start_date);
                        $('#tbl_display').html(response);
                      }
                    });
                });
        });


</script>

@endsection