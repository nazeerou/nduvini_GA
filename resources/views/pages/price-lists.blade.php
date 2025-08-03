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
            <a class="step" href="#"> Pricing </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h4 class="panel-title1"> Price Listings </h4>
                <br/>
            </div>
           
            <div class="panel-body">
                 <h4 class="panel-title1" align="center" id="client_name"> PRICE LIST </h4>
                    <table id="datatable" class="table table-striped table-bordered">
                    <thead>
                      </tr>
                            <tr>
                                <th></th>
                                <th>Client Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="display_result">
                        <tr>
                                <td width="25">1 </td>
                                <td> GENERAL PRICE LIST </td>
                                <td>
                                <a class="btn btn-sm btn-info" href="{{ url('price-lists/clients/'.'0') }}"><i class="fa fa-eye"></i> View Price Sheet </a>
                            </tr>
                        @foreach($clients as $key => $c)
                            <tr>
                                <td width="25"> {{ $key+2 }}</td>
                                <td> {{ $c->client_name }} </td>
                                <td>
                                <a class="btn btn-sm btn-info" href="{{ url('price-lists/clients/'.$c->id) }}"><i class="fa fa-eye"></i> View Price Sheet </a>
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
                      </div>
                  </div>
            </div>
      </div>
<!-- </form> -->
</div>
<!-- END  -->
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
                                    <option> Select Client </option>
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

<script>
        $(document).ready(function () {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
       });


      $("#client_idp").on('change', function () {
        
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
    var client_id = $("#client_id").val();
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
        data: { checkedData: checkedData, client_id: client_id },
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
