@extends('layouts.app_header')

@section('content')

<style>
.dv-personal {
   background: #ddd;
   color: #fff;
   padding: 4px 10px 1px 20px;
   margin: 4px 0;
}
</style>

<div class="row mt-10">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a href="{{ url('staffs/managements') }}"> <i class="fa fa-arrow"></i> Go Back </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1">Edit Employee Details</h4>
            </div>

            <div class="panel-body">
                <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Personal Info --}}
                    <div class="dv-personal">
                        <h4><i class="fa fa-user"></i> Personal Information</h4>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>First Name *</label>
                            <input type="text" name="firstname" class="form-control" required value="{{ $employee->firstname }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Middle Name</label>
                            <input type="text" name="middlename" class="form-control" value="{{ $employee->middlename }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Surname *</label>
                            <input type="text" name="surname" class="form-control" required value="{{ $employee->surname }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $employee->mobile }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $employee->email }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label>NIDA Number</label>
                            <input type="text" name="nida_number" class="form-control" value="{{ $employee->nida_number }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Designation</label>
                            <select name="designation_id" class="form-control">
                                <option value="">-- Select Designation --</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation->id }}" {{ $employee->designation_id == $designation->id ? 'selected' : '' }}>
                                        {{ $designation->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Department</label>
                            <select name="department_id" class="form-control">
                                <option value="">-- Select Department --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ $employee->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control">
                                <option value="">-- Select Branch --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $employee->branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Contract Info --}}
                    <div class="dv-personal">
                        <h4><i class="fa fa-file-text"></i> Contract & Salary</h4>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Salary Group</label>
                            <select name="salary_group_id" class="form-control select2">
                                <option value="">-- Select Salary Group --</option>
                                @foreach ($salaryGroups as $group)
                                    <option value="{{ $group->id }}" {{ $employee->salary_group_id == $group->id ? 'selected' : '' }}>
                                        {{ $group->group_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contract Type</label>
                            <select name="contract_type_id" class="form-control">
                                <option value="">-- Select Contract Type --</option>
                                @foreach ($contract_types as $type)
                                    <option value="{{ $type->id }}" {{ optional($employee->contract)->contract_type_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Contract Duration</label>
                            <input type="text" name="contract_duration" class="form-control" value="{{ optional($employee->contract)->duration }}">
                        </div>
                        <div class="form-group col-md-8">
                            <label>Contract Date</label>
                            <div class="input-daterange input-group" id="date-range" data-date-format="yyyy-mm-dd">
                                <input type="text" class="form-control" name="start_date" placeholder="Start Date" value="{{ optional($employee->contract)->start_date }}">
                                <span class="input-group-addon b-0 text-white" style="background: palevioletred">To</span>
                                <input type="text" class="form-control" name="end_date" placeholder="End Date" value="{{ optional($employee->contract)->end_date }}">
                            </div>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div class="dv-personal">
                        <h4><i class="fa fa-paperclip"></i> Attach Documents</h4>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Upload CV / Certificates</label>
                            <input type="file" name="cv_file" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Upload NIDA</label>
                            <input type="file" name="nida_file" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Upload Contract File</label>
                            <input type="file" name="contract_file" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mt-10">
                        <button type="submit" class="btn btn-success" style="float: right;">Update Employee</button>
                        <a href="{{ url()->previous() }}" class="btn btn-default" style="float: right;">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
