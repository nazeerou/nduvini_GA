@extends('layouts.app_header')

@section('content')
<style>
    th {
  /* font-weight: 400; */
  background: #7093cc;
  color: #FFF;
  text-transform: uppercase;
  font-size: 0.8em;
  font-family: 'Raleway', sans-serif;
 }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">Home</a>
            <a class="step" href="#"> Petty Cash Settings </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + Add  </button>
                 </div>
                 <h3 class="panel-title"> Petty Cash Categories </h3>

                <br/>
            </div>
            <div class="panel-body">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Name </th> 
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($petty_cash_settings as $key => $p)
                            <tr>
                                <td width="15px">{{ $key+1 }}</td>
                                <td> {{ $p->name }} </td>
                                <td>
                                <a class="btn btn-sm btn-success" href=""><i class="fa fa-edit"></i> </a>
                                @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                                <a class="btn btn-sm btn-danger" href="{{ url('petty-cash-settings/delete/'.$p->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>
                                @else 
                                @endif    
                            </td>       
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
            
                      </div>
                  </div>
            </div>
      </div>
</div>
<!-- END  -->
<!--  Modal content for the above example -->
        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-md">
            <form  action="{{ url('/accounts/petty-cash/settings') }}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Add Petty Cash Categories  </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Name </label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Enter Categories">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" class="btn-rounded btn-lg btn-primary waves-effect waves-light">Save </button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
</div>


@endsection
