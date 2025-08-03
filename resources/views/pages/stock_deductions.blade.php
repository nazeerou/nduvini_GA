@extends('layouts.app_header')

@section('content')
<style>
th {
  /* font-weight: 400; */
  background: #7093cc;
  color: #FFF;
  text-transform: uppercase;
  font-size: 0.8em;
  font-family: 'Raleway', sans-serif;
 }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Stock Deduction </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + New Labour </button>
                 </div>
                 <h3 class="panel-title1"> Stock Deductions  </h3>
                <br/>
            </div>
        
            <div class="panel-body">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Created Date</th>
                                <th>Description</th>
                                <th>Reasons</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($products as $key => $product)
                            <tr>
                                <td width="20px">{{ $key+1 }}</td>
                                <td>{{ $product->created_date }} </td>
                                <td>{{ strtoupper($product->product_id) }} </td>
                                <td>{{ number_format($product->readons) }}</td>
                                <td>
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn  btn-sm btn-success waves-effect waves-light edit_labour"><i class="fa fa-edit"></i></button>
								<a class="btn btn-sm btn-danger show-details" href="{{ url('/details/delete/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>   
                            </td>       
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
            
                      </div>
                  </div>
            </div>
      </div>
</div>
<!-- END  -->


<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
            <form  action="{{ url('/settings/add-labour')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Add Labour Setting </h4>
                    </div>
                    <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}"  class="form-control">      
                    <div class="modal-body">
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Name  </label>
                                <input type="text" name="labour" id="labour" class="form-control" placeholder="Labour " required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Rate Per hour </label>
                                <input type="text" name="rate" id="rate" class="form-control" placeholder="Eg.  1 or 1.4 " required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Charge </label>
                                <input type="text" name="charge" id="charge" class="form-control" placeholder="Charge/Price Eg. 30000" required>
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
</div>


<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="labour_modal" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
            <form  action="{{ url('/settings/update-labour')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Update Labour  </h4>
                    </div>
                    <input type="hidden" name="id" id="id" class="form-control">      
                    <div class="modal-body">
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Name  </label>
                                <input type="text" name="labour" id="labour_name" class="form-control" placeholder="Labour " required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Rate Per hour </label>
                                <input type="text" name="rate" id="qty" class="form-control" placeholder="Eg.  1 or 1.4 " required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Charge </label>
                                <input type="text" name="charge" id="charge_" class="form-control" placeholder="Charge/Price Eg. 30000" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" name="submit" class="btn btn-success waves-effect waves-light">Update </button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->



<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

        $('.edit_labour').click(function() {
         var labour_id = $(this).data('id');
         $.ajax({
             url: `labours/fetch-details/${labour_id}`,
             type: "GET",
             success: function (response) {
                console.log(response);
                 $('#labour_modal').modal('show');
                 $("#labour_name").val(response.labour);
                 $("#charge_").val(response.charge);
                 $("#qty").val(response.rate);
                 $("#id").val(response.id);
             }
         });
    });
});

</script>
@endsection
