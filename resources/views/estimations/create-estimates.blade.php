@extends('layouts.app_header')

@section('content')
<style>
 input, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
    /* width: 40px; */
}
.btn-sm {
    font-size: 10px;
    line-height: 1.5;
    border-radius: 3px;
}
table {
    border: 1px solid #ddd;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 4px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

#labour_field {
    width: 10em;
    border-radius: 3px;
    border-color: #ddd;
}

#qty {
    width: 3em;
    border-color: #ddd;
}

</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('/home') }}">Home</a> 
            <a class="step" href="#"> Estimates   </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title1"> Create Estimates </h3>
              
            </div>
            <form id="formPost" method="POST" action="{{ url('/estimate/create') }}" enctype="multipart/form-data"> 
                  @csrf
            <div class="panel-body" style="background: #fff; border: 1px solid #ddd;">
            <div class="display_message"></div>
            <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
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
         <div class="col-md-1"> </div>
        <div class="col-md-7"></div>
     </div>
             <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Add Parts   * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id" required>
                                <option value=""> --- Select Part --- </option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->id }}"> {{ $product->item_name }}  - {{ $product->model }} | {{ $product->part_number }}  :  ({{ $product->quantity }}) </option>
                                 @endforeach
                                </select>
                             </div> 
                    </div>
         <div class="col-md-1"> </div>
        <div class="col-md-7">
      
        <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}" class="form-control">
        <input type="hidden" name="quantity[]" id="quantity" class="form-control">

        <section id='Table' class='container-fluid'>
        <table class="table table-striped table-bordered" style="background: #fff;" id="myTable1">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="300px">Part Name</th>
                    <th width="100px">D &nbsp; (%) </th>
                    <th width="100px">Unit </th>
                    <th width="10">Qty</th>
                    <th> SubTotal </th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='dataRows'></tbody> 
            <tfooter style="display: none;" >
             <tr>
                 <td style="text-align: right" colspan="5"> </td>
                 <td><input disabled id="subtotal_" name="total_charge" class="form-control subtotal_" value="0" type="text" /></td> <td></td><td></td>
               </tr>
              </tfooter>
               </table>
            </section>
            </div>
            </div>  
           </div>

           <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Add Labour   * </label>
                                <select name="labour[]" class="form-control select2" id="labour">
                                <option value=""> --- Select Labour --- </option>
                                 @foreach($labours as $l)
                                   <option value="{{ $l->id }}"> {{ strtoupper($l->labour) }} </option>
                                 @endforeach
                                </select>
                             </div> 
                    </div>
         <div class="col-md-1"></div>
        <div class="col-md-7">
        <section id='Table' class='container'>
        <table class="table table table-bordered" style="background: #fff;" id="myTable2">
            <thead>
                <tr class="target_3">
                    <th>#</th>
                    <th width="300px">Labour </th>
                    <th width="100px">Unit </th>
                    <th width="20">Rate/Hr</th>
                    <th width="30">Sub Total</th>
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
                 <td><div class="col-md-2"></div></td>
                 <td><div class="col-md-2"></div></td>
                    <td>
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> General (Charges)  </label>
                            <input type="text" name="labour_name[]"  class="form-control" placeholder="Specify general labour ">
                            </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Charges </label>
                            <input type="text" name="unit_charge[]"  class="form-control charge" placeholder="Enter Charges ">
                            </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Percent (%) </label>
                            <input type="text" name="labour_qty[]"  class="form-control qty" placeholder="Enter  Eg. 20"> 
                            <input type="hidden" name="rate[]" value="%" size="2">
                            <input type="hidden" name="total_amount[]"  class="form-control subtotal"> 
                            </div> 
                    </div> 
                  </td>
            </tr>
       </div>
       </table>
     </div>

       <div id="client_details" style="padding: 10px; border: 0px solid #ddd;">
                <div class="row m-t-30">
                <div class="col-md-1"></div> 
                <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Vehicle Reg.  *</label>
                            <input type="text" name="vehicle_reg"  class="form-control" id="vehicle_reg" placeholder="Vehicle Reg..     Eg. T390 EEE" required>
                       </div>  
                    </div> 
                <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Client Name  </label>
                        <select name="client_name" class="form-control select2" id="client_name">
                                <option value=""> --- Select Client Name --- </option>
                                 @foreach($clients as $client)
                                   <option value="{{ $client->id }}">  {{ $client->client_name }}  - ({{ $client->place }}) </option>
                                 @endforeach
                                </select>
                        </div>
                    </div>                      
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Customer (Option) </label>
                       <textarea class="form-control" id="customer" name="customer_name" rows="4" placeholder="Enter Customer Name"></textarea>
                         </div>  
                    </div>       
              </div>
        <div class="row">
                <div class="col-md-1"></div>         
                <div class="col-md-10">
                <table id="datatable1" class="table table table-bordered">
                <thead id="table_title" style="display: none">
                     <tr style="text-align: center; background: #eee;">
                                <th>Customer Name</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Chassis No.</th>
                                <th>Milleage </th>
                                <th>Year of Registraion </th>
                            </tr>
                        </thead>
                         <tbody id="customer_data" class="tbody">
                        </tbody>
                    </table>
                </div>
        </div>
