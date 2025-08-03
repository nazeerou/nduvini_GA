@extends('layouts.app_header')

@section('content')

<style>
  #input-detail {
      border: 1px solid red;
  }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="btn btn-sm btn-primary" href="{{ url('clients/payments') }}"> <i class="ti-arrow-left"></i>  Go Back</a> <br/>
        </div>
    </div>
</div>
    
<div class="row">
<div class="col-lg-4">
<div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h3> Bill Details  </h3> <hr/>
                 <h4 class="panel-title1"> 
                    Name : <strong>
                    @foreach ($client_name as $w)
                         {{ strtoupper($w->client_name) }}  {{ strtoupper($w->place) }}  
                    @endforeach
                     </strong> </br></br>
                     Bill  No# : <b> #{{ $bill }}  </b> </h4>
                <br/>
            </div>
</div>
</div>
    <div class="col-lg-8">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h4> Payment Details  </br></h4>
            </div>
            <div class="panel-body">
                    <table id="datatable-fixed-header9" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Date Paid </th> 
                                <th>Amount Paid </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($client_payments as $key => $client)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $client->created_date }}</td>
                                <td> {{ number_format($client->paid_amount, 2) }} </td>
                                <td>
                                <a href="" class="btn btn-sm btn-pink"><i class="fa fa-print"></i> &nbsp; Receipt </a>
                                <button class="btn btn-success btn-sm  waves-effect waves-light edit-payment" data-id="{{ $client->id }}" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-edit"></i> Edit  </button>
			        <a class="btn btn-sm btn-danger" href="{{ url('clients/payments-delete/'.$client->id) }}"  onclick="return confirm('Are you sure you want to delete this Item?');"><i class="fa fa-trash"></i> </a>   
                                </td>            
                            </tr>
                          @endforeach  
                               <tr>
                                 <td colspan="2"> <h5 align="right"> Total Amount Paid &nbsp; : </h5> </td><td>  <h5> {{ number_format($total_paid, 2) }} </h5> </td> 
                                </tr>
                            </tbody>
                        </table>
            
                      </div>
                  </div>
            </div>
      </div>
</div>



<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;" id="user_modal">
<form id="formUpdate" method="post" action="{{ url('clients/payments-update') }}">
             @csrf
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="mySmallModalLabel">Edit Client Payment </h4>
                </div>
                <div class="modal-body">
                <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Amount  </label>
                                <input type="text" name="paid_amount" class="form-control" id="paid_amount">
                                <input type="hidden" name="id" class="form-control" id="id">
                            </div>
                        </div>
                    </div>
                   <div class="row">
                        <div class="col-md-12">
                             <div class="form-group">
                                <label for="" class="control-label">Date  </label>
                                <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Invoice Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-info b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->    
                            </div>
                        </div>
                    </div> 
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" class="btn btn-success waves-effect waves-light" id="update_btn">Update </button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
        </form>
    </div><!-- /.modal -->
</div></div>


<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit-payment').click(function() {
         var payment_id = $(this).data('id');
         $.ajax({
             url: `../../../clients/fetch-clients/payments/${payment_id}`,
             type: "GET",
             success: function (response) {
                console.log(response);
                 $('#user_modal').modal('show');
                 $("#paid_amount").val(response.paid_amount);
                 $(".created_date").val(response.created_date);
                 $("#id").val(response.id);
             }
         });
    });
  });
  
</script>
@endsection
