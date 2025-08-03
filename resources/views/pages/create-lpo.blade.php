@extends('layouts.app_header')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-info btn-sm" href="{{ url('purchase-history') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-titl"> Create Local Purchase Order (LPO)  </h4>
                 </div>
            <div class="panel-body">
            <form  method="POST" action="{{ url('purchases/create-lpo/post') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 1px; border: 1px solid #ddd;">
                  <div class="row m-t-15">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Supplier Name  </label>
                            @foreach($purchases as $p)
                            <input type="text" name="supplier_name" value="{{ $p->supplier_name }}" readonly  class="form-control">       
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Invoice No#  </label>
                            @foreach($purchases as $p)
                            <input type="text" name="invoice_number" value="{{ $p->invoice_number }}" readonly class="form-control">       
                            @endforeach
                        </div>
                    </div>
               </div>
               <div class="row m-t-10">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Reference Number   *</label>
                            <input type="text" name="lpo_number" placeholder="Enter Reference Number" required class="form-control">   
                            
                            <input type="hidden" name="invoice_date" value="{{ $invoice_date }}" class="form-control">       
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="form-group">
                            <label for="inputEmail3" class="control-label">Upload LPO File  </label>
                            <input type="file" name="lpo_file"  class="form-control">       
                        </div>
                    </div>
               </div>
               <div class="row m-t-10">
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