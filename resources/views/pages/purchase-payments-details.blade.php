@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
</style>
<!-- whole sale setting -->
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="/">Home</a> 
            <a class="step" href=""> Supplier Payments </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1"> Supplier Payment Info </h4> <br/>
            </div>
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
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Supplier Name </th>
                                <th>Invoice Number</th>
                                <th>Invoice Amount</th>
                                <th>Invoice Date</th>
                                <th>Paid Amount</th>
                                <th>Unpaid Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($purchases as $key => $purchase)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $purchase->supplier_name }} </td>
                                <td><a href="{{ url('suppliers/payments/invoice-details/'.$purchase->invoice_number) }}"> {{ $purchase->invoice_number }} </a></td>
                                <td> {{number_format( $purchase->total_amount, 2) }}</td>
                                <td> {{ $purchase->created_date }}</td>
                                <td> {{number_format( $purchase->total_paid, 2) }}</td>
                                <td> {{ number_format(($purchase->total_amount_purchased - $purchase->total_paid),2)  }}</td>
                                <!-- <td> 
                                   
                                 
                                
                                </td> -->
                                <td> 
                                <button value="{{ $purchase->invoice_number }}" class="btn-rounded btn-sm btn-info waves-effect waves-light addPayment"> <i class="fa fa-plus"></i> Payment </button>
                                <!-- <a href="{{ url('purchases/payment-details/'.$purchase->invoice_number) }}" class="btn-rounded btn-sm btn-success waves-effect waves-light addPayment"> <i class="fa fa-eye"></i> </a> -->
                                </td>       
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
                   </div>
                </div><br/>
            </div>
        </div>
    </div>
</div>
<!-- END  -->


<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="payment_modal">
<form id="AddPaymentForm" method="post" action="{{ url('accounts/purchase-payments/add-payment') }}">
             @csrf
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="mySmallModalLabel">Add Payment  </h4>
                </div>
                <div class="modal-body">
                <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Supplier Name  : </label>
                                <input type="text" name="supplier_name"  id="supplier_name" readonly class="form-control">
                                <input type="hidden" name="supplier_id"  id="supplier_id"  class="form-control">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Invoice Number  : </label>
                                <input type="text" name="invoice_number"  id="invoice_number" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Total Invoice Amount  : </label>
                                <input type="text" name="total" id="total_amount" readonly class="form-control">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">VAT Amount  : </label>
                                <input type="text" name="vat_amount" id="vat_amount" readonly class="form-control">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Amount To Pay : </label>
                                <input type="number" name="paid_amount" id="paid_amount" class="form-control" required placeholder="Enter Amount to Pay">
                             </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Upload Payment Recept  </label>
                                <input type="file" name="payment_recept" id="payment_recept" class="form-control">
                             </div>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col-md-12">
                             <div class="form-group">
                                <label for="" class="control-label">Date </label>
                                <input type="text" class="form-control created_date" required name="created_date" placeholder="Date Paid" id="datepicker-autoclose" data-date-format="yyyy-mm-dd" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-rounded btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn-rounded btn-primary waves-effect waves-light">Submit Payment </button>
                    </div>
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

    $('.addPayment').click(function() {
         var invoice_id = $(this).val();
         $.ajax({
             url: `../accounts/payments-details/${invoice_id}`,
             type: "GET",
             success: function (response) {
                 $('#payment_modal').modal('show');
                 $("#supplier_id").val(response[0].supplier_id);
                 $("#supplier_name").val(response[0].supplier_name);
                 $("#invoice_number").val(response[0].invoice_number);
                 $("#total_amount").val(response[0].total);
                 $("#vat_amount").val(response[0].vat_amount);
               } 
             
         });
    });

  });
  
</script>


@endsection