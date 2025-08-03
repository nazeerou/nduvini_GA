@extends('layouts.app_header')

@section('content')

<style>
  #input-detail {
      border: 1px solid red;
  }
</style>
<div class="row m-t-5">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="btn btn-sm btn-primary" href="{{ url('suppliers/payments') }}"> <i class="ti-arrow-left"></i>  Go Back</a> <br/>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h3> Purchase Details : </br></h3>
                 <h4 class="panel-title1"> 
                     <strong>
                    @foreach ($supplier_name as $w)
                         {{ $w->supplier_name }}    
                    @endforeach
                     </strong>
                     Invoice <b> #{{ $id }}  </b> </h4>
                <br/>
            </div>
        
            <div class="panel-body">
                    <!-- @if(session('message'))
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
                        @endif -->
                    <table id="datatable-fixed-header9" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Item Name</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Purchase Price</th>
                                <th>Qty </th>
                                <th>Invoice Amount </th>
                                <!-- <th>Action </th> -->
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($purchases as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->make }} </td>
                                <td> {{ $product->model }} </td>
                                <td> {{number_format( $product->purchase_price, 2) }} </td>
                                <td>{{ $product->quantity}}</td>
                                <td> {{number_format( $product->total_purchase, 2) }} </td>  
                                <!-- <td>
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn-rounded btn-sm  btn-info waves-effect waves-light add-detailss"> <i class="fa fa-edit"></i> Edit</button>
                                <a class="btn-rounded btn-sm btn-danger" href="{{ url('purchases/delete/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i> Delete </a>
                            </td>   -->
                            </tr>
                          @endforeach  
                               <!-- <tr>
                                 <td colspan="6"> <h5 align="right"> Total Invoice Amount &nbsp; : </h5> </td><td>  <h5> {{ number_format($total_purchases, 2) }} </h5> </td> 
                                </tr> -->
                               <tr>
                                 <td colspan="6" align="right"> 
                                      Sub Total Amount
                                    </td>
                                    <td>   
                                     {{ number_format($total_purchases,2) }}
                                    </td>
                                     <td></td>
                                </tr>
                                <tr>
                                 <td colspan="6" align="right">
                                      @if($product->vat_amount > 0) 
                                      VAT Amount (18%)
                                      @else
                                      VAT Amount (18%)
                                      @endif
                                    </td><td>  
                                          @if($product->vat_amount > 0)
                                          {{ number_format($vat_calculations, 2) }}
                                            @else
                                            {{ number_format($vat_calculations, 2) }} 
                                            @endif
                                      </td>
                                     <td></td>
                                </tr>
                                <tr>
                                 <td colspan="6" align="right">
                                      @if($product->vat_amount > 0) 
                                      Grand Total Amount
                                      @else
                                      Grand Total Amount
                                      @endif
                                    </td><td>   
                                        @if($product->vat_amount > 0)
                                          {{ number_format(($total_purchases + $vat_calculations), 2) }}
                                            @else
                                            {{ number_format($total_purchases, 2) }} 
                                            @endif
                                      </td>
                                     <td></td>
                                </tr>
                            </tbody>
                        </table>
            
                      </div>
                  </div>
            </div>
      </div>
</div>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="details_modal">
      <form id="formUpdate" method="post" action="{{ url('../purchases/payment') }}">
            @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Edit Purchase Item </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Item Name  : </label>
                                <input type="text" name="product_name" id="product" class="form-control" placeholder="Product Name" id="input-detail">
                                <input type="hidden" name="id"   id="id" class="form-control" placeholder="Product Name" id="input-detail">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make/Model  </label>
                                <input type="text" name="model" id="model" class="form-control" placeholder="">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Price  </label>
                                <input type="text" name="payment" id="payment" class="form-control" placeholder="Amount">
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light"> Update </button>
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

    $('.add-details').click(function() {
         var product_id = $(this).data('id');
         $.ajax({
             url: `../purchases/details/${product_id}`,
             type: "GET",
             data: {product_id: product_id},
             success: function (response) {
                 $('#details_modal').modal('show');
                 $("#product").val(response[0].item_name);
                 $("#payment").val(response[0].paid_amount);
                 $("#price").val(response[0].price);
                 $("#model").val(response[0].make+" - "+response[0].model);
                 $("#id").val(response[0].id);
             }
         });
    });

    $("#update_btn").on('click', function () {
            var id = $("#id").val();
            var quantity = $('#quantity').val();
            alert(id);
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