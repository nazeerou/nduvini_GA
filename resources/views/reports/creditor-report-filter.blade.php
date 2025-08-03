@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a>
            <a class="step" href="#"> Suppliers Report </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1"> Creditors Reports </h4>
            </div>
            <div class="panel-body">
                <label for="product" class="col-sm-12 control-label">Filter </label><br/><br/>
              <div class="filter">
              <form id="search_form" method="GET" action="{{ url('purchases/creditors-report-pdf/') }}" target="_blank">
                  @csrf
                <div class="row">
                <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Supplier Name  * </label>
                            <select name="supplier_id" class="form-control select2" id="supplier_name" >
                                <option value=""> ------- Select Supplier ----- </option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"> {{ $supplier->supplier_name }} </option>
                                @endforeach
                           </select>
                         </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Year   * </label>
                                <select name="year" class="form-control select2" id="year">
                                <option value=""> ---  All Year --- </option>
                                 @foreach($years as $year)
                                   <option value="{{ $year->id }}"> {{ $year->current_year  }}</option>
                                 @endforeach
                                </select>
                             </div>
                    </div>
                    <div class="col-md-2 m-t-10">
                        <div class="form-group">
                                <button type="submit" style="background: #E74C3C; color: white;" class="btn  btn-rounded pull-right m-b-0 m-t-10" id="search"><span class="pull-left" style="padding: 5px;"> Generate </span>
                                <span class="pull-right"> <i class="fa fa-arrow-circle-o-right fa-2x"></i></span></button>
                        </div>
                    </div>
                    </form>
                </div>
               </div><br/><br/>
     
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END  -->

@endsection