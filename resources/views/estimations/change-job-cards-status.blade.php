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
                <h3 class="panel-titl"> Change Job Card Status  </h3>

                 </div>
            <div class="panel-body">
            <form  method="POST" action="{{ url('job-cards/update-status') }}" enctype="multipart/form-data"> 
                  @csrf
             <div id="details" style="padding: 1px; border: 1px solid #ddd;  border-bottom: red;">
                  <div class="row m-t-15">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Job Card #  </label>
                            <input type="text" name="job_card_no" value="{{ $job_cards[0]->job_card_no }}" readonly  class="form-control">       
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label">Current Job Status  </label><br/>
                            <h4>
                         
                              @if ($job_cards[0]->status == 0)
                                <span class="label label-info">In Progress </span>
                                @elseif ($job_cards[0]->status == 1) 
                                <span class="label label-success">completed</span>
                                @else
                                <span class="label label-warning">Cancelled</span>
                                @endif
                           </h4>
                        </div>
                    </div>
               </div>
               <div class="row m-t-10">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Change Status   * </label>
                                <select name="status" class="form-control select2" id="status" required>
                                <option value=""> --- Select Status --- </option>
                                   <option value="1"> Completed </option>
                                </select>
                             </div> 
                    </div>
                        </div>
                    </div>
               <div class="row">
               <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> </label>
                                <button type="submit" class="btn btn-success btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- END  -->

@endsection