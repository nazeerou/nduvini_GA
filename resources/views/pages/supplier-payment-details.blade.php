@extends('layouts.app_header')

@section('content')

<style>
  #input-detail {
      border: 1px solid red;
  }
</style>
<div class="row m-t-5">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="btn btn-sm btn-primary" href="{{ url('suppliers/payments') }}"> <i class="ti-arrow-left"></i>  Go Back</a> <br/>
        </div>
    </div>
</div>
    
<div class="row">
<div class="col-lg-4">
<div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h3> Payment Details  </h3> <hr/>
                 <h4 class="panel-title1"> 
                    Name : <strong>
                    @foreach ($supplier_name as $w)
                         {{ $w->supplier_name }}    
                    @endforeach
                     </strong> </br></br>
                     Invoice  No# : <b> #{{ $invoice }}  </b> </h4>
                <br/>
            </div>
</div>
</div>
    <div class="col-lg-8">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h5> Payment Details  </br></h5>
            </div>
            <div class="panel-body">
                    <table id="datatable-fixed-header9" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Date Paid </th> 
                                <th>Amount Paid </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($supplier_payments as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $product->created_date }}</td>
                                <td> {{ number_format($product->paid_amount, 2) }} </td>
                                <td> 
                                <button class="btn btn-success btn-sm  waves-effect waves-light edit-payment" data-id="{{ $product->id }}" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-edit"></i> Edit  </button>
								<a class="btn btn-sm btn-danger" href="{{ url('purchases/payments/delete/'.$product->id) }}"  onclick="return confirm('Are you sure you want to delete this Item?');"><i class="fa fa-trash"></i> </a>                                 </td>
                            </tr>
                          @endforeach  
                               <tr>
                                 <td colspan="2"> <h5 align="right"> Total Amount Paid &nbsp; : </h5> </td>
                                 <td>  <h5> {{ number_format($total_purchases, 2) }} </h5> </td> 
                                 <td></td>
                                </tr>
                            </tbody>
                        </table>
            
                      </div>
                  </div>
            </div>
      </div>
</div>

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;" id="user_modal">
<form id="formUpdate" method="post" action="{{ url('purchases/payments/update') }}">
             @csrf
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="mySmallModalLabel">Edit Purchase Payment </h4>
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
             url: `../../purchases/payments/details/${payment_id}`,
             type: "GET",
             success: function (response) {
                 $('#user_modal').modal('show');
                 $("#paid_amount").val(response.paid_amount);
                 $("#id").val(response.id);
             }
         });
    });
  });
  
</script>
@endsection
