@extends('layouts.app_header')

@section('content')
<style>
 input, select, textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
    width: 40px;
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

</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('/home') }}">Home</a> 
            <a class="step" href="#"> Point of Sale  </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title1"> Point of Sale (POS)</h3>
                
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
            </div>
            <div class="panel-body">
            <div class="display_message"></div>
             <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Item Name   * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id">
                                <option value=""> --- Select Item --- </option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->pid }}">  {{ $product->item_name }}  - {{ $product->title }}  :  ({{ $product->quantity }})</option>
                                 @endforeach
                                </select>
                             </div>
                        <input type="hidden" name="quantity[]" id="quantity" class="form-control">
                    </div>
         <div class="col-md-1"></div>
        <div class="col-md-7">
        <form id="formPost" method="POST" action="{{ url('/poso') }}" enctype="multipart/form-data"> 
                  @csrf
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
                 <td style="text-align: right">Total Bill Amount : </td>
                 <td colspan="3"></td>
            </tr>
           </tfooter> -->
        </table>

    </section>
     </div>
    </div>  
 </div> 
</div>
<div id="client_details" style="padding: 10px; border: 1px solid #ddd;">
                <!-- <h3> Customer Details </h3> -->
                <div class="row m-t-10">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Client Name  *</label>
                                <input type="text" name="client_name" id="client_name" class="form-control" placeholder="Client Name " required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Bill No. </label>
                                <input type="text" name="bill_no" id="bill_no" class="form-control" placeholder="Bill Number">
                        </div>
                     </div>
                   </div>
                <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Vehicle Reg# </label>
                                <input type="text" name="vehicle_reg" id="vehicle_reg" class="form-control" placeholder="Vehicle Registration ">
                            </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date Supplied" id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
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

        $("#product_id").on('change', function () {
            var product_id = $("#product_id").val();
            $.ajax({
                type: "GET",
                url: "products/details/"+product_id,
                data: { product_id: product_id },
                success: function(response){
                    // console.log(response);
                    var html = '<tr>';
                    html += '<td>'+'<input type="hidden" name="id[]" id="product_id" value="'+response[0].pid+'">'+'</td>';
                    html += '<td width="100px">'+response[0].item_name+'</td>';
                    html += '<td>'+response[0].selling_price+'</td>';
                    html += '<td>'+'<input type="number" name="qty[]" class="form-" size="70" id="qty" required>'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows').append(html);
                }
            });
        });


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
                        url: "/poso",
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
});

</script>


@endsection