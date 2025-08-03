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
  font-family: 'Raleway', sans-serif;
    }
    .modal.active {
  z-index: 2000; /* Increase z-index for the active/modal being displayed */
}

</style>
<!-- whole sale setting -->
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">Home</a> 
            <a class="step" href="#"> Client Payment </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
            <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + Add Debit  </button>
                 </div>
                <h3 class="panel-title1"> Client Payments  </h3>
                <br/>
             
                <div class="row">
                <table id="datatable" class="table table-striped table-bordered">
                        <thead style="background-color: #DDDCCC;">
                            <tr>
                                <th> # </th>
                                <th>Client Name </th>
                                <th>Vehicle No#</th>
                                <th>Invoice No </th>
                                <th>Invoice Amount </th>
                                <th>Created Date</th>
                                <th>Paid Amount</th>
                                <th>Unpaid Amount</th>
                                <th>Balance </th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ strtoupper($product->client_name) }} </td>
                                <td> {{ strtoupper($product->vehicle_reg) }} </td>
                                <td> 
                                    {{ $product->invoice_number }} 
                                </td>
                                <td> {{ number_format($product->bill_amount, 2) }} </td>
                                <td> {{ strtoupper($product->created_date) }} </td>
                                <td> {{ number_format($product->paid_amount, 2) }} </td>
                                <td>
                                    @if($product->bill_amount > $product->paid_amount)
                                     {{ number_format($product->bill_amount - $product->paid_amount,2) }}
                                     @else 
                                     0.00
                                     @endif
                                </td>
                                <td>
                                   {{ number_format(($product->bill_amount -  $product->paid_amount), 2) }}
                                    </td>   
                                <td>
                                    <?php 
                                     $check = $product->bill_amount - $product->paid_amount;
                                    ?>
                                @if ($check == 0)
                                <button value="{{ $product->invoice_number }}" class="btn-rounded btn-sm btn-pink waves-effect waves-light"><i class="fa fa-check"></i>  Paid </button>
                                @else 
                                <button value="{{ $product->invoice_number }}" class="btn-rounded btn-sm btn-info waves-effect waves-light addPayment"> <i class="fa fa-plus"></i> Pay </button>
                                @endif
                                @if(!$product->invoice_number)
                                <a class="btn-rounded btn-sm btn-success waves-effect waves-light viewModal"> <i class="fa fa-eye"></i> View </a>   
                                @else
                                <a href="{{ url('clients/payments/view-payments/'.$product->invoice_number) }}" class="btn-rounded btn-sm btn-success waves-effect waves-light addPayment"> <i class="fa fa-eye"></i> View </a>   
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
</div>
<!-- END  -->

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="payment_modal">
      <form id="formUpdate" method="post" action="{{ url('clients/add-payments') }}">
             @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel"> Client Payment </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Invoice Number  </label>
                                <input type="hidden" name="client_id" readonly id="client_id" class="form-control" placeholder="">
                                <input type="text" name="invoice_number" readonly id="invoice_no" class="form-control" placeholder="">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Invoiced Amount  </label>
                                <input type="text" name="bill_amount" readonly id="bill_amount" class="form-control" placeholder="">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Amount   </label>
                                <input type="text" name="paid_amount" id="paid_amount" class="form-control" placeholder="Amount to Pay">
                             </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="" class="control-label">Date  </label>
                                <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Invoice Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-info b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->    
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-10"></div>
                    
</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Pay  </button>
                    </div>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->
        <!-- Alert Modal  --->

<!--  Modal content for the above example -->
<div class="modal modal-xl fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
            <form  action="{{ url('clients/add-clients-debit')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Add Clients Debit  </h4>
                    </div>
                
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-5">
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
                        <div class="col-md-5">
                        </div>  
                        </div>
                     <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Vehicle Reg  * </label>
                                <input type="text" name="vehicle_reg" id="vehicle_reg" class="form-control" placeholder="Vehicle Reg Number" required>             
                             </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make & Model  * </label>
                                <input type="text" name="make" id="make" class="form-control" placeholder="Eg. TOYOTA PRADO" required>
                            </div>
                        </div>  
                        </div>
                     </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Invoice No. * </label>
                                <input type="text" name="invoice_no" id="invoice" class="form-control" placeholder="Invoice No. " required>
                            </div>        
                         </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Invoice  Amount  * </label>
                                <input type="text" name="invoice_amount" id="invoice_amount" class="form-control" placeholder="Invoice Amount " required>
                            </div>
                           </div>  
                        </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                        <div class="col-md-5">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Invoice Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Invoice Date " id="datepicker1" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-info b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                        </div>
                      </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn-rounded btn-primary waves-effect waves-light">Save </button>
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

    jQuery('#datepicker1').datepicker();
            jQuery('#datepicker1').datepicker({
                autoclose: true,
                todayHighlight: true
            });

    $('.addPayment').click(function() {
         var invoice_no = $(this).val();
        //  console.log(invoice_no);
         $.ajax({
             url: `../clients/payments-details/${invoice_no}`,
             type: "GET",
             success: function (response) {
                 $('#payment_modal').modal('show');
                 $("#invoice_no").val(response[0].invoice_number);
                 $("#bill_amount").val(response[0].bill_amount);
                 $("#client_id").val(response[0].client_id);
               } 
           });
       });
    
       $('.viewModal').click(function() {
               alert('Bill number not Created');
       });
  });
  
</script>

@endsection
