@extends('layouts.app_header')

@section('content')
<style>
    th {
  /* font-weight: 400; */
  background: #7093cc;
  color: #FFF;
  text-transform: uppercase;
  font-size: 0.8em;
  font-family: 'Raleway', sans-serif;
 }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Adjustment </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                 </div>
                 <h3 class="panel-title"> Product Adjustments  </h3>

                <br/>
            </div>
        
            <div class="panel-body">
                    @if(session('message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Item Name</th>
                                <th> Make </th>
                                <th>Model</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($adjustments as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }} </td>
                                <td> {{ $product->model }} </td>
                                <td>
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn-rounded btn-info btn-sm waves-effect waves-light show-details"><i class="fa fa-eye"></i> Adjustment History</button>
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
<!-- END  -->
<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="details_modal">
      <form id="formUpdate" method="post" action="{{ url('product/update') }}">
             @csrf
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Adjust History </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                           <div class="col-md-12">
                           <table id="userTable" class="table table-stripped" >
                                <thead>
                                    <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Reason</th>
                                    <th width="30%">In Stock Qty (From) </th>
                                    <th width="20%">Qty Updated (To) </th>
                                    <th width="20%">Updated At </th>
                                    </tr>
                                </thead>
                                <tbody id="display_results">
                                </tbody>
                            </table>
                           </div>
                       </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->


        <script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.show-details').click(function() {
         var product_id = $(this).data('id');
         $.ajax({
             url: `products/fetch-product-adjustments/${product_id}`,
             type: "GET",
             success: function (response) {
                $('#details_modal').modal('show');
                $("#display_results").html(response);
             }
         });
    });

        $("#update_btn").on('click', function () {
            var id = $("#id").val();
            var quantity = $('#quantity').val();
            $.ajax({
                    type: "get",
                    url: "/product/update",
                    data: { id: id, quantity: quantity },
                    success: function(msg){
                        $('#display_message').html(msg);
                        swal({
                            title: "Success!",
                            text: "You have Updated Item",
                            type: "success",
                            confirmButtonClass: "btn-success",
                        });

                        $('#formUpdate')[0].reset();

                    }
                });
            });
        });
</script>
@endsection