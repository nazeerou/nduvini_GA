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
            <a class="btn btn-sm btn-" href="{{ url('estimations-history') }}"> <i class="ti-arrow-left"></i> &nbsp; GO BACK</a> <br/>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
           
    <div class="panel-heading">
    <div class="panel-body">
                 <h3> Estimation Cost : </br></h3>
                 <h4 class="panel-title1"> 
               @if($client_name[0]->client != NULL)
                 CLIENT NAME : <strong>  {{ $client_name[0]->client }}</strong><br/>
                @else 
                    {{ $sales[0]->customer_name }}                
                @endif    
                <br/>
                VEHICLE REG# : <strong>  {{ $sales[0]->vehicle_reg }} </strong>
                </h4>
                <!-- <br/> -->
                <div style="float: right">
                    <a href="{{ url('estimations/details/pdf/'.$id) }}" target="_blank" class="btn btn-sm btn-pink"> <i class="fa fa-file-pdf-o"> </i> &nbsp; Create Profoma  </a> 
                    <button type="button" class="btn btn-success btn-sm" data-id="{{ $id }}" id="editProfoma" data-name="edit-proforma" data-toggle="modal" data-target="#editProfomaModal">Edit Proforma</button>
                     <a href="{{ url('mails/proforma-invoice/'.$client_name[0]->client.'/'.$id) }}" class="btn btn-sm btn-primary"> <i class="fa fa-envelope"> </i> &nbsp; Send </a>
                </div> <br/>
            </div>
<div class="container table-responsive">
<div class="row">
        <div class="col-md-10">
        <div id="btn" style="float: right"> 
            <button type="button" class="btn btn-success btn-sm addNew" data-name="Add New " data-toggle="modal"> + Add Part </button>
        </div>
</div>
</div>
<div class="row">
        <div class="col-md-10">
               <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Parts</th>
                                <th>Qty </th>
                                <th>Unit</th>
                                <th>D (%) </th>
                                <th>Sub Total </th>
                                <th>Action </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td width="400px"> {{ $product->item_name }} </td>
                                <td>{{ $product->qty}}</td>
                                <td> {{number_format( $product->selling_price, 2) }} </td>
                                <td> {{ $product->discount ? : ''  }} {{ $product->discount ? '%' : ''  }}    </td>
                                <td> {{number_format($product->total_sales, 2) }} </td>   
                                <td>  
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn btn-sm  btn-info waves-effect waves-light edit-part_details" data-target="#myModal"> <i class="fa fa-edit"></i> </button>
                                <a class="btn btn-sm btn-danger" href="{{ url('/estimations/delete-estimations/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i>  </a>
                             </td>
                            </tr>
                          @endforeach  
                    </tbody>
                    </table>
</div>
</div>
<br/>
<div class="row">
        <div class="col-md-10">
        <div id="btn" style="float: right"> 
            <button type="button" class="btn btn-success btn-sm addLabour" data-name="Add More" data-toggle="modal"> + Add Labour </button>
        </div>
</div>
</div>
    <div class="row">
        <div class="col-md-10">
                    <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                            <th>Labour </th>
                                <th>Rate/Hr </th>
                                <th>Unit </th>
                                <th>D </th>
                                <th>Sub Total </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($labours as $key => $l)
                            <tr>
                                <td width="660px"> {{ $l->labour_name }} </td>
                                <td> {{ $l->qty }} </td>
                                <td> {{ $l->charge }} </td>
                                <td> </td>
                                <td> {{number_format(($l->total_amount), 2) }} </td> 
                                <td> 
                                <!-- <button type="button" name="submit" data-id="{{ $l->id }}" class="btn btn-sm  btn-info waves-effect waves-light edit-labour"> <i class="fa fa-edit"></i> </button> -->
                                <a class="btn btn-sm btn-danger" href="{{ url('estimations/labours/delete/'.$l->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i>  </a>   
                               </td>     
                            </tr>
                          @endforeach  
                    </tbody>
            </table>
            </div>
    </div>
    <br/>
