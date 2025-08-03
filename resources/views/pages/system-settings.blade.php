@extends('layouts.app_header')

@section('content')
<style>
.pagination>li>a, .pagination>li>span {
    /* position: relative; */
    /* float: left; */
    padding: 6px 4px;
    margin-left: -1px;
    line-height: 1.42857143;
    color: #337ab7;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd;
}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Settings </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h3 class="panel-title1"> General Settings </h3>
                <br/>
            </div>
        
            <div class="panel-body">
                        <div class="row">
                        <div class="col-lg-6">
                        <div class="card-box">
                                <div class="dropdown pull-right">
                                        <!-- <button type="submit" class="btn btn-info btn-rounded w-md waves-effect waves-light m-b-10 m-t-10" id="btn_item_items" data-toggle="modal" data-target="#myModal5"> + Notification </button> -->
                                </div>
                                <br/>
                                <h4 class="header-title m-t-0 m-b-30">Settings</h4>

                                <table class="table table-striped m-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Company or Business Name </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($settings as $key=>$n)
                                        <tr>
                                            <th scope="row">{{ $key+1 }}</th>
                                            <td>{{ $n->business_name }} </td>
                                            <td> 
                                            <a class="btn btn-sm btn-info" href="{{ url('settings/system/'.$n->id) }}"><i class="fa fa-edit"></i> </a>
                                            <a class="btn btn-sm btn-danger" href=""><i class="fa fa-trash"></i> </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="col-lg-6">
                        
                    </div> 
                 </div>
             </div>


         <!-- //////////////////////////////////////  Notification  Modal //////////////////////////////////////////////////////////////////////--->

                <div id="myModal5" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <form id="formModel5" method="post">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Notification</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">Alert Quantity</label>
                                            <input type="text" name="alert_quantity" class="form-control" id="alert_quantity" placeholder="Alert Quantity item">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-info waves-effect waves-light" id="btn_submit_notification">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
                </div><!-- /.modal -->
<!-- END  -->
 
 
 
 
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
    });

   

    $('#btn_submit_item').click(function() {
        var item_id = $('#item_id').val();
        var brand_id = $('#brand_id').val();
        alert(item_id);
            $.ajax({
                    type: "POST",
                    url: "/product-items",
                    data: { item_id: item_id, brand_id: brand_id },
                    success: function(msg){
                        $('#display_message').html(msg);
                        $('#myModal2').modal('hide');
                        swal({
                            title: "Success!",
                            text: "You have assigned New Item Model",
                            type: "success",
                            confirmButtonClass: "btn-success",
                        });
                        $('input[type]="text", select', this).val('');
                        $('#formItem')[0].reset();
                        setTimeout(function() 
                            {
                                location.reload();  //Refresh page
                         }, 1000);
                    }
                });
            });


           $('#btn_submit_model').click(function() {
            var title = $('#title').val();
                $.ajax({
                    type: "POST",
                    url: "/product-brands",
                    data: { title: title },
                    success: function(msg){
                        $('#display_message').html(msg);
                        $('#myModal1').modal('hide');
                        swal({
                            title: "Success!",
                            text: "You have added New Model",
                            type: "success",
                            confirmButtonClass: "btn-success",
                        });
                        $('input[type]="text", select', this).val('');
                        $('#formModel1')[0].reset();
                        setTimeout(function() 
                            {
                                location.reload();  //Refresh page
                         }, 1000);
                    }
                });
            });

            $('#btn_submit_product').click(function() {
                 var item_name = $('#item_name').val();
                  $.ajax({
                        type: "POST",
                        url: "/products/new-product",
                        data: { item_name: item_name },
                        success: function(response){
                            $('#display_message').html(response);
                            // $('#myModal4').modal('hide');
                            swal({
                                title: "Success!",
                                text: "You have added New Item",
                                type: "success",
                                confirmButtonClass: "btn-success",
                            });
                            $('input[type]="text", select', this).val('');
                            $('#formModel4')[0].reset();
                            setTimeout(function() 
                            {
                                location.reload();  //Refresh page
                            }, 1000);
                        }
                    });
              });

             $('#btn_submit_notification').click(function() {
                var product_id = $('#product_id').val();
                var alert_quantity = $('#alert_quantity').val();
                  $.ajax({
                        type: "POST",
                        url: "/settings/add-notification",
                        data: { product_id: product_id, alert_quantity: alert_quantity },
                        success: function(response){
                            $('#display_message').html(response);
                            $('#myModal5').modal('hide');
                            swal({
                                title: "Success!",
                                text: "You have set notification",
                                type: "success",
                                confirmButtonClass: "btn-success",
                            });
                            $('input[type]="text", select', this).val('');
                            $('#formModel5')[0].reset();
                            setTimeout(function() 
                            {
                                location.reload();  //Refresh page
                            }, 1000);
                        }
                    });
              });
    
              $('body').on('click', '.deleteItem', function () {
                var item_id = $(this).data("id");
               var conf = confirm("Are You sure want to delete ?");
               if(conf == true) {
                $.ajax({
                    type: "get",
                    url: "{{ url('/settings/items/delete') }}"+'/'+item_id,
                    success: function (data) {
                        if(data) {
                            setTimeout(function() 
                            {
                                location.reload();  //Refresh page
                            }, 1000);
                        } else {
                            alert("Something went wrong")
                        }
                       },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
               } else {
               }
           
            });

            $('body').on('click', '.deleteMake', function () {
                var Item_id = $(this).data("id");
               var conf = confirm("Are You sure want to delete ?");
               if(conf == true) {
                $.ajax({
                    type: "get",
                    url: "{{ url('/settings/makes/delete') }}"+'/'+Item_id,
                    success: function (data) {
                        if(data) {
                            setTimeout(function() 
                            {
                                location.reload();  //Refresh page
                            }, 1000);
                        } else {
                            alert("Something went wrong")
                        }
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
               } else {
               }
             
            });

            $('body').on('click', '.deleteAssignMake', function () {
                var item_id = $(this).data("id");
                  var conf = confirm("Are You sure want to delete ?");
                  if(conf == true) {
                    $.ajax({
                    type: "get",
                    url: "{{ url('/settings/assign-items/delete') }}"+'/'+item_id,
                    success: function (data) {
                        if(data) {
                            setTimeout(function() 
                            {
                                location.reload();  //Refresh page
                            }, 1000);
                        } else {
                            alert("Something went wrong")
                        }
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
                } else {
                }
          
            });

      });
 
</script>

@endsection