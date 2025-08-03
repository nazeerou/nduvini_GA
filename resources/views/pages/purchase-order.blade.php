@extends('layouts.app_header')

@section('content')

<div class="row m-t-10">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> >>
            <a class="step" href="#"> Purchase Order  </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Purchase Order </h3>
                <br/>
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
            <form  action="{{ url('/pos')}}" method="POST" enctype="multipart/form-data"> 
                  @csrf
                  <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #eee;">
                <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Item Name *</label>
                                <select name="product_id" class="form-control select2" id="product_id">
                                 @foreach($products as $product)
                                   <option value="{{ $product->id }}"> {{ $product->product_name }} </option>
                                 @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Model * </label>
                                <input type="text" name="model" id="model" class="form-control" placeholder="" required>
                            </div>
                    </div>
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Quantity * </label>
                                <input type="number" name="qty" id="qty" class="form-control" placeholder="" required>
                            </div>
                    </div>
                </div>
                </div>
                <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #eee;">
                <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Supplier Name </label>
                                <input type="text" name="client_name" id="client_name" class="form-control" placeholder="Client Name ">
                            </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Phone </label>
                                <input type="tel" name="mobile" id="phone" class="form-control" placeholder="Phone number">
                            </div>
                    </div>
                </div>
             </div>
                <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-4">
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

        $("#selling_type").on('change', function () {

            var type = $("#selling_type").val();

            $.ajax({
                type: "GET",
                url: "./ajax.php?f=processors/medicine_type.php",
                data: "type="+type,
                success: function(msg){
                    $('#product_name').html(msg);
                }
            });
        })


        $("#save_btn").on('click', function () {

            var product_name = $("#product_name").val();
            var quantity = $("#quantity").val();
            var user_id = $("#user_id").val();
            var customer_name = $("#customer_name").val();
            var phone = $("#phone").val();


            if(product_name == null){
                swal({
                    title: "Warning",
                    text: "Medicine should not be empty!",
                    type: "warning",
                    confirmButtonClass: "btn-danger",
                })
             } else {
                $.ajax({
                        type: "POST",
                        url: "./ajax.php?f=processors/post_sales.php",
                        data: { product_name: product_name, quantity: quantity, user_id: user_id, customer_name: customer_name, phone: phone },
                        success: function(msg){
                            $('#display_message').html(msg);
                            $('input[type]="text", select', this).val('');
                        }
                    });
               }
           })
        });


</script>


@endsection