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
            <a class="step" href="#"> Stocks </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
            <h4 class="panel-title1">Current Stocks </h4>
            <div id="btn" style="float: right"> 
                <a href="{{ url('current-stocks/pdf') }}" target="_blank" class="btn btn-pink btn btn-sm waves-effect waves-light m-b-20 m-t-10"> <i class="fa fa-file-pdf-o"> </i> &nbsp; PDF  </a>
                 </div>
                <br/>
            </div>
        
            <div class="panel-body">
                    <table id="datatable-fixed-header" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Item Name</th>
                                <TH>Make </th>
                                <th>Model</th>
                                <th>Part number</th>
                                <th>Quantity </th>
                                <th>Purchase Unit </th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($stocks as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }} </td>
                                <td> {{ $product->model }} </td>
                                <td> {{ $product->part_number }} </td>
                                <td>{{ $product->quantity}}</td>
                                <td>{{ $product->purchase_unit}}</td>
                                <td>
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn-rounded btn-sm btn-info waves-effect waves-light show-details"><i class="fa fa-eye"></i> Details </button>
                                <button class="btn-rounded btn-primary btn-sm  waves-effect waves-light item_adjustment" data-id="{{ $product->id }}" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-plus"></i> Adjustment</button>
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

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;" id="item_adjustment_modal">
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

<!--  Modal content for the above example -->
        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="details_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Product Details </h4>
                    </div>
                    <div class="modal-body">
                    <div class="row m-t-0">
                   <div class="col-md-12">
                        <table class="table table-striped m-10">
                            <tr>
                             <td width="200px">Item Name </td><td width="2px">:</td><td id="item_name"></td>
                            </tr>
                            <tr>
                             <td>Part Number </td> <td width="2px">:</td><td id="part_number"></td>
                            </tr>
                            <tr>
                            <td>Make  </td> <td width="2px">:</td> <td id="title"></td>
                            </tr>
                            <tr>
                            <td>Model  </td> <td width="2px">:</td> <td id="model_id"></td>
                            </tr>
                            <tr>
                            <td>Purchasing Price  </td> <td width="2px">:</td> <td id="purchasing_price"></td>
                            </tr>
                            <tr>
                            <td>Selling Price  </td> <td width="2px">:</td> <td id="selling_price"></td>
                            </tr>
                            <tr>
                            <td>Quantity </td> <td width="2px">:</td> <td id="qty"></td>
                            </tr>
                            <tr>
                            <td>Purchase Unit  </td> <td width="2px">:</td> <td id="purchase_unit"></td>
                            </tr>
                            <tr>
                            <td>Total Purchases </td> <td width="2px">:</td> <td id="total_purchase"></td>
                            </tr>
                            <!-- <tr>
                            <td>Alert Quantity </td> <td width="2px">:</td> <td id="alert_quantity"></td>
                            </tr> -->
                            <tr>
                            <td> Description  </td> <td width="2px">:</td> <td id="description"></td>
                            </tr>
                          </table>
                        </div>
                    </div>
                </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
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
             url: `current-stocks/details/${product_id}`,
             type: "GET",
             success: function (response) {
                 $('#details_modal').modal('show');
                 $("#item_name").html(response[0].item_name);
                 $("#part_number").html(response[0].part_number);
                 $("#title").html(response[0].title);
                 $("#model_id").html(response[0].model);
                 $("#qty").html(response[0].quantity);
                 $("#purchasing_price").html(response[0].purchasing_price);
                 $("#selling_price").html(response[0].selling_price);
                 $("#purchase_unit").html(response[0].purchase_unit);
                 $("#total_purchase").html(response[0].total_purchase);
                 $("#alert_quantity").html(response[0].alert_quantity);
                 $("#description").html(response[0].description);
             }
         });
    });



    $('.item_adjustment').click(function() {
         var product_id = $(this).data('id');
         $.ajax({
             url: `products/adjustments/details/${product_id}`,
             type: "GET",
             success: function (response) {
                 $('#item_adjustment_modal').modal('show');
                 $("#product").val(response[0].item_name);
                 $("#qty_in_stock").val(response[0].quantity);
                 $("#quantity").val(response[0].quantity);
                 $("#sellingPrice").val(response[0].selling_price);
                 $("#purchasingPrice").val(response[0].purchasing_price);
                 $("#model").val(response[0].model);
                 $("#id").val(response[0].pid);
             }
         });
    });

    $("#update_btn").on('click', function () {
            var id = $("#id").val();
            var quantity = $('#quantity').val();
            var purchasing_price = $('#purchasing_price').val();
            var selling_price = $('#selling_price').val();
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
