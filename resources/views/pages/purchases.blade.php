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
            <a class="step" href="#"> Sales Report </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Sales Summary Report </h3>
                <br/>
                <div id="display_message" style="display: none"></div>
            </div>
            <div class="panel-body">
                <label for="product" class="col-sm-12 control-label">Filter </label><br/><br/>
              <div class="filter">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group m-t-10">
                        <select name="product_id" class="form-control select2" id="product_id">
                                 @foreach($products as $product)
                                   <option value="{{ $product->id }}"> {{ $product->product_name }}  -  ({{ $product->quantity }})</option>
                                 @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group m-t-10">
                                <div class="input-daterange input-group" id="date-range" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control" name="start" placeholder="Start Date" />
                                    <span class="input-group-addon b-0 text-white" style="background: palevioletred">to</span>
                                    <input type="text" class="form-control" name="end"  placeholder="End Date"/>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                                <!-- <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn"> Search </button> -->
                                <button name="filter_by_all" style="background: #E74C3C; color: white;" class="btn btn-rounded pull-right m-b-20 m-t-10" id="search"><span class="pull-left" style="padding: 5px;"> Search </span>
                                <span class="pull-right"> <i class="fa fa-arrow-circle-o-right fa-2x"></i></span></button>
                        </div>
                    </div>
                </div>
               </div><br/>

                <div class="row">
                    <div class="col-md-12">
                        <h4 align="center"> Sales Summary report </h4>
                    </div>
                </div>
                <div class="row">
                <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Item Name</th>
                                <th>Model</th>
                                <th> Client Name </th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->product_name }} </td>
                                <td> {{ $product->model }} </td>
                                <td> {{ $product->client_name }} </td>
                                <td>{{ $product->created_at}}</td>
                                <td>
                                    <i class="ti-eye"></i> 
                                    <i class="ti-pencil"></i> 
                                    <i class="ti-trash"></i> 
                                </td>       
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
             

               </div>
                </div><br/>
            </div>
        </div>
    </div>
</div>
<!-- END  -->


<script>
    $(document).ready(function () {

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
                     $('#display_results').html(response);
                }
            });
        })


        $("#save_btn").on('click', function () {

            var medicine_name = $("#medicine").val();
            var quantity = $("#quantity").val();
            var user_id = $("#user_id").val();
                $.ajax({
                    type: "POST",
                    url: "./ajax.php?f=processors/post_sales.php",
                    data: { medicine_name: medicine_name, quantity: quantity, user_id: user_id },
                    success: function(msg){
                        $('#display_message').html(msg);
                        $('input[type]="number", select', this).val('');
                    }
                });
        })
    });


</script>

@endsection