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
            <a class="btn btn-sm btn-primary" href="{{ url('accounts/payments') }}"> <i class="ti-arrow-left"></i>  Go Back</a> <br/>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right">
                <h2> Pay To : </br></h2>
                 <h4 class="panel-title1"> 
                     <strong>
                    @foreach ($supplier_name as $w)
                         {{ $w->supplier_name }}    
                    @endforeach
                     </strong>
                     Invoice <b> #{{ $id }}  </b> </h4>
                <br/>
                 </div>
                 <h2> Purchase List : </br></h2>
                 <h4 class="panel-title1"> 
                     <!-- <strong>
                    @foreach ($supplier_name as $w)
                         {{ $w->supplier_name }}    
                    @endforeach
                     </strong>
                     Invoice <b> #{{ $id }}  </b> </h4>
                <br/> -->
            </div>
        
            <div class="panel-body">
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
                        @endif
                    
                         <div class="row">
                         <div class="row m-t-5">
                   <div class="col-md-1"></div>
                      <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Upload Invoice File  * </label>
                                <input type="file" name="invoice_file" id="invoice_file" class="form-control">
                            </div>
                      </div>
</div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  </label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" name="created_date" placeholder="Date Created" id="datepicker-autoclose" data-date-format="yyyy-mm-dd" required>
                            <span class="input-group-addon bg-info text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                       <div class="col-md-1"></div>
                    </div>
                    </div>
                       </div>
                      </div>
                  </div>
            </div>
      </div>
</div>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="details_modal">
      <form id="formUpdate" method="post" action="{{ url('../purchases/payment') }}">
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
                                <input type="text" name="product_name" id="product" class="form-control" placeholder="Product Name" id="input-detail">
                                <input type="hidden" name="id"   id="id" class="form-control" placeholder="Product Name" id="input-detail">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make/Model  </label>
                                <input type="text" name="model" id="model" class="form-control" placeholder="">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Price  </label>
                                <input type="text" name="payment" id="payment" class="form-control" placeholder="Amount">
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light"> Update </button>
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

    $('.add-details').click(function() {
         var product_id = $(this).data('id');
         $.ajax({
             url: `../purchases/details/${product_id}`,
             type: "GET",
             data: {product_id: product_id},
             success: function (response) {
                 $('#details_modal').modal('show');
                 $("#product").val(response[0].item_name);
                 $("#payment").val(response[0].paid_amount);
                 $("#price").val(response[0].price);
                 $("#model").val(response[0].make+" - "+response[0].model);
                 $("#id").val(response[0].id);
             }
         });
    });

    $("#update_btn").on('click', function () {
            var id = $("#id").val();
            var quantity = $('#quantity').val();
            alert(id);
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