</div></div>
        <div id="client_detail_one" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row">
                <div class="col-md-1"></div>         
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Make *</label>
                                <input type="text" name="make"  class="form-control" placeholder="Enter Make" id="make" required>
                       </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Model *</label>
                                <input type="text" name="model"  class="form-control" placeholder="Enter Model " id="model" required>
                       </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Chassis No.   *</label>
                                <input type="text" name="chassis"  class="form-control" placeholder="Enter Chassis No." id="chassis" required>
                       </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label"> Milleage   </label>
                                <input type="text" name="milleage"  class="form-control" placeholder=" Eg. 799 km " id="milleage">
                       </div>  
                    </div>
                    <div class="col-md-2">
                       <div class="form-group">
                        <label for="inputEmail3" class="control-label">Year of Registration   *</label>
                                <input type="text" name="registration_year"  class="form-control" placeholder="Enter Year of Registration" id="registration_year" required>
                          </div>  
                       </div>
                     </div>
                   <div class="row m-t-30">
                <div class="col-md-1"></div>         

               <div class="col-md-3">
                <div class="form-group">
                <label for="inputEmail3" class="control-label">VAT </label>
                 </div>
                        <div class="radio radio-info radio-inline">
                                        <input type="radio" name="vat_amount" id="with_vat" value="0.18" name="radioInline" checked required>
                                        <label for="inlineRadio1"> With VAT </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" name="vat_amount" id="without_vat" value="0" name="radioInline" required>
                                        <label for="inlineRadio2"> Without VAT </label>
                                  </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                            <label for="inputEmail3" class="control-label">TEMESA Fee ? </label>
                         </div>
                        <div class="radio radio-info radio-inline">
                                <input type="radio" name="temesa_fee" value="0.08" name="radioInline" checked required>
                                <label for="inlineRadio1">Yes</label>
                            </div>
                            <div class="radio radio-inline">
                                <input type="radio" name="temesa_fee" value="0" name="radioInline" required>
                                <label for="inlineRadio2">No</label>
                        </div>
                       </div>               
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                    </div>
                   </div>
              </div>
              <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save  </button>
                        </div>
                    </div>
                </div>
              </div>
              </form> <!-- End form -->
        </div>
    </div>
