@extends('layouts.app_header')

@section('content')

<style>
  #input-detail {
      border: 1px solid red;
  }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="btn btn-sm btn-info" href="{{ url('sales-summary') }}"> <i class="ti-arrow-left"></i> &nbsp; GO BACK</a> <br/>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right">
                <div id="btn" style="float: right"> 
                    </div>
                    <!-- <button class="btn btn-pink"> <i class="fa fa-print"> </i> Print </button>  -->
                 </div>
                 <h3> Sales Details  : </br></h3>
                 <h4 class="panel-title1"> 
                   Client :   <strong>
                    @foreach ($client_name as $w)
                         {{ $w->client_name }}    
                    @endforeach
                     </strong>
                     Vehicle Reg# : <b> #{{ strtoupper($id) }}  </b>
                     </h4>
                <br/>
            </div>
        
            <div class="panel-body">
                    <table id="datatable-fixed-header9" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Item Name</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th> Price</th>
                                <th>Qty </th>
                                <th>Amount </th>
                                <th>Action </th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }} </td>
                                <td> {{ $product->model }} </td>
                                <td> {{number_format( $product->selling_price, 2) }} </td>
                                <td>{{ $product->qty}}</td>
                                <td> {{number_format($product->total_sales, 2) }} </td>  
                                <td>
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn-rounded btn-sm  btn-info waves-effect waves-light edit-details"> <i class="fa fa-edit"></i> Edit</button>
                                <a class="btn-rounded btn-sm btn-danger" href="{{ url('sales-summary/sales-items/delete/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i> Delete </a>
                            </td>  
                            </tr>
                          @endforeach  
                                <tr>
                                 <td colspan="6" align="right">
                                      Sub Total Bill  :
                                    </td>
                                    <td>  
                                     {{ number_format($total_sales, 2) }} 
                                    </td>
                                     <td></td>
                                </tr>
                                <tr>
                                 <td colspan="6" align="right">
                                      VAT Amount (18%) &nbsp; :
                                    </td><td>  
                                          {{ number_format($vat_calculations, 2) }}
                                      </td>
                                     <td></td>
                                </tr>
                                <tr>
                                 <td colspan="6" align="right">
                                      Grand Total Bill Amount
                                      &nbsp; : 
                                    </td><td>  
                                          {{ number_format(($total_sales + $vat_calculations), 2) }}
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
      <form id="formUpdate" method="post" action="{{ url('../sales/update') }}">
            @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Edit Sale Item </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Item Name   </label>
                                <input type="text" name="product_name" id="product" class="form-control" readonly placeholder="Product Name">
                                <input type="hidden" name="id"   id="id" class="form-control" placeholder="Product Name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make  </label>
                                <input type="text" name="make" id="make" class="form-control" readonly placeholder="">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Model </label>
                                <input type="text" name="model" id="model" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-10">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Selling Price   </label>
                                <input type="number" name="selling_price" id="selling_price" readonly class="form-control" placeholder="Amount">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Quantity * </label>
                                <input type="number" name="quantity" id="qty" class="form-control" placeholder="Amount">
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn-rounded btn-success waves-effect waves-light"> Update </button>
                    </div>
                 </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->

        <!-- End Edit Modal  -->

<!--  Modal content for the above example -->
<div>
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="addModal">
      <!-- <form id="formUpdate" method="post" action="{{ url('../sales/update') }}"> -->
            @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Edit Sale Item </h4>
                    </div>
                    
                    <div class="modal-body">
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn-rounded btn-success waves-effect waves-light"> Update </button>
                    </div>
                 </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            <!-- </form> -->
        </div><!-- /.modal -->
</div>

<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit-details').click(function() {
         var sale_id = $(this).data('id');   
         $.ajax({
             url: `../../../sales/view-details/${sale_id}`,
             type: "GET",
             data: {sale_id: sale_id},
             success: function (response) {
                 console.log(response);
                 $('#details_modal').modal('show');
                 $("#product").val(response[0].item_name);
                 $("#selling_price").val(response[0].selling_price);
                 $("#qty").val(response[0].qty);
                 $("#model").val(response[0].model);
                 $("#make").val(response[0].title);
                 $("#id").val(response[0].id);
             }
         });
    });

    $("#addNew").click(function () {
            $('#addModal').modal('show');
        });
  });
</script>
@endsection