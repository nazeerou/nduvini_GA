@extends('layouts.app_header')

@section('content')
<style>
 #input_disabled { background: #fff; } 
</style>
<div class="row m-t-10">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-primary btn-sm" href="{{ url('users') }}"> <i class="ti-arrow-left"></i>  Go Back</a> 
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-title"> Edit User </h3>
                 </div>
            <div class="panel-body">
            <form id="edit" method="POST" action="{{ url('users/update') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 10px; border: 1px solid #ddd; background: #eee; border-bottom: red;">
                  <div class="row m-t-10">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Firstname </label>
                            <input type="text" name="fname" value="{{ $users->fname }}"  class="form-control" style="background: #fff;">      
                            <input type="hidden" name="id" value="{{ $users->id }}"  class="form-control" style="background: #fff;">                                
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Lastname  </label>
                                <input type="text" name="lname"  value="{{ $users->lname }}" class="form-control" placeholder="">
                            </div>
                        </div>
                  </div>
        
                <div class="row">
                 <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Email Name </label>
                                <input type="text" name="email" value="{{ $users->email }}" class="form-control" id="input_disabled">
                            </div>
                       </div>
                       <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Mobile    </label>
                               <input type="text" name="mobile"  value="{{ $users->mobile }}" class="form-control">
                             </div>
                       </div>
                     </div> 
                    <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Branch    </label>
                                <select name="branch_id" class="form-control select2" id="branch_id">
                                <option value="{{ $users->id }}"> </option>
                                <option value="1"> Dar es Salaam </option>
                                <option value="3"> Dodoma </option>
                                <option value="2"> TABORA  </option>
                                </select>
                             </div>
                       </div>
                  </div>
                 </div> 
                 
                <div id="client_details" style="padding: 10px; border: 1px solid #ddd; background: #eee;">
                            
                <div class="row">
                <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-success btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10">Update</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END  -->

@endsection
