@extends('layouts.app_header')

@section('content')

<div class="row m-t-5">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> >>
            <a class="step" href="#"> Store </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + New Store </button>
                 </div>
                 <h3 class="panel-title"> Stores </h3>

                <br/>
            </div>
        
            <div class="panel-body">
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
                        @endif
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Store </th>
                                <th>Location</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($stores as $key => $store)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $store->store_name }} </td>
                                <td> {{ $store->location }} </td>
                                <td>
                                    <i class="ti-eye"></i> 
                                    <i class="ti-pencil"></i> 
                                    <i class="ti-trash"></i> 
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
            <div class="modal-dialog modal-lg">
            <form  action="{{ url('/stores')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Add Store </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Store Name * </label>
                                <input type="text" name="store_name" id="store_name" class="form-control" placeholder="Store Name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Region  * </label>
                                <input type="text" name="region" id="region" class="form-control" placeholder="Region" required>

                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Location * </label>
                                <input type="text" name="location" id="location" class="form-control" placeholder="Location">
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save </button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


@endsection