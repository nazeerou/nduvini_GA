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
            <a class="step" href="#">Out of Stocks </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                 </div>
                 <h3 class="panel-title"> Item Out Of Stock  </h3>

                <br/>
            </div>
        
            <div class="panel-body">
                  
                    <table id="datatable-fixed-header" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Item Name</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Part number</th>
                                <th>Quantity </th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($out_stocks as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }} </td>
                                <td> {{ $product->model }} </td>
                                <td> {{ $product->part_number }} </td>
                                <td>{{ $product->quantity}}</td>
                                <td>
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn-rounded btn-info btn-sm waves-effect waves-light show-details"> <i class="fa fa-plus"></i> Adjustment </button>
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

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="details_modal">
<form id="formUpdate" method="post" action="{{ url('product/update') }}">
             @csrf
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="mySmallModalLabel">Item Adjustment</h4>
                </div>
                <div class="modal-body">
                <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Item Name  : </label>
                                <input type="text" name="product_name" disabled  id="product" class="form-control" placeholder="Product Name" id="input-detail">
                                <input type="hidden" name="id"   id="id" class="form-control" placeholder="Product Name">
                                <input type="hidden" name="selling_price"   id="sellingPrice" class="form-control" placeholder="">
                                <input type="hidden" name="purchasing_price"   id="purchasingPrice" class="form-control" placeholder="">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make/Model  </label>
                                <input type="text" name="model" disabled id="model" class="form-control" placeholder="">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                                <label for="inputEmail3" class="control-label">Stock Qty   </label>
                                <input type="text" name="qty_in_stock" id="qty_in_stock" class="form-control" readonly>
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">.</label>
                                <input type="text" name="new_qty" id="new_qty" class="form-control" size="20" placeholder="Add new qty">
                             </div>
                        </div>
                            <!-- <div class="form-group">
                                <label for="inputEmail3" class="control-label">Quantity  </label>
                                <input type="text" name="quantity" id="quantity" class="form-control" placeholder="">
                                <input type="hidden" name="qty_in_stock" id="qty_in_stock" class="form-control" placeholder="">
                             </div> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="" class="control-label">Reason  * </label>
                                <select name="reason_id" class="form-control select2" id="reason_id" required>
                                  <option value=""> Select Reason </option>
                                   @foreach($adjustments as $p)
                                   <option value="{{ $p->id }}"> {{ strtoupper($p->reason) }} </option>
                                   @endforeach
                             </select>        
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="update_btn">Update </button>
                    </div>
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
             url: `products/details/${product_id}`,
             type: "GET",
             success: function (response) {
                 $('#details_modal').modal('show');
                 $("#product").val(response[0].item_name);
                 $("#qty_in_stock").val(response[0].quantity);
                 $("#quantity").val(response[0].quantity);
                 $("#model").val(response[0].model);
                 $("#id").val(response[0].pid);
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