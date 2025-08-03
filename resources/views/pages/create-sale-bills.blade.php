@extends('layouts.app_header')

@section('content')
<style>
 #input_disabled { background: #fff; } 
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('sales-summary') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-titl"> Create Bill  </h3>
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
            <div class="panel-body">
            <form  method="POST" action="{{ url('sales/add-bills/post') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 1px; border: 1px solid #ddd;  border-bottom: red;">
                  <div class="row m-t-15">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Issued To  </label>
                            <input type="text" name="vehicle_reg" value="{{ strtoupper($vehicle) }}" readonly  class="form-control">       
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Created Date  </label>
                            <input type="text" name="created_date" value="{{ $date }}" readonly class="form-control">       
                        </div>
                    </div>
               </div>
               <div class="row m-t-10">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Billed Amount  </label>
                            @foreach($total_bill_amount as $b)
                            <input type="text" name="bill_amount" readonly value="{{ number_format($b->total_billed_amount, 2) }}" class="form-control">     
                            @endforeach  
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Bill Number  </label>
                            <input type="text" name="bill_number" placeholder="Enter Bill Number"  class="form-control">       
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