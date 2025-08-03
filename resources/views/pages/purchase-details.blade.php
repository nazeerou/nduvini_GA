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
            <a class="btn btn-sm btn-primary" href="{{ url('purchase-history') }}"> <i class="ti-arrow-left"></i>  Go Back</a> <br/>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right">
                    <a href="{{ url('purchase-report/data/'.$id) }}" target="_blank" class="btn btn-pink"> <i class="fa fa-file-pdf-o"> </i> &nbsp; PDF  </a> 
                 </div>
                 <h3> Purchase Details : </br></h3>
                 <h4 class="panel-title1"> 
                     <strong>
                         {{ $supplier_name[0]->supplier_name }}    
                     </strong>
                     Invoice <b> #{{ $id }}  </b> </h4>
                <br/>
            </div>
        
            <div class="panel-body">
                    <table id="datatable-fixed-header9" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Part Name</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Part Number</th>
                                <th>Unit Price</th>
                                <th>Qty </th>
                                <th>Sub total </th>
                                <th>Action </th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($purchases as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->make }} </td>
                                <td> {{ $product->model }} </td>
                                <td> {{ $product->part_number }} </td>
                                <td> {{number_format( $product->purchase_price, 2) }} </td>
                                <td>{{ $product->quantity}}</td>
                                <td> {{number_format( $product->total_purchase, 2) }} </td>  
                                <td>
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn-rounded btn-sm  btn-info waves-effect waves-light edit-details"> <i class="fa fa-edit"></i> Edit</button>
                                <a class="btn-rounded btn-sm btn-danger" href="{{ url('purchases/items/delete/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i> Delete </a>
                            </td>  
                            </tr>
                          @endforeach  
                               <tr>
                                 <td colspan="7"> <h5 align="right">
                                      @if($product->vat_type > 0) 
                                      Sub Total Amount
                                      &nbsp; : </h5>
                                      @else
                                      Sub Total Amount
                                      @endif
                                    </td><td>  
                                        <h5> 
                                        @if($product->vat_type > 0)
                                          {{ number_format($total_purchases,2) }}
                                            @else
                                            {{ number_format($total_purchases, 2) }} 
                                            @endif
                                      </h5></td>
                                     <td></td>
                                </tr>
                                <tr>
                                 <td colspan="7"> <h5 align="right">
                                      @if($product->vat_type > 0) 
                                      VAT Amount (18%)
                                      &nbsp; : </h5>
                                      @else
                                      VAT Amount (18%)
                                      @endif
                                    </td><td>  
                                        <h5> 
                                          @if($product->vat_type == 1)
                                          {{ number_format($vat_calculations, 2) }}
                                            @else
                                            {{ number_format($vat_calculations, 2) }} 
                                            @endif
                                      </h5></td>
                                     <td></td>
                                </tr>
                                <tr>
                                 <td colspan="7"> <h5 align="right">
                                      @if($product->vat_type > 0) 
                                      Grand Total Amount
                                      &nbsp; : </h5>
                                      @else
                                      Grand Total Amount
                                      @endif
                                    </td><td>  
                                        <h5> 
                                        @if($product->vat_type > 0)
                                          {{ number_format(($total_purchases + $vat_calculations), 2) }}
                                            @else
                                            {{ number_format($total_purchases, 2) }} 
                                            @endif
                                      </h5></td>
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
      <form id="formUpdate" method="post" action="{{ url('purchases/update') }}">
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
                                <input type="text"  id="product" readonly class="form-control">
                                <input type="hidden" name="id"   id="id" class="form-control">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make  </label>
                                <input type="text" name="make" id="make" class="form-control" placeholder="">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Model  </label>
                                <input type="text" name="model" id="model" class="form-control" placeholder="">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Purchase Price  </label>
                                <input type="number" name="purchase_price" id="purchase_price" class="form-control" placeholder="Amount">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Quantity  </label>
                                <input type="text" name="quantity" id="qty" class="form-control" placeholder="">
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" class="btn-rounded btn-primary waves-effect waves-light" id="update_btn"> Update </button>
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

    $('.edit-details').click(function() {
         var product_id = $(this).data('id');
         $.ajax({
             url: `../../purchases/view-details/${product_id}`,
             type: "GET",
             data: {product_id: product_id},
             success: function (response) {
                 console.log(response);
                 $('#details_modal').modal('show');
                 $("#product").val(response[0].item_name);
                 $("#qty").val(response[0].quantity);
                 $("#purchase_price").val(response[0].purchase_price);
                 $("#make").val(response[0].make);
                 $("#model").val(response[0].model);
                 $("#id").val(response[0].id);
             }
         });
    });

    $(".deleteBtn").click(function() {
            // e.preventDefault();
            var product_id = $(this).data('id');
            alert(product_id);

        swal({
            title: "Attention",
            text: "Veuillez confirmer la suppression",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Confirmer",
            cancelButtonText: "Annuler",
        }).then((result) => {
            if (result.value) {
                $('#del_type').submit();
            } else {
                swal('cancelled');
            }
        });
    });
        

    $("#update_btn1").on('click', function () {
            var id = $("#purchase_id").val();
            var prouct_id = $("#product_id").val();
            var make = $('#make').val();
            var model = $('#model').val();
            var quantity = $('#qty').val();
            var purchase_price = $('#purchase_price').val();
            $.ajax({
                    type: "POST",
                    url: "/purchases/update",
                    data: { id: id, product_id: product_id, make: make, model: model, purchase_price: purchase_price, quantity: quantity },
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