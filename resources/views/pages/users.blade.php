@extends('layouts.app_header')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> Users </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div id="btn" style="float: right"> 
                <button type="submit" class="btn btn-info btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + Create User </button>
                 </div>
                 <h3 class="panel-title1">System  Users </h3>
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
                            <tr style="background: #ddd">
                                <th> # </th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th> Mobile </th>
                                <th> Branch Name</th>
                                <th>User Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($users as $key => $user)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $user->fname }} {{ $user->lname }} </td>
                                <td> {{ $user->email }} </td>
                                <td> {{ $user->mobile }} </td>
                                <td> {{ $user->branch_name }} </td>
                                <td> {{ $user->name }} </td>
                                <td>
                                @if($user->role_id == 1)
                                <a class="btn btn-sm btn-success" href="{{ url('users/edit/'.$user->id) }}"><i class="fa fa-edit"></i> </a>
								<a class="btn btn-sm btn-danger" href="{{ url('users/delete/'.$user->id) }}"  onclick="return confirm('Are you sure you want to delete this User?');"><i class="fa fa-trash"></i> </a> 
                                 @else 
                                 <button class="btn btn-info btn-sm  waves-effect waves-light btn-user" data-id="{{ $user->id }}" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-plus"></i> Assign Role  </button>
                                 <a class="btn btn-sm btn-success" href="{{ url('users/edit/'.$user->id) }}"><i class="fa fa-edit"></i> </a>
								<a class="btn btn-sm btn-danger" href="{{ url('users/delete/'.$user->id) }}"  onclick="return confirm('Are you sure you want to delete this User?');"><i class="fa fa-trash"></i> </a>
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
            <form  action="{{ url('create-user') }}" method="POST"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Create New User </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">First Name * </label>
                                <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name" required>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Last Name * </label>
                                <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name" required>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Email  * </label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>

                            </div>
                        </div>
                       </div>
                       <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Mobile   </label>
                                <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile number">

                            </div>
                        </div>
                       </div>
                       <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Branch Name*</label>
                                <select name="branch_id" class="form-control select2" id="branch_id" required>
                                    <option> Select Branch </option>
                                    @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->branch_name }}</option>
                                    @endforeach
                             </select>       
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save </button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
</div>


<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;" id="user_modal">
<form id="formUpdate" method="post" action="{{ url('users/update/roles') }}">
             @csrf
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="mySmallModalLabel">Assign or Change Role </h4>
                </div>
                <div class="modal-body">
                <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Full name   </label>
                                <input type="text" name="user" readonly class="form-control" id="user">
                                <input type="hidden" name="id" class="form-control" id="id">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="" class="control-label">Role   * </label>
                                <select name="role_id" class="form-control select2" required>
                                  <option value=""> Select Role </option>
                                   @foreach($roles as $p)
                                   <option value="{{ $p->id }}"> {{ strtoupper($p->name) }} </option>
                                   @endforeach
                             </select>        
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" class="btn btn-success waves-effect waves-light" id="update_btn">Update </button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
        </form>
    </div><!-- /.modal -->
</div></div>





<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.btn-user').click(function() {
         var user_id = $(this).data('id');
         $.ajax({
             url: `users/details/${user_id}`,
             type: "GET",
             success: function (response) {
                 $('#user_modal').modal('show');
                 $("#user").val(response.fname + " "+ " "+ response.lname);
                 $("#id").val(response.id);
             }
         });
    });
  });
  
</script>
@endsection