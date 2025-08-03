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

 textarea {
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
    border: 1px solid #ddd;
}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Retun Job </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + Create Job (RJ) </button>
                 </div>
                 <h3 class="panel-title1"> Return Jobs  </h3>
                <br/>
            </div>
        
            <div class="panel-body">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Client Name</th>
                                <th>Description</th>
                                <th>Reasons</th>
                                <th>Return Date </th>
                                @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                                <th>Created By </th>
                                @else
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($stocks as $key => $stock)
                            <tr>
                                <td width="20px">{{ $key+1 }}</td>
                                <td>{{ strtoupper($stock->client_name ) }} </td>
                                <td>{{ strtoupper($stock->vehicle_reg ) }} </td>
                                <td>{{ $stock->reasons }}</td>
                                <td>{{ $stock->created_date }} </td>
                                @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                                <td>{{ $stock->fname }} {{ $stock->lname }} </td>
                                @else 
                                @endif
                                <td>
                                <button type="button" name="submit" data-id="{{ $stock->vehicle_reg }}" class="btn  btn-sm btn-info waves-effect waves-light edit_labour"><i class="fa fa-eye"></i></button>
                                <button type="button" name="submit" data-id="{{ $stock->vehicle_reg }}" class="btn  btn-sm btn-success waves-effect waves-light edit_labour"><i class="fa fa-edit"></i></button>
			       @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                                <a class="btn btn-sm btn-danger show-details" href="{{ url('/details/delete/'.$stock->vehicle_reg) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>   
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



  <!-- Modal -->
  <div class="modal modal-xl fade bs-example-modal-lg" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true"  id="myModal" role="dialog">
            <div class="modal-dialog modal-lg">
            <form id="formPost" method="POST" action="{{ url('/stocks/return-jobs/add') }}" enctype="multipart/form-data"> 
                  @csrf
               <!-- Modal content-->
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                     <h4 class="modal-title">Return Job </h4>
                  </div>
                  <div class="modal-body">
                  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Client Name * </label>
                                <select name="client_id" class="form-control select2" id="client_id" required >
                                <option value=""> ---Select Client --- </option>
                                @foreach($clients as $client)
                                   <option value="{{ $client->id }}">  {{ $client->client_name }}  </option>
                                 @endforeach
                                </select>
                             </div> 
                          </div>
                          <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label"> Registration   </label>
                                <input type="text" name="vehicle_reg"  class="form-control"id="vehicle_reg" placeholder="Enter Vehicle Reg... ">
                       </div>  
                    </div>
                   <div class="col-md-7"></div>
               </div>
                  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Part Name   * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id">
                                <option value=""> --- Select Parts --- </option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->id }}">  {{ $product->item_name }}  -{{ $product->title }} | {{ $product->model }}  :  ({{ $product->quantity }}) </option>
                                 @endforeach
                                </select>
                             </div>
                         </div>
                  </div>

     <div id="client_details" style="padding: 0px; border: 0px solid #ddd;">
         <div class="row m-t-20">
        <div class="col-md-12">
        <section id='Table'>
        <table class="table table-striped table-bordered" style="background: #fff;" id="myTable">
            <thead>
                <tr>
                    <th width="10">#</th>
                    <th width="550px">Part Name</th>
                    <th width="10">Qty</th>
                    <th width="40">Action</th>
                </tr>
            </thead>
            <tbody id='dataRows'></tbody>
           </table>
          </section>
        </div>
      </div>  
    </div>
    <div class="row m-t-10">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Reasons   * </label>
                            <textarea id="myTextarea" name="reasons" rows="4" cols="50"></textarea>
                             </div>
                         </div>
                  <div class="col-md-3">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                </div>
           </div>
</div>
    <div class="modal-footer">
            <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
            <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save  </button>
        </div>
</div>
</div>
</div>
</div>    
</div>
</form> <!-- End form -->
</div>
</div>


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
            console.log(product_id);
            $.ajax({
                type: "GET",
                url: "../../products/price-lists/details/"+product_id,
                data: { product_id: product_id },
                success: function(response){
                    console.log(response);
                    $("#dataRows").show();
                    var html = '<tr class="target">';
                    html += '<td>'+'<input type="hidden" name="id[]" id="product_id" value="'+response[0].pid+'">'+'</td>';
                    html += '<td width="100px">'+response[0].item_name+'</td>';
                    html += '<td>'+'<input type="text" name="qty[]" class="qty" size="5" id="qty[]" required>'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows').append(html);
                }
            });
        });
});

</script>
@endsection
