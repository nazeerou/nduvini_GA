@extends('layouts.app_header')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">Home</a>
            <a class="step" href="#">HR Settings</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="pull-left">HR Settings</h4>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                    <li role="presentation" class="active"><a href="#departments" role="tab" data-toggle="tab">Departments</a></li>
                    <li role="presentation"><a href="#designations" role="tab" data-toggle="tab">Designations</a></li>
                    <li role="presentation"><a href="#contract-types" role="tab" data-toggle="tab">Contract Types</a></li>
                    <li role="presentation"><a href="#salary-groups" role="tab" data-toggle="tab">Salary Groups</a></li>
                    <li role="presentation"><a href="#banks" role="tab" data-toggle="tab">Banks Settings</a></li>

                </ul>

                <!-- Tab panes -->
                <div class="tab-content">

                    <!-- Departments Tab -->
                    <div role="tabpanel" class="tab-pane fade in active" id="departments">
                        <button id="addDepartmentBtn" class="btn btn-primary" style="float: right;">Add Department</button>
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr><th>Name</th><th style="width: 130px;">Actions</th></tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $dept)
                                    <tr data-id="{{ $dept->id }}" data-name="{{ $dept->name }}">
                                        <td>{{ $dept->name }}</td>
                                        <td>
                                            <button class="btn btn-xs btn-info edit-department-btn">Edit</button>
                                            <button class="btn btn-xs btn-danger delete-department-btn">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Designations Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="designations">
                        <button id="addDesignationBtn" class="btn btn-primary" style="float: right;">Add Designation</button>
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr><th>Position</th><th style="width: 130px;">Actions</th></tr>
                            </thead>
                            <tbody>
                                @foreach($designations as $des)
                                    <tr data-id="{{ $des->id }}" data-title="{{ $des->name }}">
                                        <td>{{ $des->name }}</td>
                                        <td>
                                            <button class="btn btn-xs btn-info edit-designation-btn">Edit</button>
                                            <button class="btn btn-xs btn-danger delete-designation-btn">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Contract Types Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="contract-types">
                        <button id="addContractTypeBtn" class="btn btn-primary" style="float: right;">Add Contract Type</button>
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr><th>Type</th><th style="width: 130px;">Actions</th></tr>
                            </thead>
                            <tbody>
                                @foreach($contract_types as $type)
                                    <tr data-id="{{ $type->id }}" data-name="{{ $type->name }}">
                                        <td>{{ $type->name }}</td>
                                        <td>
                                            <button class="btn btn-xs btn-info edit-contract-type-btn">Edit</button>
                                            <button class="btn btn-xs btn-danger delete-contract-type-btn">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Salary Groups Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="salary-groups">
                        <button id="addSalaryGroupBtn" class="btn btn-primary" style="float: right;">Add Salary Group</button>
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr><th>Group Name</th><th>Basic Salary</th><th style="width: 130px;">Actions</th></tr>
                            </thead>
                            <tbody>
                                @foreach($salary_groups as $group)
                                    <tr data-id="{{ $group->id }}" data-group_name="{{ $group->group_name }}" data-basic_salary="{{ $group->basic_salary }}">
                                        <td>{{ $group->group_name }}</td>
                                        <td>{{ number_format($group->basic_salary, 2) }}</td>
                                        <td>
                                            <button class="btn btn-xs btn-info edit-salary-group-btn">Edit</button>
                                            <button class="btn btn-xs btn-danger delete-salary-group-btn">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Banks Tab -->
