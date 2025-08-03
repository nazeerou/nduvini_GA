@extends('layouts.app_header')

@section('content')
<style>
 #input_disabled { background: #fff; } 
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('job-cards') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-titl"> Create Tax Invoice  </h3>

                 </div>
            <div class="panel-body">
            <form  method="POST" action="{{ url('invoices/create-invoice/post') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 1px; border: 1px solid #ddd;  border-bottom: red;">
                  <div class="row m-t-15">
                    <div class="col-md-1"></div>
                    <input type="hidden" name="reference" value="{{ $reference_id }}" class="form-control"> 
                    <input type="hidden" name="client_id" value="{{ $invoices[0]->client_id }}" class="form-control">       
                    <input type="hidden" name="estimate_ref" value="{{ $invoices[0]->estimate_reference }}" class="form-control">       
                    <input type="hidden" name="vehicle_reg" value="{{ $invoices[0]->vehicle_reg }}" class="form-control">       
                    <?php 
                     if ($invoices[0]->account_no < 10) {
                          $account = '00'.$invoices[0]->account_no;
                     } else if($invoices[0]->account_no < 100) {
                        $account = '0'.$invoices[0]->account_no;
                     } else {
                        $account = $invoices[0]->account_no;
                     }
                    ?>
                    <input type="hidden" name="account" value="{{ $invoices[0]->account_prefix }}{{ $account }}" class="form-control">       
                    <input type="hidden" name="make" value="{{ $invoices[0]->make }}  {{ $invoices[0]->model }}" class="form-control">       

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Job Card   </label>
                            <input type="text" name="job_card_no" value="{{ $invoices[0]->job_card_no }}" readonly  class="form-control">       
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Bill Amount  </label>
                            <input type="text" name="bill_amount" value="{{ $invoices[0]->amount }}" readonly class="form-control">       
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Invoice No.  </label>
                            <input type="text" name="invoice_no" value="{{ $invoice_no }}" readonly class="form-control">       
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Status  </label><br/>
                            <h4>
                              @if ($invoices[0]->status == 0)
                                <span class="label label-info">In Progress </span>
                                @elseif ($invoices[0]->status == 1) 
                                <span class="label label-success">completed</span>
                                @else
                                <span class="label label-warning">Cancelled</span>
                                @endif
                           </h4>
                        </div>
                    </div>
                    </div>
            </div>
           <div id="details" style="padding: 1px; border: 1px solid #ddd;">
           <div class="row m-t-15">
           <div class="col-md-1"></div>
                    <div class="col-md-6">
                    <div class="form-group">
                            <label for="inputEmail3" class="control-label">Bank Details :  </label>
                        </div>
                    </div>
            </div>
                  <div class="row m-t-15">
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Account No.   </label>
                            <input type="text" name="account_no"  class="form-control" placeholder="Account No.">       
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Account Name  </label>
                            <input type="text" name="account_name" class="form-control" placeholder="Account Name">       
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Bank Name  </label>
                            <input type="text" name="bank_name" placeholder="Bank name"  class="form-control">       
                        </div>
                    </div>
               </div>
               <div class="row m-t-15">
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Branch  </label>
                            <input type="text" name="branch_name" placeholder="Branch name"  class="form-control">       
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Swift Code  </label>
                            <input type="text" name="swift_code" placeholder="swift code"  class="form-control">       
                        </div>
                    </div>
               </div>
</div>
               <div class="row">
               <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10">Create </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- END  -->

@endsection
