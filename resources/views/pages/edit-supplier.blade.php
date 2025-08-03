@extends('layouts.app_header')

@section('content')
<style>
 #input_disabled { background: #fff; } 
</style>
<div class="row m-t-10">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('suppliers') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Edit Supplier </h3>
                <!-- <br/>
                @if(session('message'))
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
            <form id="edit" method="POST" action="{{ url('suppliers/update') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #eee; border-bottom: red;">
                  <div class="row m-t-20">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <input type="hidden" name="id" value="{{ $suppliers[0]->id }}"  class="form-control" style="background: #fff;">       

                            <label for="inputEmail3" class="control-label">Supplier Name  </label>
                            <input type="text" name="supplier_name" value="{{ $suppliers[0]->supplier_name }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Mobile Number  </label>
                            <input type="text" name="phone" value="{{ $suppliers[0]->phone }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-success btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END  -->

@endsection