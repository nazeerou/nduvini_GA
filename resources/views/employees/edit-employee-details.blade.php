@extends('layouts.app_header')

@section('content')

<style>
    /* ===== Section divider ===== */
    .form-section {
        background: #f8f9fa;
        border-left: 4px solid #4e54c8;
        padding: 10px 16px;
        border-radius: 6px;
        margin: 22px 0 14px;
    }
    .form-section h5 {
        margin: 0;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #4e54c8;
    }
    .form-section h5 i { margin-right: 6px; }

    /* ===== Card ===== */
    .edit-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 20px;
    }
    .edit-card .card-head {
        padding: 16px 22px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .edit-card .card-head h4 {
        margin: 0;
        font-size: 17px;
        font-weight: 600;
        color: #212529;
    }
    .edit-card .card-body { padding: 22px; }

    /* ===== Files already attached ===== */
    .current-file {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #4e54c8;
        margin-top: 4px;
    }
    .current-file i { font-size: 12px; }

    /* ===== Submit row ===== */
    .form-actions {
        border-top: 1px solid #e9ecef;
        padding-top: 16px;
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .help-inline {
        font-size: 11.5px;
        color: #6c757d;
        margin-top: 4px;
        display: block;
    }
</style>

{{-- Back link --}}
<div class="row" style="margin-bottom: 12px;">
    <div class="col-md-12 text-right">
        <a href="{{ url('staffs/managements') }}" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left"></i> Back to list
        </a>
    </div>
</div>

<div class="edit-card">
    <div class="card-head">
        <h4><i class="fa fa-pencil text-primary"></i> Edit Employee Details</h4>
        <small class="text-muted">
            {{ trim($employee->firstname . ' ' . $employee->surname) }}
            @if($employee->email) &lt;{{ $employee->email }}&gt; @endif
        </small>
    </div>

    <div class="card-body">
        <form action="{{ route('employees.update', $employee->id) }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ============================
                 PERSONAL INFORMATION
            ============================= --}}
            <div class="form-section">
                <h5><i class="fa fa-user"></i> Personal Information</h5>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>First Name <span class="text-danger">*</span></label>
                    <input type="text" name="firstname" class="form-control" required
                           value="{{ old('firstname', $employee->firstname) }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Middle Name</label>
                    <input type="text" name="middlename" class="form-control"
                           value="{{ old('middlename', $employee->middlename) }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Surname <span class="text-danger">*</span></label>
                    <input type="text" name="surname" class="form-control" required
                           value="{{ old('surname', $employee->surname) }}">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Mobile</label>
                    <input type="text" name="mobile" class="form-control"
                           value="{{ old('mobile', $employee->mobile) }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email', $employee->email) }}">
                </div>
                <div class="form-group col-md-4">
                    <label>NIDA Number</label>
                    <input type="text" name="nida_number" class="form-control"
                           value="{{ old('nida_number', $employee->nida_number) }}">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Designation</label>
                    <select name="designation_id" class="form-control">
                        <option value="">— Select —</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}"
                                {{ old('designation_id', $employee->designation_id) == $designation->id ? 'selected' : '' }}>
                                {{ $designation->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Department</label>
                    <select name="department_id" class="form-control">
                        <option value="">— Select —</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Branch</label>
                    <select name="branch_id" class="form-control">
                        <option value="">— Select —</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ============================
                 CONTRACT & SALARY
            ============================= --}}
            <div class="form-section">
                <h5><i class="fa fa-file-text"></i> Contract &amp; Salary</h5>
            </div>

            @php
                $currentContractTypeId = old(
                    'contract_type_id',
                    $employee->contract_type_id ?? optional($employee->contract)->contract_type_id
                );
                $currentDuration = old(
                    'contract_duration',
                    optional($employee->contract)->contract_duration
                );
                $currentStartDate = old(
                    'start_date',
                    optional($employee->contract)->start_date ?? $employee->joining_date
                );
                $currentEndDate = old(
                    'end_date',
                    optional($employee->contract)->end_date
                );
            @endphp

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Salary Group</label>
                    <select name="salary_group_id" class="form-control select2">
                        <option value="">— Select Salary Group —</option>
                        @foreach ($salaryGroups as $group)
                            <option value="{{ $group->id }}"
                                {{ old('salary_group_id', $employee->salary_group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->group_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>Contract Type</label>
                    <select name="contract_type_id" class="form-control">
                        <option value="">— Select Contract Type —</option>
                        @foreach ($contractTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ $currentContractTypeId == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>Contract Duration</label>
                    <input type="text" name="contract_duration" class="form-control"
                           placeholder="e.g. 12 months"
                           value="{{ $currentDuration }}">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-8">
                    <label>Contract Period</label>
                    <div class="input-daterange input-group" id="date-range" data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control" name="start_date"
                               placeholder="Start date" autocomplete="off"
                               value="{{ $currentStartDate }}">
                        <span class="input-group-addon" style="background: #4e54c8; color: #fff;">to</span>
                        <input type="text" class="form-control" name="end_date"
                               placeholder="End date" autocomplete="off"
                               value="{{ $currentEndDate }}">
                    </div>
                </div>
            </div>

            {{-- ============================
                 DOCUMENTS
            ============================= --}}
            <div class="form-section">
                <h5><i class="fa fa-paperclip"></i> Attach Documents</h5>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>CV / Certificates</label>
                    <input type="file" name="cv_file" class="form-control">
                    <span class="help-inline">Leave empty to keep the current file.</span>
                    @if($employee->cv_file)
                        <a href="{{ Storage::url($employee->cv_file) }}" target="_blank" class="current-file">
                            <i class="fa fa-file-pdf-o"></i> View current CV
                        </a>
                    @endif
                </div>

                <div class="form-group col-md-4">
                    <label>NIDA</label>
                    <input type="file" name="nida_file" class="form-control">
                    <span class="help-inline">Leave empty to keep the current file.</span>
                    @if($employee->nida_file)
                        <a href="{{ Storage::url($employee->nida_file) }}" target="_blank" class="current-file">
                            <i class="fa fa-file-image-o"></i> View current NIDA
                        </a>
                    @endif
                </div>

                <div class="form-group col-md-4">
                    <label>Contract File</label>
                    <input type="file" name="contract_file" class="form-control">
                    <span class="help-inline">Leave empty to keep the current file.</span>
                    @if($employee->contract_file)
                        <a href="{{ Storage::url($employee->contract_file) }}" target="_blank" class="current-file">
                            <i class="fa fa-file-pdf-o"></i> View current contract
                        </a>
                    @endif
                </div>
            </div>

            {{-- ============================
                 ACTIONS
            ============================= --}}
            <div class="form-actions">
                <a href="{{ url()->previous() }}" class="btn btn-default">
                    <i class="fa fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update Employee
                </button>
            </div>

        </form>
    </div>
</div>

<script>
$(function () {
    if ($.fn.datepicker) {
        $('.input-daterange').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }

    if ($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }
});
</script>

@endsection