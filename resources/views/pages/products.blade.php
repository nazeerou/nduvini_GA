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
            <a class="step" href="#"> Spare Parts </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + New Part </button>
                 </div>
                 <h3 class="panel-title"> All Spare Parts </h3>
                <br/>
            </div>
        
            <div class="panel-body">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Item Name</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Part number</th>
                                <th>Qty </th>
                                <th width="100px">Purchase Unit</th>
                                <th width="120px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }} </td>
                                <td> {{ strtoupper($product->model) }} </td>
                                <td> {{ strtoupper($product->part_number) }} </td>
                                <td> {{ $product->quantity }} </td>
                                <td>{{ strtoupper($product->purchase_unit) }}</td>
                                <td>
                                <a class="btn btn-sm btn-info" href="{{ url('products/view-details/'.$product->id) }}"><i class="fa fa-eye"></i> </a>
                                <a class="btn btn-sm btn-success" href="{{ url('products/edit/'.$product->id) }}"><i class="fa fa-edit"></i> </a>
				 @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                                <a class="btn btn-sm btn-danger" href="{{ url('products/delete/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>
                                 @else 
                                 @endif
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
            <form  action="{{ url('/products')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Add Part or Item </h4>
                    </div>
                    <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}"  class="form-control">      
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Item Name * </label>
                                <select name="product_id" class="form-control select2" id="product_id" required>
                                  <option> Select Item </option>
                                   @foreach($items as $p)
                                   <option value="{{ $p->id }}"> {{ $p->item_name }} </option>
                                   @endforeach
                             </select>        
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make  * </label>
                                <select name="brand_id" class="form-control select2" id="brand_id" required>
                             </select>        

                            </div>
                        </div>
                       
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Model  </label>
                                <input type="text" name="model" id="model" class="form-control" placeholder="Make" required>      

                            </div>
                        </div>

                    </div>
                    <div class="row">
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Part Number </label>
                                <input type="text" name="part_number" id="part_number" class="form-control" placeholder="Part Number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Purchasing Price </label>
                                <input type="text" name="purchasing_price" id="purchasing_price" class="form-control" placeholder="Purchasing Price" required>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Selling Price </label>
                                <input type="text" name="selling_price" id="selling_price" class="form-control" placeholder="Selling Price" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Quantity </label>
                                <input type="text" name="quantity" id="quantity" class="form-control" placeholder="Quantity" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Purchase Unit of Measure </label>
                                <input type="text" name="purchase_unit" id="purchase_unit" class="form-control" placeholder="Purchase unit of measure" required>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Alert Quantity </label>
                                <input type="text" name="alert_qty" id="alert_qty" class="form-control" placeholder="Alert Quantity">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Description </label>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Description">
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



<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('#product_id').on('change', function() {
         var product_id = $('#product_id').val();
             $.ajax({
                    type: "get",
                    url: "/product/fetch-assigned-items",
                    data: { product_id: product_id },
                    success: function(response){
                        $('#brand_id').html(response);
                    }
                });
            });
        });

</script>
@endsection
