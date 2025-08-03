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
            <a class="btn btn-sm btn-" href="{{ url('sales-summary') }}"> <i class="ti-arrow-left"></i> &nbsp; GO BACK</a> <br/>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                  <div>
                  <button type="button" class="btn-rounded btn-info btn-md addNew" data-name="Add New" data-toggle="modal"> + Add New</button>
                  </div>
                </div>
                 <h3> Sales Details  : </br></h3>
                 <h4 class="panel-title1"> 
                   Client :   <strong>
                    @foreach ($client_name as $w)
                         {{ $w->client }}   {{ $w->place }}  
                    @endforeach
                     </strong>
                    
                     </h4>
                <!-- <br/> -->
                <div style="float: right">
                    <a href="{{ url('sales/views/pdf/'.$id) }}" target="_blank" class="btn btn-sm btn-pink"> <i class="fa fa-file-pdf-o"> </i> &nbsp; PDF  </a> 
                 </div> <br/>
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
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn btn-sm  btn-info waves-effect waves-light edit-details"> <i class="fa fa-edit"></i> </button>
                                <a class="btn btn-sm btn-danger" href="{{ url('sales-summary/sales-items/delete/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i>  </a>
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

  <!-- Modal -->
      <div class="modal fade bs-example-modal-lg" id="myModal" role="dialog">
            <div class="modal-dialog">
               <!-- Modal content-->
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                     <h4 class="modal-title">Add Sale</h4>
                  </div>
                  <div class="modal-body">
                  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Item Name   * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id">
                                <option value=""> --- Select Item --- </option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->pid }}">  {{ $product->item_name }}  - {{ $product->model }}  :  ({{ $product->quantity }}) </option>
                                 @endforeach
                                </select>
                             </div>
                         </div>
                  </div>

     <div id="client_details" style="padding: 0px; border: 0px solid #ddd;">
         <div class="row m-t-20">
        <div class="col-md-12">
        <form id="formPost" method="POST" action="{{ url('/pos/add') }}" enctype="multipart/form-data"> 
                  @csrf
         <input type="hidden" name="reference" id="reference" value="{{ $id }}" class="form-control">
        <section id='Table'>
        <table class="table table-striped table-bordered" style="background: #fff;" id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="300px">Item Name</th>
                    <th width="100px"> Price</th>
                    <th width="10">Qty</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='dataRows'></tbody>
            <!-- <tfooter style="display: none;" >
             <tr">
                 <td></td>
                 <td style="text-align: right">Total Amount : </td>
                 <td colspan=""><span id="totalCost">0</span></td> <td></td><td></td>
            </tr>
           </tfooter> -->
              </table>

            </section>
            </div>
            </div>  
           </div>

                <div class="row m-t-10">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save  </button>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>
              </div>
              </div>
         </form> <!-- End form -->
        </div>
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

  // Initializing our modal.
  $('#myModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false,
    });

    $(document).on("click", ".addNew", function() {
        $('#myModal').modal('show');
    });


    $("#myTable").on('click','.btnDelete',function(){
         $(this).closest('tr').remove();
      });


      $("#labour_charge").change( function(){
        if($(this).val() == '1') {
            $("#dataRow2").show();
        } else {
            $('#dataRow2').hide();
        }
      });


        $("#product_id").on('change', function () {
            var product_id = $("#product_id").val();
            $.ajax({
                type: "GET",
                url: "../../products/details/"+product_id,
                data: { product_id: product_id },
                success: function(response){
                    $("#dataRows").show();
                    var html = '<tr>';
                    html += '<td>'+'<input type="hidden" name="id[]" id="product_id" value="'+response[0].pid+'">'+'</td>';
                    html += '<td width="100px">'+response[0].item_name+'</td>';
                    html += '<td>'+'<input type="text" name="selling_price[]" id="selling_price[]" value="'+response[0].selling_price+'">'+'</td>';
                    html += '<td>'+'<input type="text" name="qty[]" class="form-" size="5" id="qty[]" onkeyup="calculateItemsValue()" required>'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows').append(html);
                }
            });
        });

    function calculate_price() {
        var price = 0;
        $(".qty").each(function() {
            rowindex = $(this).closest('tr').index();
            qty =  $(this).val();
            selling_price = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .selling_price').val();
            price += qty * unit_price;
        });
        $('#totalCost').html(price);
    }

 function calculateItemsValue() {
    var total = 0;
    for (i=1; i<=total_items; i++) {
         
        itemID = document.getElementById("qty"+i);
        price = document.getElementById("selling_price"+i);

        if (typeof itemID === 'undefined' || itemID === null) {
            alert("No such item - " + "qty_"+i);
        } else {
            total = total + parseInt(itemID.value) * parseInt(price.value);
        }
         
    }
    document.getElementById("totalCost").innerHTML = "TZS" + total;  
}

        $("#save_btn").on('click', function () {
            var product_id = $("#product_id").val();
            var qty = $("#qty").val();
            var vehicle_reg = $('#vehicle_reg').val();
            var client_name = $("#client_name").val();
            var bill_no = $("#bill_no").val();
            var created_date = $(".created_date").val();
            // if(qty == ''){
            //     swal({
            //         title: "Warning!",
            //         text: "Quantity should not be empty!",
            //         type: "warning",
            //         confirmButtonClass: "btn-danger",
            //     });
            //  } else if(created_date == ''){
            //     swal({
            //         title: "Warning!",
            //         text: "Date Should not be Empty! ",
            //         type: "warning",
            //         confirmButtonClass: "btn-danger",
            //     });
            //  } 
            //  else {
                $.ajax({
                        type: "POST",
                        url: "/pos/create",
                        data: { product_id: product_id, qty: qty, bill_no: bill_no, vehicle_reg: vehicle_reg, client_name: client_name, created_date: created_date },
                        success: function(response){
                            $('#display_message').html(response);
                            swal({
                                title: "Success!",
                                text: "You have Added New Sale",
                                type: "success",
                                confirmButtonClass: "btn-success",
                            });

                            $('input[type]="text", select', this).val('');
                            $('#formPost')[0].reset();
                            setTimeout(function() 
                            {
                                location.reload();  //Refresh page
                            }, 1000)
                        }
                });
        //    }
    });

    function CalculateItemsValue() {
    var total = 0;
    for (i=1; i<=total_items; i++) {
         
        itemID = document.getElementById("qnt_"+i);
        if (typeof itemID === 'undefined' || itemID === null) {
            alert("No such item - " + "qnt_"+i);
        } else {
            total = total + parseInt(itemID.value) * parseInt(itemID.getAttribute("data-price"));
        }
         
    }
    document.getElementById("ItemsTotal").innerHTML = "$" + total;
     
}

  });
</script>
@endsection