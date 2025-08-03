@extends('layouts.app_header')

@section('content')
<style>
 #input_disabled { background: #fff; } 
</style>
<div class="row m-t-10">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('clients') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Edit Client </h3>
                 </div>
            <div class="panel-body">
            <form id="edit" method="POST" action="{{ url('clients/update') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #eee; border-bottom: red;">
                  <div class="row m-t-10">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <input type="hidden" name="id" value="{{ $clients[0]->id }}"  class="form-control" style="background: #fff;">       
                            <label for="inputEmail3" class="control-label">Client Name  </label>
                            <input type="text" name="client_name" value="{{ $clients[0]->client_name }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Abbreviation  </label>
                            <input type="text" name="abbreviation" value="{{ $clients[0]->abbreviation }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                 </div>
                 <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <input type="hidden" name="id" value="{{ $clients[0]->id }}"  class="form-control" style="background: #fff;">       
                            <label for="inputEmail3" class="control-label">Address  </label>
                            <input type="text" name="address" value="{{ $clients[0]->address }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Place  </label>
                            <input type="text" name="place" value="{{ $clients[0]->place }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                 </div>
                 <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Phone  </label>
                            <input type="text" name="phone" value="{{ $clients[0]->phone }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Email Address  </label>
                            <input type="text" name="email" value="{{ $clients[0]->email }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                 </div>
                 <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">TIN No.  </label>
                            <input type="text" name="tin" value="{{ $clients[0]->tin }}"  class="form-control" style="background: #fff;">       
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">VRN No.  </label>
                            <input type="text" name="vrn" value="{{ $clients[0]->vrn }}"  class="form-control" style="background: #fff;">       
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