<div role="tabpanel" class="tab-pane fade" id="banks">
    <button id="addBankBtn" class="btn btn-primary" style="float: right;">Add Bank</button>
    <table class="table table-bordered mt-3">
        <thead>
            <tr><th>Bank Name</th><th style="width: 130px;">Actions</th></tr>
        </thead>
        <tbody>
            @foreach($banks as $bank)
                <tr data-id="{{ $bank->id }}" data-name="{{ $bank->name }}">
                    <td>{{ $bank->name }}</td>
                    <td>
                        <button class="btn btn-xs btn-info edit-bank-btn">Edit</button>
                        <button class="btn btn-xs btn-danger delete-bank-btn">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

                </div>

                <!-- Modals -->

                <!-- Department Modal -->
                <div class="modal fade" id="departmentModal" tabindex="-1" role="dialog" aria-labelledby="departmentModalLabel">
                    <div class="modal-dialog">
                        <form id="departmentForm" method="POST" class="modal-content">
                            @csrf
                            <input type="hidden" name="_method" id="departmentMethod" value="POST" />
                            <input type="hidden" name="id" id="departmentId" />
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" id="departmentModalLabel">Add Department</h4>
                            </div>
                            <div class="modal-body">
                                <input name="name" id="departmentName" class="form-control" placeholder="Department Name" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Designation Modal -->
                <div class="modal fade" id="designationModal" tabindex="-1" role="dialog" aria-labelledby="designationModalLabel">
                    <div class="modal-dialog">
                        <form id="designationForm" method="POST" class="modal-content">
                            @csrf
                            <input type="hidden" name="_method" id="designationMethod" value="POST" />
                            <input type="hidden" name="id" id="designationId" />
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" id="designationModalLabel">Add Designation</h4>
                            </div>
                            <div class="modal-body">
                                <input name="title" id="designationTitle" class="form-control" placeholder="Designation Title" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contract Type Modal -->
                <div class="modal fade" id="contractTypeModal" tabindex="-1" role="dialog" aria-labelledby="contractTypeModalLabel">
                    <div class="modal-dialog">
                        <form id="contractTypeForm" method="POST" class="modal-content">
                            @csrf
                            <input type="hidden" name="_method" id="contractTypeMethod" value="POST" />
                            <input type="hidden" name="id" id="contractTypeId" />
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" id="contractTypeModalLabel">Add Contract Type</h4>
                            </div>
                            <div class="modal-body">
                                <input name="name" id="contractTypeName" class="form-control" placeholder="Contract Type Name" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Salary Group Modal -->
                <div class="modal fade" id="salaryGroupModal" tabindex="-1" role="dialog" aria-labelledby="salaryGroupModalLabel">
                    <div class="modal-dialog">
                        <form id="salaryGroupForm" method="POST" class="modal-content">
                            @csrf
                            <input type="hidden" name="_method" id="salaryGroupMethod" value="POST" />
                            <input type="hidden" name="id" id="salaryGroupId" />
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" id="salaryGroupModalLabel">Add Salary Group</h4>
                            </div>
                            <div class="modal-body">
                                <input name="group_name" id="salaryGroupName" class="form-control" placeholder="Group Name" required style="margin-bottom: 10px;">
                                <input name="basic_salary" id="salaryBasicSalary" type="number" class="form-control" placeholder="Basic Salary" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel">
                    <div class="modal-dialog modal-sm">
                        <form id="deleteForm" method="POST" class="modal-content">
                            @csrf
                            @method('DELETE')
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h4>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this record?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div> <!-- panel-body -->
        </div> <!-- panel -->
    </div> <!-- col -->
</div> <!-- row -->
<!-- Bank Modal -->
<div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="bankModalLabel">
    <div class="modal-dialog">
        <form id="bankForm" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="bankMethod" value="POST" />
            <input type="hidden" name="id" id="bankId" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="bankModalLabel">Add Bank</h4>
            </div>
            <div class="modal-body">
                <input name="name" id="bankName" class="form-control" placeholder="Bank Name" required>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- jQuery + Bootstrap 3 JS required --}}
