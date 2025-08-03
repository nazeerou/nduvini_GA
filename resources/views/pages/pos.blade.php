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
            <a class="step" href="#"> Estimates & Invoices   </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title1"> Create Estimate </h3>
              
            </div>
            <div class="panel-body">
            <div class="display_message"></div>
             <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Add Parts   * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id" required>
                                <option value=""> --- Select Item --- </option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->pid }}"> {{ $product->item_name }}  - {{ $product->model }}  :  ({{ $product->quantity }}) </option>
                                 @endforeach
                                </select>
                             </div>
                             
                    </div>
         <div class="col-md-1"> </div>
        <div class="col-md-7">
        <form id="formPost" method="POST" action="{{ url('/pos/create') }}" enctype="multipart/form-data"> 
                  @csrf

                <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}" class="form-control">
                <input type="hidden" name="quantity[]" id="quantity" class="form-control">

        <section id='Table' class='container-fluid'>
        <table class="table table-striped table-bordered" style="background: #fff;" id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="300px">Item Name</th>
                    <th width="100px">Selling Price</th>
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

                <div id="client_details" style="padding: 10px; border: 1px solid #ddd;">
                <!-- <h3> Customer Details </h3> -->
                <!-- <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">LPO Number# </label>
                                <input type="text" name="lpo_number" id="vehicle_reg" class="form-control" placeholder="LPO Number ">
                            </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Tax Invoice No# </label>
                                <input type="text" name="tax_invoice" id="" class="form-control" placeholder="Tax invoice Number ">
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                         <label for="inputEmail3" class="control-label">Delivery Note </label> 
                                 <input type="text" name="delivery_note" id="bill_no" class="form-control" placeholder="Delivery Note"> 
                        </div>
                     </div>
                   </div> -->
                <div class="row m-t-20">
                <div class="col-md-1"></div>
                <div class="col-md-3">
                        <!-- <div class="radio radio-info radio-inline">
                                        <input type="radio" name="vat_type" id="with_vat1" value="1" name="radioInline" checked required>
                                        <label for="inlineRadio1"> With VAT </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" name="vat_type" id="without_vat" value="0" name="radioInline" required>
                                        <label for="inlineRadio2"> Without VAT </label>
                                  </div>
                            </div> -->
                            <!-- <div class="col-md-3">
                                   <div class="checkbox">
                                        <input type="checkbox" name="withholding" value="0.02">
                                        <label for="inlineRadio2"> WithHolding   Tax (2%)  </label>
                                  </div>
                          </div>  -->
                        </div>
                        <div class="row m-t-30">
                <div class="col-md-1"></div>         
               
                <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Vehicle Reg#  *</label>
                                <input type="text" name="vehicle_reg"  class="form-control" placeholder="Vehicle Reg..     Eg. T390 EEE" required>
                       </div>  
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Make *</label>
                                <input type="text" name="make"  class="form-control" placeholder="Enter Make " required>
                       </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Model *</label>
                                <input type="text" name="model"  class="form-control" placeholder="Enter Model " required>
                       </div>  
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Chassis No.   *</label>
                                <input type="text" name="chassis"  class="form-control" placeholder="Enter Chassis No." required>
                       </div>  
                    </div>
                   </div>
                   <div class="row m-t-30">
                <div class="col-md-1"></div>         
               
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Client Name  *</label>
                        <select name="client_name" class="form-control select2" id="client_name" required>
                                <option value=""> --- Select Client --- </option>
                                 @foreach($clients as $client)
                                   <option value="{{ $client->id }}">  {{ $client->client_name }}  - ({{ $client->place }}) </option>
                                 @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
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
              </div>
         </form> <!-- End form -->
        </div>
    </div>
</div>
<!-- END  -->


<script>
    $(document).ready(function () {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
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
                url: "products/details/"+product_id,
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

 

});

</script>

@endsection