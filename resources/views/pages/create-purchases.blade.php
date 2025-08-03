@extends('layouts.app_header')

@section('content')

 <div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Purchases  </a>
        </div>
    </div>
</div> 

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title1"> Create Purchases  </h3>
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
                  </div>
            <div class="panel-body">
            <form id="purchase-form" method="POST" action="{{ url('send-purchases') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #dde; border-bottom: red;">                 
                    <div class="row m-t-10">
                        <!-- <div class="col-md-1"></div> -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="control-label">Item Name * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id"  required>
                                  <option> Select Item </option>
                                   @foreach($items as $p)
                                   <option value="{{ $p->id }}"> {{ $p->item_name }} </option>
                                   @endforeach
                             </select>        
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="control-label">Make   *</label>
                                <input type="text" name="make[]" id="make" class="form-control" placeholder="Make" required>             
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="control-label">Model   *</label>
                                <input type="text" name="model[]" id="model" class="form-control" placeholder="Model" required>      
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="control-label">Part Number *  </label>
                                <input type="text" name="part_number[]" id="model" class="form-control" placeholder="Part number" required>      
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                         <div class="form-group">
                                <label for="inputEmail3" class="control-label">Purchase Price  * </label>
                                <input type="text" name="purchase_price[]" id="purchase_price" class="form-control" placeholder="Purchase Price" required>
                            </div>
                       </div>
                       <div class="col-md-1">
                         <div class="form-group">
                                <label for="inputEmail3" class="control-label">Qty  *</label>
                                <input type="text" name="quantity[]" id="quantity" class="form-control" placeholder="Enter qty " required>
                            </div>
                        </div>
                       <div class="col-md-1"> 
                            <button class="add_field_button btn-success" type="button"><i class="fa fa-plus"></i> Add </button>
                            <!-- <button class="add_field_button pull-right"> + Add </button> -->
                     </div>
              </div>  <!-- End Original Form Input -->
                <div class="input_fields"></div>
           </div>

            
                <div id="client_details" style="padding: 10px; border: 1px solid #ddd; background: #;">
                <!-- <h3> Customer Details </h3> -->
                <div class="row m-t-30">
                <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Supplier Name  *</label>
                            <select name="supplier_id" class="form-control select2" id="supplier_id">
                                   @foreach($suppliers as $p)
                                   <option value="{{ $p->id }}"> {{ $p->supplier_name }} </option>
                                   @endforeach
                             </select>      
                            </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Invoice Number  * </label>
                                <input type="text" name="invoice_number" id="invoice_number" class="form-control" placeholder="Invoice Number " required>
                            </div>
                    </div>
                       <div class="col-md-1"> </div>
                    </div>

                    <div class="row m-t-10">
                   <div class="col-md-1"></div>
                      <div class="col-md-4">
                        <div class="radio radio-info radio-inline">
                                        <input type="radio" name="vat_type" id="with_vat1" value="1" name="radioInline" checked required>
                                        <label for="inlineRadio1"> With VAT </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" name="vat_type" id="without_vat" value="0" name="radioInline" required>
                                        <label for="inlineRadio2"> Without VAT </label>
                                  </div>
                          </div> 
                       <div class="col-md-4">
                        <div class="form-group">
                        <!-- <label for="inputEmail3" class="control-label"> VAT Amount * </label> -->
                                <input type="number" name="vat_amount" id="vat_field" style="display:none;" class="form-control" placeholder="VAT Amount">
                            </div>
                    </div>                 
                         
                       <!-- <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Other Cost  </label>
                                <input type="text" name="other_cost" id="other_cost" class="form-control" placeholder="Eg. Transport Costs">
                            </div>
                    </div> -->
                    </div>
                    <div class="row m-t-20">
                   <div class="col-md-1"></div>
                      <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Upload Invoice File   </label>
                                <input type="file" name="invoice_file" id="invoice_file" class="form-control">
                            </div>
                      </div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  </label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" autocomplete="off" name="created_date" placeholder="Date Created" id="datepicker-autoclose" data-date-format="yyyy-mm-dd" required>
                            <span class="input-group-addon bg-info text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                       <div class="col-md-1"></div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> </label>
                                    <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10">Save  </button>
                            </div>
                        </div>
                    </div>
                </form>
        
            </div>
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


var max_fields = 30; //maximum input boxes allowed
 var wrapper = $(".input_fields"); //Fields wrapper
 var add_button = $(".add_field_button"); //Add button class

 var x = 1; //initial text box count
$(add_button).click(function(e){ //on add input button click
 e.preventDefault();
 if(x < max_fields){ //max input box allowed
 x++; //text box increment

$(wrapper).append('<divi>'+
        '<button class="remove_field pull-right btn-danger"><span><i class="fa fa-remove"></i> Delete</span></button>'+
            '<div class="row m-t-10">'+
                '<div class="col-md-2">'+
                    '<div class="form-group">'+
                        '<label for="" class="control-label">Item Name * </label>'+
                        '<select name="product_id[]" class="form-control custom-select" id="product_id"  required>'+
                            '<option> Select Item </option>'+
                            @foreach($items as $p)
                            '<option value="{{ $p->id }}"> {{ $p->item_name }} </option>'+
                            @endforeach
                    '</select>'+      
                    '</div>'+
                '</div>'+
                '<div class="col-md-2">'+
                    '<div class="form-group">'+
                        '<label for="" class="control-label">Make   *</label>'+
                        '<input type="text" name="make[]" id="make" class="form-control" placeholder="Make" required>'+             
                    '</div>'+
                '</div>'+
                '<div class="col-md-2">'+
                    '<div class="form-group">'+
                        '<label for="" class="control-label">Model  * </label>'+
                        '<input type="text" name="model[]" id="model" class="form-control" placeholder="Model" required>'+      
                    '</div>'+
                '</div>'+
                '<div class="col-md-2">'+
                    '<div class="form-group">'+
                        '<label for="inputEmail3" class="control-label">Part Number  *</label>'+
                        '<input type="text" name="part_number[]" id="part_number" class="form-control" placeholder="Part Number" required>'+
                    '</div>'+
                '</div>'+
               
                '<div class="col-md-2">'+
                    '<div class="form-group">'+
                        '<label for="inputEmail3" class="control-label">Purchase Price  * </label>'+
                        '<input type="text" name="purchase_price[]" id="purchase_price" class="form-control" placeholder="Purchase Price" required>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-1">'+
                    '<div class="form-group">'+
                        '<label for="inputEmail3" class="control-label">Qty  *</label>'+
                        '<input type="text" name="quantity[]" id="quantity" class="form-control" placeholder="Quantity" required>'+
                    '</div>'+
                '</div>'+
                '<div class="col-md-1">'+ 
                '</div>'+
            '</div>'+
        '</divi>'); //add input box
        }
 });
 
 $(wrapper).on("click",".remove_field", function(e){ 
 e.preventDefault();

 $(this).parent('divi').remove(); x--;
 })
});

$('.custom-select:last').select2();

//  Select  VAT type
$("#input[type='radio']").change( function(){
    if($(this).val() == '1') {
        $("#vat_field").show();
    } else {
        $('#vat_field').hide();
    }
  });

</script>


@endsection