<script>
$(document).ready(function() {

    // Departments
    $('#addDepartmentBtn').click(function() {
        $('#departmentForm')[0].reset();
        $('#departmentMethod').val('POST');
        $('#departmentId').val('');
        $('#departmentModalLabel').text('Add Department');
        $('#departmentForm').attr('action', '{{ route("departments.store") }}');
        $('#departmentModal').modal('show');
    });

    $('.edit-department-btn').click(function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var name = row.data('name');

        $('#departmentForm')[0].reset();
        $('#departmentMethod').val('PUT');
        $('#departmentId').val(id);
        $('#departmentName').val(name);
        $('#departmentModalLabel').text('Edit Department');
        $('#departmentForm').attr('action', '/departments/' + id);
        $('#departmentModal').modal('show');
    });

    $('.delete-department-btn').click(function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        $('#deleteForm').attr('action', '/departments/' + id);
        $('#deleteConfirmModal').modal('show');
    });


    // Designations
    $('#addDesignationBtn').click(function() {
        $('#designationForm')[0].reset();
        $('#designationMethod').val('POST');
        $('#designationId').val('');
        $('#designationModalLabel').text('Add Designation');
        $('#designationForm').attr('action', '{{ route("designations.store") }}');
        $('#designationModal').modal('show');
    });

    $('.edit-designation-btn').click(function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var title = row.data('title');

        $('#designationForm')[0].reset();
        $('#designationMethod').val('PUT');
        $('#designationId').val(id);
        $('#designationTitle').val(title);
        $('#designationModalLabel').text('Edit Designation');
        $('#designationForm').attr('action', '/designations/' + id);
        $('#designationModal').modal('show');
    });

    $('.delete-designation-btn').click(function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        $('#deleteForm').attr('action', '/designations/' + id);
        $('#deleteConfirmModal').modal('show');
    });


    // Contract Types
    $('#addContractTypeBtn').click(function() {
        $('#contractTypeForm')[0].reset();
        $('#contractTypeMethod').val('POST');
        $('#contractTypeId').val('');
        $('#contractTypeModalLabel').text('Add Contract Type');
        $('#contractTypeForm').attr('action', '{{ route("contract-types.store") }}');
        $('#contractTypeModal').modal('show');
    });

    $('.edit-contract-type-btn').click(function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var name = row.data('name');

        $('#contractTypeForm')[0].reset();
        $('#contractTypeMethod').val('PUT');
        $('#contractTypeId').val(id);
        $('#contractTypeName').val(name);
        $('#contractTypeModalLabel').text('Edit Contract Type');
        $('#contractTypeForm').attr('action', '/contract-types/' + id);
        $('#contractTypeModal').modal('show');
    });

    $('.delete-contract-type-btn').click(function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        $('#deleteForm').attr('action', '/contract-types/' + id);
        $('#deleteConfirmModal').modal('show');
    });


    // Salary Groups
    $('#addSalaryGroupBtn').click(function() {
        $('#salaryGroupForm')[0].reset();
        $('#salaryGroupMethod').val('POST');
        $('#salaryGroupId').val('');
        $('#salaryGroupModalLabel').text('Add Salary Group');
        $('#salaryGroupForm').attr('action', '{{ route("salary-groups.store") }}');
        $('#salaryGroupModal').modal('show');
    });

    $('.edit-salary-group-btn').click(function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var groupName = row.data('group_name');
        var basicSalary = row.data('basic_salary');

        $('#salaryGroupForm')[0].reset();
        $('#salaryGroupMethod').val('PUT');
        $('#salaryGroupId').val(id);
        $('#salaryGroupName').val(groupName);
        $('#salaryBasicSalary').val(basicSalary);
        $('#salaryGroupModalLabel').text('Edit Salary Group');
        $('#salaryGroupForm').attr('action', '/salary-groups/' + id);
        $('#salaryGroupModal').modal('show');
    });

    $('.delete-salary-group-btn').click(function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        $('#deleteForm').attr('action', '/salary-groups/' + id);
        $('#deleteConfirmModal').modal('show');
    });

    // Banks
$('#addBankBtn').click(function() {
    $('#bankForm')[0].reset();
    $('#bankMethod').val('POST');
    $('#bankId').val('');
    $('#bankModalLabel').text('Add Bank');
    $('#bankForm').attr('action', '{{ route("banks.store") }}');
    $('#bankModal').modal('show');
});

$('.edit-bank-btn').click(function() {
    var row = $(this).closest('tr');
    var id = row.data('id');
    var name = row.data('name');

    $('#bankForm')[0].reset();
    $('#bankMethod').val('PUT');
    $('#bankId').val(id);
    $('#bankName').val(name);
    $('#bankModalLabel').text('Edit Bank');
    $('#bankForm').attr('action', '/banks/' + id);
    $('#bankModal').modal('show');
});

$('.delete-bank-btn').click(function() {
    var row = $(this).closest('tr');
    var id = row.data('id');
    $('#deleteForm').attr('action', '/banks/' + id);
    $('#deleteConfirmModal').modal('show');
});

});
</script>
@endsection
