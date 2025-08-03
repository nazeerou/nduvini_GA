@extends('layouts.app_header')

@section('content')
<style>
 #input_disabled { background: #fff; } 
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('settings/system') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title1"> Edit General Setting</h3>
                 </div>
            <div class="panel-body">
            <form id="edit" method="POST" action="{{ url('setting/general/update') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #;">
                  <div class="row m-t-10">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <input type="hidden" name="id" value="{{ $settings->id }}"  class="form-control" style="background: #fff;">       
                            <label for="inputEmail3" class="control-label">Business Name  </label>
                            <input type="text" name="business_name" value="{{ $settings->business_name }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Type of Business  </label>
                            <input type="text" name="type" value="{{ $settings->type }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                 </div>
                 <div class="row m-t-10">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Upload LOGO   </label>
                                <input type="file" name="logo_file" id="logo_file" required class="form-control">
                            </div>
                      </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Address  </label>
                            <input type="text" name="address" value="{{ $settings->address }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                 </div>
                 
                 <div class="row">
                 <div class="col-md-1"></div>
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