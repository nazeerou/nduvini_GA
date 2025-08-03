@extends('layouts.app_header')

@section('content')
<style>
input { text-transform: uppercase; }
</style>
<div class="row m-t-10">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('sales-summary') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Edit Sale </h3>
                <!-- <br/>
                @if(session('message'))
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
            <form id="edit" method="POST" action="{{ url('sales/update') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #eee; border-bottom: red;">
                <div class="row m-t-30">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Item Name   * </label>
                                <select name="product_id" class="form-control select2" id="product_id">
                                <option value="{{ $sales[0]->id }}"> {{ $sales[0]->item_name }}</option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->pid }}">  {{ $product->item_name }}  - {{ $product->title }}  :  ({{ $product->quantity }})  </option>
                                 @endforeach
                                </select>
                             </div>
                        <input type="hidden" name="id" value=" {{ $sales[0]->id }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Selling Price * </label>
                            <input type="text" name="selling_price" id="selling_price" value="{{ $sales[0]->selling_price }}" class="form-control" style="background: #ddd;">  

                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Quantity * </label>
                                <input type="number" name="qty" id="quantity"  value="{{ $sales[0]->qty }}" class="form-control" placeholder="" required>
                            </div>
                    </div>
                </div>
                </div> 
                <div id="client_details" style="padding: 10px; border: 1px solid #ddd; background: #eee;">
                <!-- <h3> Customer Details </h3> -->
                <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Client Name </label>
                                <input type="text" name="client_name" id="client_name"  value="{{ $sales[0]->client_name }}" class="form-control" placeholder="Client Name " required>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Vehicle Reg.No </label>
                                <input type="text" name="vehicle_reg" id="vehicle_reg"  value="{{ $sales[0]->vehicle_reg }}" class="form-control" placeholder="Vehicle Registration" required>
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Phone </label>
                                <input type="tel" name="phone" id="phone" class="form-control"  value="{{ $sales[0]->mobile }}" placeholder="Phone number">
                            </div>
                    </div>
                   </div>
                <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Bill No </label>
                                <input type="text" name="bill_no" id="bill_no" class="form-control"  value="{{ $sales[0]->bill_no }}" placeholder="Bill number">
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  </label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" name="created_date"value="{{ $sales[0]->created_date }}" placeholder="Date Supplied" id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                    </div>
                   </div>
                <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-success btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10">Update Sale</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
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

        $("#product_id").on('change', function () {
            var product_id = $("#product_id").val();
            $.ajax({
                type: "GET",
                url: "../../products/"+product_id,
                data: { product_id: product_id },
                success: function(response){
                    $('#selling_price').val(response[0].selling_price);
                    $('#quantity').val(response[0].quantity);
                }
            });
        });
     });


</script>


@endsection