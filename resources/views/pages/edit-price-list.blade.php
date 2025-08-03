@extends('layouts.app_header')

@section('content')
<style>
input { text-transform: uppercase; }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-info btn-sm" href="{{ url('price-lists/clients/'.$products[0]->client_id) }}"> <i class="ti-arrow-left"></i>  Go Back</a>  
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1"> Edit Price  </h4>
                 </div>
            <div class="panel-body">
            <form id="edit" method="POST" action="{{ url('products/price-list/update') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #; border-bottom: red;">
                <div class="row m-t-30">
                <div class="col-md-1">
                <input type="hidden" name="id" class="form-control" value="{{ $products[0]->id }}"> 
                <input type="hidden" name="client_id" class="form-control" value="{{ $products[0]->client_id }}">
                <input type="hidden" name="product_id" class="form-control" value="{{ $products[0]->prod_id }}">      
      
                </div>
                    <div class="col-md-3">
                    <div class="form-group">
                                <label for="inputEmail3" class="control-label">Part Name   </label>
                                <input type="text" name="model" id="model" readonly class="form-control" placeholder="Make" value="{{ $products[0]->item_name }}">      
                            </div>
                    </div>
                    <div class="col-md-3">
                    <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make   </label>
                                <input type="text" name="model" id="model" readonly class="form-control" placeholder="Make" value="{{ $products[0]->title }}">      
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Model   </label>
                                <input type="text" name="model" id="model" readonly class="form-control" placeholder="Make" value="{{ $products[0]->model }}">      
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Part Number </label>
                                <input type="text" name="part_number" readonly id="part_number" class="form-control" placeholder="Part Number" value="{{ $products[0]->part_number }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Purchasing Price </label>
                                <input type="text" name="purchasing_price" id="purchasing_price" readonly class="form-control" placeholder="Purchasing Price"  value="{{ $products[0]->purchasing_price }}">

                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Selling Price </label>
                                <input type="text" name="selling_price" id="selling_price" class="form-control" placeholder="Selling Price"  value="{{ $products[0]->sale_price }}">
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
