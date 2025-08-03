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

 div.dataTables_filter {
    text-align: left;
} 
</style>
<div class="row m-b-5">
    <div class="col-lg-12">
        <div style="float: right">
        <a  href="{{ url('price-lists') }}"> <i class="ti-arrow-left"></i>  GO BACK </a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
            <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + Set Price </button>
                 </div>
                 <h4 class="panel-title1"> Price Listings </h4>
                <br/>
            </div>
            <form id="formPost" method="POST" action="{{ url('products/client-prices/pdf') }}" enctype="multipart/form-data" target="_blank"> 
                  @csrf
            @if(!$id) 
            <input type="hidden" name="client" id="client" value="0">
            @else
            <input type="hidden" name="client" id="client" value="{{ $id }}">
            @endif
            <div class="panel-body">
                 <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-pink btn btn-sm waves-effect waves-light m-b-20 m-t-10" id="btn_pdf_"> <i class="fa fa-file-pdf-o"></i>&nbsp; PDF  </button>
                 </div>
                  <!-- <span id="loader" style="display: none;"> Loading ... </div> -->
                  @if(!$id)
                  <h4 class="panel-title1" align="center" id="client_name1"> GENERAL PRICE LIST </h4>
                  @else
                    <h4 class="panel-title1" align="center" id="client_name"> {{  strtoupper($clients[0]->client_name) }} PRICE LIST </h4>
                   @endif
                    <table id="datatable" class="table table-striped table-bordered">
                    <thead>
                      </tr>
                            <tr>
                                <th align="center"> <input type="checkbox" id="selectAll"> </th>
                                <th>Part Name</th>
                                <th>Description </th>
                                <th>Buying Price </th>
                                <th>Selling Price </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="display_result">
                        @foreach($stocks as $key => $product)
                            <tr>
                                <td width="30" style="text-align: center">
                                <input type="checkbox" class="itemCheckbox" name="selectedItems[]" value="{{ $product->id }}">
                                </td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }}  - 
                                 {{ $product->model }} | {{ $product->part_number }} </td>
                                <td>{{ number_format($product->purchasing_price, 2) }}</td>
                                <td>{{ $product->sale_price }}</td>
                                <td>
                                <!-- <a class="btn btn-sm btn-success" href="{{ url('products/edit-price-list/'.$product->id) }}"><i class="fa fa-edit"></i> </a> -->

                                <!-- <button class="btn btn-success btn-sm  waves-effect waves-light item_adjustment" data-id="{{ $product->id }}" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-edit"></i></button> -->
                                <a class="btn btn-sm btn-success" href="{{ url('price-lists/clients/'.$id.'/edit/'.$product->id) }}"><i class="fa fa-edit"></i> </a>
                                @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
								<a class="btn btn-sm btn-danger" href="{{ url('products/price-lists/delete/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>                                </td>       
                                @else 
                                @endif
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
                      </div>
                  </div>
            </div>
      </div>
</form>
</div>
<!-- END  -->

@if(!$id)

@else
<!--  Modal content for the above example -->
        <div class="modal modal-xl fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
            <form  action="{{ url('products/add-client-price-sheet')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Set Client Price  </h4>
                    </div>
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Client Name * </label>
                                <select name="client_id" class="form-control select2" id="client_id" required>
                                   <!-- <option> Select Client </option> -->
                                     @foreach($clients as $s)
                                     <option  value="{{ $s->id }}"> {{ $s->client_name }} </option>
                                     @endforeach
                             </select>              
                             </div>
                        </div>
                     </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Part Name * </label>
                                <select name="product_id" class="form-control select2" id="product_id" required>
                                    <option> Select Part </option>
                                     @foreach($products as $s)
                                     <option value="{{ $s->id }}"> {{ $s->item_name }} - {{ $s->title }} | {{ $s->model }} | {{ $s->part_number }} </option>
                                     @endforeach
                             </select>              
                             </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Selling Price  * </label>
                                <input type="text" name="selling_price" id="selling_price" class="form-control" placeholder="Selling Price  Eg. 30000" required>
                            </div>
                        </div>  
                        </div>
                    </div>

                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" name="submit" class="btn-md btn-rounded btn-primary waves-effect waves-light"> Save </button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

@endif
<script>
        $(document).ready(function () {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
       });

       if (!$.fn.DataTable.isDataTable('#datatable')) {
        // If not initialized, initialize DataTable
        $('#datatable').DataTable({
            "paging": false, // Disable pagination
            // "info": false // Disable info text
            // Other DataTable options...
        });
    }

    $('#client_ids').on('change', function() {
        // Get the selected value
        var selectedValue = $(this).val();

        // Update the URL parameter
        updateUrlParameter('clients', selectedValue);

        // Perform your AJAX request or any other actions here

    });

    // Function to update URL parameters
    function updateUrlParameter(key, value) {
        var url = window.location.href;
        var urlParams = new URLSearchParams(window.location.search);

        // Update or add the parameter
        if (urlParams.has(key)) {
            urlParams.set(key, value);
        } else {
            urlParams.append(key, value);
        }

        // Construct the new URL
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '/?' + urlParams.toString();

        // Update the URL without triggering a page reload
        window.history.pushState({ path: newUrl }, '', newUrl);
    }

      $("#client_id1").on('change', function () {
        
            var client_id = $("#client_id").val();
            $.ajax({
                type: "GET",
                url: "products/find-client-prices/"+client_id,
                data: { client_id: client_id },
                success: function(response){
                    console.log(response);
                    $('#display_result').html(response);
                    if(client_id === '0') {
                        $('#client_name').html('GENERAL PRICE LIST')
                    } else {
                        $('#client_name').html(response[0].client_name+ " "+ " "+ 'PRICE LIST')
                    }
                    // Assuming you have a DataTable initialized with id 'datatable'
                 var table = $('#datatable').DataTable();
                // Clear existing data 
                table.clear();

                }
            });
        });

    $('#selectAll').click(function() {
         $('.itemCheckbox').prop('checked', this.checked);
     });

 $("#go").on('click', function () {
    var client_id = $("#client_id").val();
            $.ajax({
                type: "GET",
                url: "products/find-client-prices/"+client_id,
                data: { client_id: client_id },
                success: function(response){
                    console.log(response);
                    $('#display_result').html(response);
                    if(client_id === '0') {
                        $('#client_name').html('GENERAL PRICE LIST')
                    } else {
                        $('#client_name').html(response[0].client_name+ " "+ " "+ 'PRICE LIST')
                    }
                   }
                });
});

$("#btn_pdf_").on('click', function () {
    var client_id = $("#client").val();
    var checkedData = [];
    // Use :checked selector to get only the checked checkboxes
    $(".itemCheckbox:checked").each(function() {
        checkedData.push($(this).val());
    });

    // Check the length of the checkedData array
    if (checkedData.length < 1) {
        alert('Please select at least one Spare Part');
        return;
    }

    // Send the checkedData and client_id to the server
    $.ajax({
        type: "GET",
        url: "/products/client-prices/pdf",
        data: { checkedData: checkedData, client: client },
        success: function(response) {
            // Assuming the response is the PDF content or a URL
            if (response) {
                console.log(response);

                // If the response is a URL, open it in a new window
                if (response.url) {
                    window.open(response.url, '_blank');
                } else {
                    // If the response is the PDF content, create a Blob and open it
                    var blob = new Blob([response], { type: 'application/pdf' });
                    var url = URL.createObjectURL(blob);
                    window.open(url, '_blank');
                }
            } else {
                console.warn('No valid response received.');
            }
        },
        error: function(error) {
            console.log(error);
        }
    });
});


});

</script>
@endsection
