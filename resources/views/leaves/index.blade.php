@extends('layouts.app_header')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">Home</a>
            <a class="step" href="#">Leave Management</a>
        </div>
    </div>
</div>

<div class="row">
<div class="col-lg-12">
    <div class="panel panel-custom panel-border">
        <div class="panel-heading">
            <h3 class="panel-title">Leave Management</h3>
        </div>

        <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="{{ $statusTab === 'all' ? 'active' : '' }}">
                    <a href="{{ url('leaves?tab=all') }}">All Leaves</a>
                </li>
                <li class="{{ $statusTab === 'approval' ? 'active' : '' }}">
                    <a href="{{ url('leaves?tab=approval') }}">Pending Approvals</a>
                </li>
                <li class="{{ $statusTab === 'apply' ? 'active' : '' }}">
                    <a href="{{ url('leaves?tab=apply') }}">Apply Leave</a>
                </li>
                <li class="{{ $statusTab === 'types' ? 'active' : '' }}">
                    <a href="{{ url('leaves?tab=types') }}">Leave Types</a>
                </li>
            </ul>
            <br>

            {{-- ALL & APPROVAL TAB --}}
            @if(in_array($statusTab, ['all', 'approval']))
                <form method="GET" action="{{ url('leaves') }}" class="row">
                    <input type="hidden" name="tab" value="{{ $statusTab }}">
                    <div class="col-md-3">
                        <select name="employee_id" class="form-control">
                            <option value="">-- All Employees --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="leave_type_id" class="form-control">
                            <option value="">-- All Leave Types --</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}"></div>
                    <div class="col-md-2"><input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}"></div>
                    <div class="col-md-2"><button class="btn btn-primary" type="submit">Filter</button></div>
                </form>
                <br>

                <div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>Dates</th>
                <th>Status</th>
                <th>Approved By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $leave)
                <tr>
                <td>{{ $leave->employee->firstname ?? '-' }} {{ $leave->employee->surname ?? '' }}</td>
                <td>{{ $leave->leaveType->name ?? '-' }}</td>
                    <td>{{ $leave->start_date ?? '-' }} to {{ $leave->end_date ?? '-' }}</td>
                    <td>
                        <span class="label label-{{ $leave->status == 'approved' ? 'success' : ($leave->status == 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($leave->status ?? 'N/A') }}
                        </span>
                    </td>
                    <td>{{ $leave->approver->name ?? '-' }}</td>
                    <td>
                        <a href="{{ url('leaves?tab=apply&edit_id=' . $leave->id) }}" class="btn btn-xs btn-info">Edit</a>
                        <form method="POST" action="{{ url('leaves/' . $leave->id) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger" onclick="return confirm('Delete this leave?')">Delete</button>
                        </form>
                        @if($leave->status === 'pending')
                            <form method="POST" action="{{ url('leaves/' . $leave->id . '/approve') }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-xs btn-success">Approve</button>
                            </form>
                            <form method="POST" action="{{ url('leaves/' . $leave->id . '/reject') }}" style="display:inline;">
                                @csrf
                                <button class="btn btn-xs btn-warning">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No leave records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $leaves->withQueryString()->links() }}
</div>


            {{-- APPLY TAB --}}
            @elseif($statusTab === 'apply')
    <div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ isset($editLeave) ? 'Edit Leave Request' : 'Apply for Leave' }}</h3>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ isset($editLeave) ? url('leaves/' . $editLeave->id) : url('leaves') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($editLeave))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="employee_id">Employee</label>
                <select name="employee_id" class="form-control" id="employee_id" required>
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('employee_id', $editLeave->employee_id ?? '') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->firstname }} {{ $emp->surname }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="leave_type_id">Leave Type</label>
                <select name="leave_type_id" class="form-control" id="leave_type_id" required>
                    <option value="">-- Select Leave Type --</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ old('leave_type_id', $editLeave->leave_type_id ?? '') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" class="form-control" id="start_date" value="{{ old('start_date', $editLeave->start_date ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" class="form-control" id="end_date" value="{{ old('end_date', $editLeave->end_date ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="reason">Reason</label>
                <textarea name="reason" class="form-control" id="reason" rows="3" required>{{ old('reason', $editLeave->reason ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label for="attachment">Attachment (optional)</label>
                <input type="file" name="attachment" class="form-control" id="attachment">
                @if(!empty($editLeave->attachment))
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $editLeave->attachment) }}" target="_blank">View Current File</a>
                    </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">
                {{ isset($editLeave) ? 'Update Leave' : 'Apply Leave' }}
            </button>
        </form>
    </div>
</div>


            {{-- TYPES TAB --}}
            @elseif($statusTab === 'types')
                <div class="row">
                    <div class="col-md-6">
                        <form method="POST" action="{{ isset($editType) ? url('leave-types/' . $editType->id) : url('leave-types') }}">
                            @csrf @if(isset($editType)) @method('PUT') @endif
                            <div class="form-group">
                                <label>Leave Type Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name', $editType->name ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>Description (optional)</label>
                                <textarea name="description" class="form-control">{{ old('description', $editType->description ?? '') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ isset($editType) ? 'Update Type' : 'Add Leave Type' }}</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaveTypes as $type)
                                    <tr>
                                        <td>{{ $type->name }}</td>
                                        <td>{{ $type->description }}</td>
                                        <td>
                                            <a href="{{ url('leaves?tab=types&edit_type=' . $type->id) }}" class="btn btn-xs btn-info">Edit</a>
                                            <form action="{{ url('leave-types/' . $type->id) }}" method="POST" style="display:inline-block;">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-xs btn-danger" onclick="return confirm('Delete this type?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">No leave types defined.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
