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
            <a class="step" href="#"> Products </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h3 class="panel-title"> Products & Models </h3>
                <br/>
            </div>
        
            <div class="panel-body">
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
                   <div class="row">
                   <div class="col-lg-6">
                            <div class="card-box">
                                <div class="dropdown pull-right">
                                <button class="btn btn-primary waves-effect waves-light btn-rounded w-md" data-toggle="modal" data-target=".item-modal"> + Item </button>

                                        <!-- <button type="button" class="btn btn-info btn-rounded w-md waves-effect waves-light m-b-10 m-t-10" id="btn_item_item" data-toggle="modal" data-target="#myModal4"> + Item </button> -->
                                </div>
                                <br/>
                                <h4 class="header-title m-t-0 m-b-30">Item Lists</h4>

                                <p class="text-muted font-13 m-b-15">
                                    
                                </p>

                                <table class="table table-striped m-0" id="itemTable">
                                    <thead>
                                        <tr>
                                            <!--<th>#</th>-->
                                            <th>Item Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <!-- @foreach($items as $key=>$item)
                                        <tr>
                                            <th scope="row">{{ $key+1 }}</th>
                                            <td>{{ $item->item_name }} </td>
                                            <td> 
                                            <a class="btn btn-sm btn-info" href="{{ url('products/items/edit/'.$item->id) }}"><i class="fa fa-edit"></i> </a>
                                            <a class="btn btn-sm btn-danger" href="{{ url('products/items/delete/'.$item->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach -->
                                     </tbody>
                                </table> 
                            </div>
                        </div><!-- end col -->
                            <div class="col-lg-6">
		              <div class="card-box">
                                    <div class="dropdown pull-right">
                                    <button class="btn btn-primary waves-effect waves-light btn-rounded w-md" data-toggle="modal" data-target=".assign-item-modal"> + Assign Make </button>
                                        <!-- <button type="submit" class="btn btn-info btn-rounded w-md waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target="#myModal2" id="btn_item"> + Assign Make </button>  -->
                                    </div>
                                    <br/>
                        			<h4 class="header-title m-t-0 m-b-30">Assigned Items Lists</h4>

                                    <p class="text-muted font-13 m-b-15">
                                    </p>

                                    <table id="assignedMakeTable" class="table table-striped m-0">
                                        <thead>
                                            <tr>
                                                <!--<th>#</th>-->
                                                <th>Item Name</th>
                                                <th>Make </th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <!-- @foreach($item_models as $key=>$item)
                                            <tr>
                                                <th scope="row">{{ $key+1 }}</th>
                                                <td>{{ $item->item_name }} {{ $item->id }} </td>
                                                <td>{{ $item->title }} </td>
                                                <td>
                                               @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2) 
						<a class="btn btn-sm btn-danger" href="{{ url('product-items/delete/'.$item->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>
                                               @else
                                               @endif 
                                               </td>
                                            </tr>
                                        @endforeach -->
                                        </tbody>
                                    </table>

								</div>
							</div><!-- end col -->
                        </div>       


                        <div class="row">
                    <div class="col-lg-6">
								<div class="card-box">
                                    <div class="dropdown pull-right">
                                            <button type="submit" class="btn btn-primary btn-rounded w-md waves-effect waves-light m-b-10 m-t-10" id="btn_model" data-toggle="modal" data-target=".make-modal"> + Make </button>
                                    </div>
                                    <br/>
                        			<h4 class="header-title m-t-0 m-b-30">Make Lists</h4>
                                    <table class="table table-striped m-0"  id="makeTable">
                                        <thead>
                                            <tr>
                                                <!--<th>#</th>-->
                                                <th>Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <!-- @foreach($brands as $key=>$brand)
                                            <tr>
                                                <th scope="row">{{ $key+1 }}</th>
                                                <td>{{ $brand->title }} </td>
                                                <td> 
                                                <a class="btn btn-sm btn-info" href="{{ url('product-brands/edit/'.$brand->id) }}"><i class="fa fa-edit"></i> </a>
												<a class="btn btn-sm btn-danger" href="{{ url('product-brands/delete/'.$brand->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i> </a>
                                                </td>
                                            </tr>
                                        @endforeach -->
                                        </tbody>
                                    </table>

								</div>
							</div><!-- end col -->
                
                        
                        <div class="col-lg-6">
                        <div class="card-box">
                                <div class="dropdown pull-right">
                                <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".adjustment-reason-modal"> +  Reason </button>
                                </div>
                                <br/>
                                <h4 class="header-title m-t-0 m-b-30">Adjustment Reasons </h4>

                                <table id="datatable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th> # </th>
                                            <th>Reason </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($adjustments  as $key => $adjust)
                                        <tr>
                                            <td with="10px">{{ $key+1 }}</td>
                                            <td> {{ $adjust->reason }} </td>
                                            <td>
                                            <a class="btn btn-sm btn-danger" href="{{ url('settings/adjustments/delete/'.$adjust->id) }}"  onclick="return confirm('Are you sure you want to delete this reason ?');"><i class="fa fa-trash"></i> </a>
                                            </td>       
                                        </tr>
                                    @endforeach  
                                  </tbody>
                               </table>
                            </div>
                        </div><!-- end col -->
                    </div> 

                        
                 </div>
             </div>



         <!-- //////////////////////////////////////  Item  Modal //////////////////////////////////////////////////////////////////////--->

        <div class="modal fade item-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
            <form id="formModel4" method="post" action="{{ url('products/new-product') }}">
              @csrf
                <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title">Add New Item </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="field-1" class="control-label">Item Name</label>
                                                <input type="text" name="item_name" class="form-control" id="item_name" placeholder="Item Name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-info waves-effect waves-light" id="btn_submit_products">Save</button>
                                </div>
                            </div>
                        </div>
                     </form>
                    </div><!-- /.modal -->





         <!-- //////////////////////////////////////  Make  Modal //////////////////////////////////////////////////////////////////////--->

                    <div class="modal fade make-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                      <form id="formModel4" method="post" action="{{ url('product-brands') }}">
                        @csrf
                            <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                <h4 class="modal-title">Add New Make </h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="field-1" class="control-label">Make *</label>
                                                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Make   Eg. TOYOTA" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-info waves-effect waves-light" id="btn_submit_products">Save</button>
                                            </div>
                                        </div>
                                   </div>
                              </form>
                        </div><!-- /.modal -->



         <!-- //////////////////////////////////////  Assign Item  Modal //////////////////////////////////////////////////////////////////////--->
                    <div class="modal fade assign-item-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                            <form id="formItem" method="post" action="{{ url('product-items') }}">
                            @csrf
                            <div class="modal-dialog" style="width:75%;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h4 class="modal-title" id="myModalLabel">Assign Item to Make </h4>
                                    </div>
                                    <div class="modal-body">
                                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="control-label">Item Names * </label>
                                            <div class="row m-t-10">
                                            @foreach($items as $key=>$item)
                                                <div class="col-md-2">
                                                <input type="checkbox" name="item_id[]" value="{{ $item->id }}"> {{ $item->item_name }}
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="row m-t-10">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="control-label"> Make  *</label>
                                        <select name="brand_id" class="form-control select2" id="brand_id" required>
                                            <option value=""> Select Make </option>
                                            @foreach($brands as $key=>$brand)
                                            <option value="{{ $brand->id }}"> {{ $brand->title }} </option>
                                            @endforeach
                                    </select>       
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary waves-effect waves-light" id="btn_submit_item -#">Save</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </form>
            </div><!-- /.modal -->



         <!-- //////////////////////////////////////  Adjustment  Modal //////////////////////////////////////////////////////////////////////--->
                    <div class="modal fade adjustment-reason-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-md">
                          <form  action="{{ url('/adjustments')}}" method="POST" enctype="multipart/form-data"> 
                                @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="myLargeModalLabel">Add Adjustment Reason </h4>
                                </div>
                                
                                <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="control-label">Reason * </label>
                                            <input type="text" name="reason" id="reason" class="form-control" placeholder="Reason" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                    <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save </button>
                                </div>
                            </form>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->


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


    $('#itemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('settings.items') }}',
           
        "columns": [
                // {data: 'id', name: 'id'},
                {data: 'item_name', name: 'item_name'},
                {data: 'Actions', name: 'Actions',orderable:false,serachable:false,sClass:'text-center'},
            ]
        });


        $('#assignedMakeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('settings.item-assigned-make') }}',
           
        "columns": [
                // {data: 'id', name: 'id'},
                {data: 'item_name', name: 'item_name'},
                {data: 'title', name: 'title'},
                {data: 'Actions', name: 'Actions',orderable:false,serachable:false,sClass:'text-center'},
            ]
        });


        $('#makeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('settings.make') }}',
           
        "columns": [
                // {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'Actions', name: 'Actions',orderable:false,serachable:false,sClass:'text-center'},

            ]
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
