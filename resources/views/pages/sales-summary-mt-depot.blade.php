@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
    th {
  /* font-weight: 400; */
  background: #7093cc;
  color: #FFF;
  text-transform: uppercase;
  font-size: 0.8em;
  /*font-family: 'Raleway', sans-serif;*/
    }
</style>
<!-- whole sale setting -->
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">Home</a> 
            <a class="step" href="#"> Sales Summary </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Sales Summary (MT DEPOT DAR ES SALAAM)  </h3>
                <br/>
                <div id="display_message" style="display: none"></div>
            </div>
            <!-- <div class="panel-body">
                <label for="product" class="col-sm-12 control-label">Filter </label><br/><br/>
              <div class="filter">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group m-t-10">
                        <select name="product_id" class="form-control select2" id="product_id">
                                  <option> --- All Month --- </option>
                                   <option value="">January </option>
                                   <option value="">February </option>
                                   <option value="">March </option>
                                   <option value="">April </option>
                                   <option value="">May </option>
                                   <option value="">June </option>
                                   <option value="">July </option>
                                   <option value="">Augost </option>
                                   <option value="">September </option>
                                   <option value="">October </option>
                                   <option value="">November </option>
                                   <option value="">December </option>

                                </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group m-t-10">
                                <div class="input-daterange input-group" id="date-range" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control" name="start" placeholder="Start Date" />
                                    <span class="input-group-addon b-0 text-white" style="background: palevioletred">to</span>
                                    <input type="text" class="form-control" name="end"  placeholder="End Date"/>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                                <button name="filter_by_all" style="background: #E74C3C; color: white;" class="btn btn-rounded pull-right m-b-20 m-t-10" id="search"><span class="pull-left" style="padding: 5px;"> Search </span>
                                <span class="pull-right"> <i class="fa fa-arrow-circle-o-right fa-2x"></i></span></button>
                        </div>
                    </div>
                </div> -->
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-12">
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
                    </div>
                </div>
                <div class="row">
                <table id="datatable" class="table table-striped table-bordered">
                        <thead style="background-color: #dddccc; color: #fff">
                            <tr>
                                <th> # </th>
                                <th> Client Name </th>
                                <th> Vehicle Reg. </th>
                                <th> Bill No </th>
                                <th> Bill Amount (VAT incl.)</th>
                                <th>Date </th>
                                <th style="text-align: center"> Payment Status </th>
                                <th> Location </th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td width="180px"> 
                                @if(!$product->reference)
                                    <a href="sales-summary/create-reference/{{ $product->vehicle_reg }}/{{ $product->created_date }}"> Create Reference {{ $product->client_name }} </a>
                                     @else
                                     {{ strtoupper($product->client_name) }}   {{ strtoupper($product->place) }}
                                     @endif
                                </td>
                                <td width="150px"> {{ strtoupper($product->vehicle_reg) }} </td>
                                <td width="150px">
                                    @if(!$product->bill_no)
                                    <a href="../sales-summary/create-bill/{{ $product->vehicle_reg }}/{{ $product->created_date }}"> Create Bill </a>
                                     @else
                                     {{ strtoupper($product->bill_no) }}
                                     @endif
                                     </td>
                                <td width="150px"> {{ number_format(($product->total_amount + $product->vat_amount), 2) }} </td>
                                <td width="150px">{{ $product->created_date }}</td>
                                <td width="150px" style="text-align: center"> 
                                @if ($product->paid_amount == Null)
                                <span class="label label-danger">Not Paid</span>
                                @elseif ($product->paid_amount < $product->total_amount) 
                                <span class="label label-warning">Partial</span>
                                @else
                                <span class="label label-success">Paid</span>
                                @endif
                                </td>
                                <td> 
                                @if ($product->selling_location == 1)
                                <span class="">MT DEPOT</span>
                                @else
                                <span class="">VINGUNGUTI</span>
                                @endif
                                </td>
                                <td width="150px">
                                <button class="btn btn-success btn-sm  waves-effect waves-light edit-details" data-id="{{ $product->reference }}" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-edit"></i></button>
                                @if(!$product->reference)
                                <a href="/sales/details/{{$product->vehicle_reg}}/{{$product->created_date}}" class="btn btn-info btn-sm waves-effect waves-light"><i class="fa fa-eye"></i> </a>
                                @else
                                <a href="/sales/details/{{$product->reference }}" class="btn btn-info btn-sm waves-effect waves-light"><i class="fa fa-eye"></i> </a>
                                @endif
                                <a class="btn btn-sm btn-danger" href="{{ url('sales-summary/delete/'.$product->reference) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i> </a>
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
</div>
<!-- END  -->

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="edit_sale_modal">
      <form id="formUpdate" method="post" action="{{ url('sales-summary/update') }}">
             @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Edit Sales </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Client Name  : </label>
                                <select name="client_name" class="form-control select2" id="client_name" required>
                                <option value="client_name">  </option>
                                 @foreach($clients as $client)
                                   <option value="{{ $client->id }}">  {{ $client->client_name }}  - ({{ $client->place }}) </option>
                                 @endforeach
                                </select>
                                <!-- <input type="text" name="client_name"  id="client_name" class="form-control" placeholder="Product Name"> -->
                                <input type="hidden" name="reference_no"  id="reference_no" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Vehicle Reg #  </label>
                                <input type="text" name="vehicle_reg" id="vehicle_reg" class="form-control" placeholder="">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="inputEmail3" class="control-label">Date </label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date Supplied" id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                         </div>
                        </div>
                    </div></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Update </button>
                    </div>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->



<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit-details').click(function() {
         var reference_no = $(this).data('id');
         $.ajax({
             url: `sales/edit/${reference_no}`,
             type: "GET",
             success: function (response) {
                 console.log(response[0].client_name);
                 $('#edit_sale_modal').modal('show');
                 $("#client_name").val(response[0].client + " "+ response[0].place);
                 $(".created_date").val(response[0].created_date);
                 $("#vehicle_reg").val(response[0].vehicle_reg);
                 $("#reference_no").val(response[0].reference);
             }
         });
    });
  });
</script>

@endsection