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
            <a class="step" href="#">USER ROLES </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <h4 class="panel-title1"> USER ROLES AND PERMISSIONS  </h4>
            </div>
        
            <div class="panel-body">
                    
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif

<section>
    <div style="float: right">
        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="fa fa-plus"></i> Add Role </a>
    </div> 
    <div><br/>
        <table id="datatable" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th></th>
                    <th>Role </th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $key=>$role)
                <tr>
                    <td width="29">{{$key+1}}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->description }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" onclick="openEditModal({{ $role->id }})" class="open-EditroleDialog btn btn-link"><i class="fa fa-edit"></i> &nbsp;Edit
                                </button>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="{{ route('role.permission', ['id' => $role->id]) }}" class="btn btn-link"><i class="fa fa-unlock-alt"></i> &nbsp;Change Permission</a>
                                </li>
                                @if($role->id > 2)
                                <li>
                                    &nbsp;
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm">
                                    <i class="fa fa-trash"></i>&nbsp;&nbsp; Delete                                 
                                   </button>
                                </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<!-- Add Role Modal -->
<div id="createModal" class="modal fade text-left" tabindex="-1" role="dialog" aria-labelledby="createLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="createRoleForm" action="{{ route('roles.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="createLabel">Add Role</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <small class="text-muted">Fields marked with * are required.</small>

                <div class="form-group mt-3">
                    <label for="name"><strong>Role Name *</strong></label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description"><strong>Description</strong></label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>

                <input type="hidden" name="guard_name" value="web">
                <input type="hidden" name="is_active" value="1">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Role</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Role Modal -->
<div id="editModal" class="modal fade text-left" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editRoleForm" action="" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editLabel">Update Role</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="role_id" id="edit-role-id">

                <div class="form-group">
                    <label><strong>Role Name</strong></label>
                    <input type="text" name="name" id="edit-role-name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label><strong>Description</strong></label>
                    <textarea name="description" id="edit-role-description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Update Role</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
</div>
</div>

<!-- Trigger -->

<script type="text/javascript">

    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #role-menu").addClass("active");

	 function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    function openEditModal(role) {
        $('#edit-role-id').val(role.id);
        $('#edit-role-name').val(role.name);
        $('#edit-role-description').val(role.description ?? '');
        $('#editRoleForm').attr('action', '/roles/' + role.id);
        $('#editModal').modal('show');
    }

</script>
@endsection