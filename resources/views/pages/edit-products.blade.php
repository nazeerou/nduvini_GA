@extends('layouts.app_header')

@section('content')
<style>
input { text-transform: uppercase; }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-info btn-sm" href="{{ url('parts') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1"> Edit Products  </h4>
                 </div>
            <div class="panel-body">
            <form id="edit" method="POST" action="{{ url('products/stocks/update') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #; border-bottom: red;">
                <div class="row m-t-30">
                <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Part Name   * </label>
                            <input type="hidden" name="id" id="id" class="form-control" placeholder="Make" value="{{ $products[0]->id }}">      
                                <select name="product_id" class="form-control select2" id="product_id">
                                <option value="{{ $products[0]->product_id }}"> {{ $products[0]->item_name }}</option>
                                @foreach($items as $p)
                                   <option value="{{ $p->id }}"> {{ $p->item_name }} </option>
                                   @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make  * </label>
                                <select name="brand_id" class="form-control select2" id="brand_id">
                                <option value="{{ $products[0]->brand_id }}"> {{ $products[0]->title }}</option>
                                 @foreach($brands as $p)
                                   <option value="{{ $p->id }}"> {{ $p->title }} </option>
                                   @endforeach
                             </select>        
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Model  * </label>
                                <input type="text" name="model" id="model" class="form-control" placeholder="Make" value="{{ $products[0]->model }}">      
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Part Number </label>
                                <input type="text" name="part_number" id="part_number" class="form-control" placeholder="Part Number" value="{{ $products[0]->part_number }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Purchasing Price </label>
                                <input type="text" name="purchasing_price" id="purchasing_price" class="form-control" placeholder="Purchasing Price"  value="{{ $products[0]->purchasing_price }}">

                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Selling Price </label>
                                <input type="text" name="selling_price" id="selling_price" class="form-control" placeholder="Selling Price"  value="{{ $products[0]->selling_price }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                  <div class="col-md-1"></div>
                    <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Quantity </label>
                                <input type="text" name="quantity" id="quantity" class="form-control" placeholder="Quantity"  value="{{ $products[0]->quantity }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Purchase Unit of Measure </label>
                                <input type="text" name="purchase_unit" id="purchase_unit" class="form-control" placeholder="Purchase unit of measure"  value="{{ $products[0]->purchase_unit }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Description </label>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Description"  value="{{ $products[0]->description }}">
                            </div>
                        </div>
                    </div>
                  <div class="row">
                  <div class="col-md-1"></div>
                    <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Alert Quantity </label>
                                <input type="number" name="alert_qty"  class="form-control" placeholder="Alert Quantity"  value="{{ $products[0]->alert_qty }}">
                            </div>
                        </div>
                       
                    </div>
                </div> 
                <div id="client_details" style="padding: 10px; border: 1px solid #ddd; background: #;">
                <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-success btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10">Update </button>
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
