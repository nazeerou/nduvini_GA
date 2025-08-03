@extends('layouts.app_header')

@section('content')
<style>
 #input_disabled { background: #fff; } 
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('estimations-history') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-titl"> Create Job Sheet  </h3>

                 </div>
            <div class="panel-body">
            <form  method="POST" action="{{ url('job-cards/create-job-cards/post') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 1px; border: 1px solid #ddd;  border-bottom: red;">
                  <div class="row m-t-15">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                    <input type="hidden" name="client_id" value="{{ $sales[0]->client_id }}" readonly class="form-control">       
                    <input type="hidden" name="reference" value="{{ $sales[0]->reference }}" readonly class="form-control">       
                    @foreach($sales as $s)
                     <input type="hidden" name="id[]" value="{{ $s->id }}">
                    @endforeach
                    @foreach($sales as $s)
                     <input type="hidden" name="product_id[]" value="{{ $s->product_id }}">
                    @endforeach
                    @foreach($sales as $s)
                     <input type="hidden" name="qty[]" value="{{ $s->qty }}">
                    @endforeach
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Vehicle Reg.  </label>
                            <input type="text" name="vehicle_reg" value="{{ strtoupper($sales[0]->vehicle_reg) }}" readonly  class="form-control">       
                        </div>
                    </div>
              
               </div>
               <div class="row m-t-10">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Estimated Amount  </label>
                            <!-- @foreach($total_bill_amount as $b) -->
                            <input type="text" name="bill_amount" readonly value="{{ $grand_total_amount }}" class="form-control">     
                            <!-- @endforeach   -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Job Card No.  </label>
                            <input type="text" name="job_card_no" value="{{ $job_card_no }}" placeholder="EnterInvoice Number"  class="form-control">       
                        </div>
                    </div>
               </div>
               <div class="row">
               <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="form-group m-t-15">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END  -->

@endsection