<div class="row">
<div class="col-md-6"></div>
<div class="col-md-4">
                        <table id="total_estimate"  class="table table table-bordered" style="color: black">
                           <thead>
                           @if($total_discounts != NULL)
                               <tr>
                                 <td> <strong> Net Discount : </strong> </td>
                                    <td> {{ number_format($total_discounts, 2) }} </td>
                                </tr>
                                @else  {{ '' }} @endif 
                                 <tr>
                                 <tr>
                                 <td> Parts : </td>
                                    <td> {{ number_format($total_sales, 2) }} </td>
                                </tr>
                                <tr>
                                 <td> Labour : </td>
                                    <td> {{ number_format($total_labours, 2) }} </td>
                                </tr>
                                <tr>
                                 <td>
                                     <strong> Sub Total : </strong>
                                    </td>
                                    <td width="100px"> {{ number_format(($total_sales + $total_labours), 2) }} </td>
                                </tr>
                                 @if (!empty($temesa_fee) && $temesa_fee > 0)
                                    <tr>
                                        <td>TEMESA Fee (8%):</td>
                                        <td>{{ number_format($temesa_fee, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                 <td>
                                      VAT Amount (18%)  :
                                    </td><td>  
                                          {{ number_format($vat_charges, 2) }}
                                      </td>
                                </tr>
                                <tr style="background: #eee">
                                 <td>
                                     <strong> Total Estimate  </strong>
                                      &nbsp; : 
                                    </td><td>  
                                          {{ number_format(($grand_total_amount), 2) }}
                                      </td>  
                            </tr>
                     </thead>
              </table>
              
        <br/><br/><br/>
</div>
                    
                   
            </div>
      </div>
</div>


  <!-- Modal -->
      <div class="modal modal-xl fade bs-example-modal-lg" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true"  id="myModal" role="dialog">
            <div class="modal-dialog modal-lg">
               <!-- Modal content-->
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                     <h4 class="modal-title">Add Spare Parts </h4>
                  </div>
                  <div class="modal-body">
                  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Client    * </label>
                                <select name="client[]" class="form-control select2" id="client" >
                                <option value=""> --- General --- </option>
                                @foreach($clients as $client)
                                   <option value="{{ $client->id }}">  {{ $client->client_name }}  </option>
                                 @endforeach
                                </select>
                             </div> 
                          </div>
                   <div class="col-md-7"></div>
               </div>
                  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Part Name   * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id">
                                <option value=""> --- Select Parts --- </option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->id }}">  {{ $product->item_name }}  -{{ $product->title }} | {{ $product->model }}  :  ({{ $product->quantity }}) </option>
                                 @endforeach
                                </select>
                             </div>
                         </div>
                  </div>

     <div id="client_details" style="padding: 0px; border: 0px solid #ddd;">
         <div class="row m-t-20">
        <div class="col-md-12">
        <form id="formPost" method="POST" action="{{ url('/estimations/add-more-parts') }}" enctype="multipart/form-data"> 
                  @csrf
         <input type="hidden" name="reference" id="reference" value="{{ $id }}" class="form-control">
         <input type="hidden" name="client_id"  value="{{ $client_name[0]->client_name }}" class="form-control">
        <section id='Table'>
        <table class="table table-striped table-bordered" style="background: #fff;" id="myTable1">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="300px">Part Name</th>
                    <th width="100px"> D (%)</th>
                    <th width="100px"> Price</th>
                    <th width="10">Qty</th>
                    <th width="100px"> Sub Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='dataRows'></tbody>
           </table>
          </section>
        </div>
      </div>  
    </div>
</div>
    <!-- <div class="row m-t-10">
        <div class="col-md-2">
            <div class="form-group">
                <label for="inputEmail3" class="control-label"> </label>
                    <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save  </button>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>
    </div> -->
    <div class="modal-footer">
            <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
            <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save  </button>
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
<div class="modal fade modal-xl bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="details_modal">
      <form id="formUpdate" method="post" action="{{ url('../estimations/update-part-details') }}">
            @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Edit Part Details </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Part Name    </label>
                                <input type="text" name="product_name" id="product" class="form-control" readonly>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Selling Price   </label>
                                <input type="text" name="selling_price" id="selling_price" class="form-control" placeholder="Amount">
                             </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Qty * </label>
                                <input type="text" name="quantity" id="qty" class="form-control" placeholder="Amount">
                             </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Discount(%) </label>
                                <input type="text" name="discount" id="discount" class="form-control">
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
</div>
<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="labour_modal">
<form id="formUpdate" method="post" action="{{ url('../estimations/add-new-labour-details') }}">
            @csrf
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myLargeModalLabel">Add or Change Labour Details </h4>
        </div>
        <div class="modal-body">
        <div class="row m-t-0">
     <div class="col-md-12">
  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
  <input type="hidden" name="reference" id="reference" value="{{ $id }}" class="form-control">

    <div class="row m-t-10">
        <div class="col-md-3">
            <div class="form-group">
                <label for="inputEmail3" class="control-label"> Labour   * </label>
                    <select name="labour[]" class="form-control select2" id="labour">
                    <option value=""> --- Select Labour --- </option>
                        @foreach($labour_settings as $l)
                        <option value="{{ $l->id }}"> {{ strtoupper($l->labour) }} </option>
                        @endforeach
                    </select>
                    </div> 
        </div>
        <div class="col-md-9">
        <section id='Table' class='container'>
        <table class="table table table-bordered" style="background: #fff;" id="myTable2">
            <thead>
                <tr class="target_3">
                    <th width="200px">Labour </th>
                    <th width="100px">Unit </th>
                    <th width="20">Rate/Hr</th>
                    <th width="50">Sub Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='dataRows2'></tbody>
              </table>
            </section>
            </div>
          </div>  
          <div class="row mt-20">
          <table class="table" id="myTableOther">
              <tr class="target_2">
                    <td>
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> General (Charges)  </label>
                            <input type="text" name="labour_name[]"  class="form-control" placeholder="Name of general charges ">
                            </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Amount </label>
                            <input type="text" name="unit_charge[]"  class="form-control charge" placeholder="Enter Amount Eg. 50000 ">
                            </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Percent (%) </label>
                            <input type="text" name="labour_qty[]"  class="form-control qty" placeholder="Enter  Eg. 20"> 
                            <input type="hidden" name="total_amount[]"  class="form-control subtotal"> 
                            </div> 
                    </div> 
                      </td>
                    </tr>
                 </div>
             </table>
           </div>
        </div>
    </div>
</div>

        <div class="modal-footer">
            <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
            <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save  </button>
        </div>
    </form>
    </div><!-- /.modal-content -->        
</div><!-- /.modal-dialog -->
</form>
</div><!-- /.modal -->
</div>
<!-- Edit Profoma -->
<!-- Modal -->
<div class="modal fade" id="editProfomaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<form id="form" method="post" action="{{ url('../estimations/update-profoma-details') }}">
  @csrf  
<div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="editProfomaModalLabel">Edit Proforma Details  </h4>
      </div>
      <div class="modal-body">
      <div class="row">
      <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Vehicle Reg # </label>
                                <input type="text" name="vehicle_reg" id="vehicle_registration" class="form-control">
                            </div>
                        </div>
                    </div>
                @if (Auth::user()->role_id === 1 || Auth::user()->role_id === 2)
               <div class="row">
               <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Reference # </label>
                                <input type="text" name="reference" value="{{ $id }}" class="form-control">
                             </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Proforma Invoice # </label>
                                <input type="text" name="proforma_invoice" id="proforma_invoice" class="form-control">
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make  </label>
                                <input type="text" name="make" id="make_" class="form-control">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Model </label>
                                <input type="text" name="model" id="model_" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-10">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Chassis No.   </label>
                                <input type="text" name="chassis" id="chassis" class="form-control" placeholder="Amount">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Milleage </label>
                                <input type="text" name="milleage" id="milleage" class="form-control">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Created Date </label>
                                  <div class="input-group">
                                  <input type="text" class="form-control" id="created_date" autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                                  <span class="input-group-addon bg-info b-0 text-white"><i class="ti-calendar"></i></span>
                              </div><!-- input-group -->                           
                            </div>
                        </div>
                    </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-rounded">Update Changes</button>
      </div>
    </div>
  </div>
</form>
</div>
        

<script>
 $(function() {
    $("#myTableOther").keyup(function(event) {
        var sub = 0;
        $("#myTableOther .target_2").each(function() {

            var qty = parseFloat($(this).find(".qty").val());
            var price = parseFloat($(this).find(".charge").val());

            var subtotal = parseFloat((qty/100) * price);
            $(this).find(".subtotal").val(subtotal);
            console.log(subtotal);
            if(!isNaN(subtotal))
            sub+=subtotal;
        });
        $(".subtotal").val(sub);
     });
 })

$(function() {
    $("#myTable1").keyup(function(event) {
        var sub = 0;
        $("#myTable1 .target").each(function() {

            var qty = parseFloat($(this).find(".qty").val());
            var discount = parseFloat($(this).find(".discount").val());
            var price = parseFloat($(this).find(".selling_price").val());
            var discount_value = (discount/100) * price;
            var total_price = price - discount_value;
            var subtotal = qty * total_price;
            console.log(subtotal);
            $(this).find(".total_").val(subtotal);
            
            if(!isNaN(subtotal))
            sub+=subtotal;
        });
        // $(".total_").val(sub);
     });
 })

 $("#myTable2").keyup(function(event) {
        var sub = 0;
        $("#myTable2 .target_3").each(function() {

            var qty = parseFloat($(this).find(".qty").val());
            var price = parseFloat($(this).find(".charge").val());

            var subtotal = (qty * price);
            console.log(subtotal);
            
            $(this).find(".subtotal_other").val(subtotal);
            console.log(subtotal);
            if(!isNaN(subtotal))
            sub+=subtotal;
        });
        // $(".subtotal_other").val(sub);
     });

  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#myTable1").on('click','.btnDelete',function(){
         $(this).closest('tr').remove();
      });
      $("#myTable2").on('click','.btnDelete',function(){
         $(this).closest('tr').remove();
      });


      $('#myModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false,
    });
  
    $(document).on("click", ".addNew", function() {
        $('#myModal').modal('show');
    });
  
    $(document).on("click", ".addLabour", function() {
        $('#labour_modal').modal('show');
    });


    $('.edit-part_details').click(function() {
         var part_id = $(this).data('id');   
         console.log(part_id);

         $.ajax({
             url: `../../../estimations/edit-spareparts-details/${part_id}`,
             type: "GET",
             data: {part_id: part_id},
             success: function (response) {
                 console.log(response);
                 $('#details_modal').modal('show');
                 $("#product").val(response[0].item_name);
                 $("#selling_price").val(response[0].selling_price);
                 $("#qty").val(response[0].qty);
                 $("#model").val(response[0].model);
                 $("#make").val(response[0].title);
                 $("#id").val(response[0].id);
                 $("#discount").val(response[0].discount);
             }
         });
    });


    $('.edit-labour').click(function() {
         var labour_id = $(this).data('id');   
         console.log(labour_id);
         $.ajax({
             url: `../../../estimations/edit-labours/${labour_id}`,
             type: "GET",
             data: {labour_id: labour_id},
             success: function (response) {
                 console.log(response);
                 $('#labour_modal').modal('show');
                 $("#product").val(response[0].labour_name);
                 $("#selling_price").val(response[0].selling_price);
                 $("#qty").val(response[0].qty);
                 $("#model").val(response[0].model);
                 $("#make").val(response[0].title);
                 $("#id").val(response[0].id);
             }
         });
    });


    $('#editProfoma').click(function() {
         var reference = $(this).data('id');   
         console.log(reference);
         $.ajax({
             url: `../../../estimations/edit-profoma-details/${reference}`,
             type: "GET",
             data: {reference: reference},
             success: function (response) {
                 console.log(response);
                 $('#editProfomaModal').modal('show');
                 $("#vehicle_registration").val(response[0].vehicle_reg);
                 $("#make_").val(response[0].make);
                 $("#chassis").val(response[0].chassis);
                 $("#model_").val(response[0].model);
                 $("#proforma_invoice").val(response[0].profoma_invoice);
                 $("#milleage").val(response[0].milleage);
                 $("#created_date").val(response[0].created_date);
             }
         });
    });

  // Initializing our modal.
 

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

      $('#client').on('change', function() {
        var client = $('#client').val();
            $.ajax({
                type: "GET",
                url: "../../products/fetch-client-prices/"+client,
                data: { client: client },
                success: function(response){
                    $('#product_id').html(response);
                }
            });
        });

        $("#product_id").on('change', function () {
            var product_id = $("#product_id").val();
            console.log(product_id);
            $.ajax({
                type: "GET",
                url: "../../products/price-lists/details/"+product_id,
                data: { product_id: product_id },
                success: function(response){
                    console.log(response);
                    $("#dataRows").show();
                    var html = '<tr class="target">';
                    html += '<td>'+'<input type="hidden" name="id[]" id="product_id" value="'+response[0].pid+'">'+'</td>';
                    html += '<td width="100px">'+response[0].item_name+'</td>';
                    html += '<td>'+'<input type="text" name="discount[]" class="discount" size="5" value="0">'+'</td>';
                    html += '<td>'+'<input type="text" name="selling_price[]" class="selling_price" value="'+response[0].sale_price+'">'+'</td>';
                    html += '<td>'+'<input type="text" name="qty[]" class="qty" size="5" id="qty[]" required>'+'</td>';
                    html += '<td>'+'<input type="text" class="total_" name="total_charge[]" readonly value="0">'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows').append(html);
                }
            });
        });

        $("#labour").on('change', function () {
            var labour_id = $("#labour").val();
            console.log(labour_id);
            $.ajax({
                type: "GET",
                url: "../../labours/details/"+labour_id,
                data: { labour_id: labour_id },
                success: function(response){
                    $("#dataRows2").show();
                    var html = '<tr class="target_3">';
                    html += '<input type="hidden" name="labour_name[]" value="'+response[0].labour+'">';
                    html += '<td width="100px">'+response[0].labour+'</td>';
                    html += '<td>'+'<input type="text" name="unit_charge[]" class="charge" value="'+response[0].charge+'">'+'</td>';
                    html += '<td>'+'<input type="text" name="labour_qty[]"  class="qty" size="5"  required>'+'</td>';
                    html += '<td>'+'<input type="text" class="subtotal_other" name="total_amount[]" readonly value="0">'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows2').append(html);
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