</div>
<!-- END  -->

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="client_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Client Information </h4>
                    </div>
                    <div class="modal-body">
                    <div class="row m-t-0">
                   <div class="col-md-12">
                        <table class="table table-striped m-10">
                            <thead>
                            <th>Tick </th>
                            <th>Description </th>
                            <th></th>
                            <th>Date</th>
                            <th>Action </th>
                           </thead>
                           <tbody id="tabody"></tbody>
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


        <!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="alert_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Client Information </h4>
                    </div>
                    <div class="modal-body">
                    <div class="row m-t-0">
                   <div class="col-md-12">
                                 <h4> No Record Found! <strong>Please enter Information</strong></h4>
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
            $(this).find(".total_").val(subtotal);
            
            if(!isNaN(subtotal))
            sub+=subtotal;
        });
        $(".subtotal_").val(sub);
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

      $("#labour").on('change', function () {
            var labour_id = $("#labour").val();
            $.ajax({
                type: "GET",
                url: "labours/details/"+labour_id,
                data: { labour_id: labour_id },
                success: function(response){
                    $("#dataRows2").show();
                    var html = '<tr class="target_3">';
                    html += '<td width="10px;">'+'<input type="hidden" name="labour_name[]" value="'+response[0].labour+'">'+'</td>';
                    html += '<td width="100px">'+response[0].labour+'</td>';
                    html += '<td>'+'<input type="text" name="unit_charge[]" class="form-control charge" value="'+response[0].charge+'">'+'</td>';
                    html += '<td>'+'<input type="text" name="labour_qty[]"  class="form-control qty" size="5"  required><input type="hidden" name="rate[]" value="hr" size="2">'+'</td>';
                    html += '<td width="70px;">'+'<input type="text" class="form-control subtotal_other" name="total_amount[]" readonly value="0">'+'</td>';
                    html += '<td width="20px;">'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows2').append(html);
                }
            });
        });

        $('#client').on('change', function() {
        var client = $('#client').val();
            $.ajax({
                type: "GET",
                url: "products/fetch-client-prices/"+client,
                data: { client: client },
                success: function(response){
                    $('#product_id').html(response);
                }
            });
        });

        $("#product_id").on('change', function () {
            var product_id = $("#product_id").val();
            $.ajax({
                type: "GET",
                url: "products/price-lists/details/"+product_id,
                data: { product_id: product_id },
                success: function(response){
                    $("#dataRows").show();
                    var html = '<tr class="target">';
                    html += '<td width="10px">'+'<input type="hidden" name="id[]" id="product_id" value="'+response[0].pid+'">'+'</td>';
                    html += '<td width="80px">'+response[0].item_name+'</td>';
                    html += '<td>'+'<input type="text" name="discount[]"  class="form-control discount" size="5" value="0">'+'</td>';
                    html += '<td>'+'<input type="text" name="selling_price[]" class="form-control selling_price" value="'+response[0].sale_price+'">'+'</td>';
                    html += '<td width="50px;">'+'<input type="text" name="qty[]" size="15" class="form-control qty" required>'+'</td>';
                    html += '<td>'+'<input type="text" class="form-control total_" name="total_charge[]" readonly value="0">'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows').append(html);
                }
            });
        });


        $("#client_id").on('change', function () {
            var client_id = $("#client_id").val();
            console.log(client_id);
            $.ajax({
                type: "GET",
                url: "../clients/client-details/"+client_id,
                data: { client_id: client_id },
                success: function(response){      
                if(response != '') {
                $('#client_modal').modal('show');
                 console.log(response[0].client_name);
                var res = '';
                $.each(response, function (key, value) {
                        res +='<tr>'+
                        '<td>'+'<input type="checkbox" name="check" id="reg" value="'+response[0].vehicle_reg+'">'+'</td>'+
                        '<td>'+value.vehicle_reg+'</td>'+'<td>'+value.make+' '+'|'+value.model+'</td>'+'<td>'+value.created_date+'</td>'+
                        '<td>'+'<input type="submit" class="btn-sm btn-primary btn-rounded waves-effect waves-light" id="save_" value="submit">'+'</td>'+'<td>'+
                        '</tr>';
                       });
                $('#tabody').html(res);
                } else {
                   $('#alert_modal').modal('show');
                }
                }
            });
        });

        $('#save_').click(function() {
            alert("hi");
        $.ajax({
            url: '/get-users',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var users = response.users;
                var userList = $('#userList');
                userList.empty();

                users.forEach(function(user) {
                    userList.append('<p>' + user.name + '</p>');
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });


    $("#vehicle_reg").keyup(function(event) {
       var vehicle = $('#vehicle_reg').val();
        $.ajax({
                type: "GET",
                url: "../clients/vehicle_details/"+vehicle,
                data: { vehicle: vehicle },
                success: function(response){ 
                    if (response) {

                        // $("#client_name").append('<option>Select Client</option>');
                    $.each(response, function (key, value) {
                     $("#client_name").empty();
                     $("#client_name").append('<option value="'+value["id"]+'">'
                     + value["client_name"] +
                      '</option>');
                    });
                } else {
                    $("#client_name").empty();
                }
                  console.log(response[0].id);
                 $("#client_name").val(response[0].client_name);
                 $("#client_id").val(response[0].id);
                 $("#make").val(response[0].make);
                 $("#model").val(response[0].model);
                 $("#chassis").val(response[0].chassis);
                 $("#milleage").val(response[0].milleage);
                 $("#registration_year").val(response[0].registration_year);
                }

            });

     });

});

</script>

@endsection
