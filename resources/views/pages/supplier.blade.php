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
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Suppliers </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + New Supplier </button>
                 </div>
                 <h3 class="panel-title"> Supplier Lists </h3>

                <br/>
            </div>
        
            <div class="panel-body">
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
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Supplier Name</th>
                                <th>Mobile Number</th>
                                <th>Date Registered </th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($suppliers as $key => $supplier)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $supplier->supplier_name }} </td>
                                <td> {{ $supplier->phone }} </td>
                                <td> {{ $supplier->created_at }} </td>
                                <td>
                                <a class="btn btn-sm btn-success" href="{{ url('suppliers/edit/'.$supplier->id) }}"><i class="fa fa-edit"></i> </a>
								<a class="btn btn-sm btn-danger" href="{{ url('suppliers/delete/'.$supplier->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>
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
            <form  action="{{ url('/suppliers')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Add New Supplier </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Supplier Name </label>
                                <input type="text" name="supplier_name" id="supplier_name" class="form-control" placeholder="Supplier Name">
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">TIN </label>
                                <input type="text" name="TIN" id="TIN" class="form-control" placeholder="TIN">
                            </div>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Mobile </label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Mobile Number">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save </button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->



        <script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

</script>
@endsection