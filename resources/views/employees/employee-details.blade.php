@extends('layouts.app_header')
<style>
    .profile-section {
        margin-top: 20px;
        padding: 15px;
        background-color: #fdfdfd;
        border: 1px solid #ddd;
        border-radius: 6px;
    }

    .profile-avatar {
        width: 140px;
        height: 140px;
        border: 4px solid rgb(244, 245, 249);
        margin: 0 auto 15px auto;
    }

    .profile-table {
        width: 100%;
        background-color: #fff;
    }

    .profile-table th {
        width: 35%;
        background-color: #f2f4f6;
        color: #333;
        text-align: left;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .profile-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
        background-color: #fafafa;
    }

    .profile-table tr:nth-child(odd) td {
        background-color: #f9f9f9;
    }

    .profile-table tr:nth-child(even) td {
        background-color: #fff;
    }

    .contract-info-table {
        width: 100%;
        max-width: 800px;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 15px;
    }

    .contract-info-table td {
        padding: 10px 12px;
        border: 1px solid #ddd;
        vertical-align: top;
    }

    .contract-info-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .contract-info-table td:first-child {
        font-weight: bold;
        background-color: #f0f0f0;
        width: 40%;
    }

    .label-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 5px;
        font-size: 13px;
        font-weight: 600;
    }

    .label-success {
        background-color: #28a745;
        color: white;
    }

    .label-danger {
        background-color: #dc3545;
        color: white;
    }
    .document-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 15px;
    }

    .document-table th, .document-table td {
        border: 1px solid #ddd;
        padding: 12px 15px;
        text-align: left;
    }

    .document-table th {
        background-color: #f5f5f5;
        font-weight: bold;
    }

    .document-table tr:nth-child(even) {
        background-color: #fafafa;
    }

    .btn-view {
        display: inline-block;
        margin-right: 8px;
        padding: 6px 12px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
    }

    .btn-view:hover {
        background-color: #0056b3;
    }
</style>

@section('content')
<div class="row">
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
            <h3> 
            Deatails : {{ $employee->name }}</h3>
            </div>
            <hr/>
<div class="panel-body">
<!-- Nav tabs -->
<div class="container">
     <!-- Nav Tabs -->
     <ul class="nav nav-tabs">
        <li class="active"><a href="#profile" data-toggle="tab">Profile</a></li>
        <li><a href="#documents" data-toggle="tab">Documents</a></li>
        <li><a href="#contract" data-toggle="tab">Contract & Salary</a></li>
        <li><a href="#bank_details" data-toggle="tab">Bank Account Details </a></li>
        <li><a href="#nssf_details" data-toggle="tab">NSSF Details </a></li>
        <li><a href="#termination" data-toggle="tab">Termination</a></li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" style="padding: 20px; border: 1px solid #ddd; border-top: none;">
        <!-- Profile -->
        <div class="tab-pane fade in active" id="profile">
            <div class="row profile-section">
                <!-- Left: Profile Image -->
                <div class="col-md-2 text-center">
                    <img src="{{ url('/assets/images/users/profile.jpg') }}" 
                        class="profile-avatar img-circle" 
                        alt="Profile Image">
                </div>

                <!-- Right: Employee Details -->
                <div class="col-md-7">
                    <table class="table profile-table">
                        <tr>
                            <th> Employee ID </th>
                            <td><strong>NAW{{ str_pad($employee->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                        <tr>
                            <th>Full Name</th>
                            <td>{{ $employee->firstname }} {{ $employee->middlename }} {{ $employee->surname }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $employee->email }}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>{{ $employee->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Joined Date</th>
                            <td>{{ \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') }}</td>
                            </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ optional($employee->department)->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Designation/Position</th>
                            <td>{{ optional($employee->designation)->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    <!-- Documents -->
    <div class="tab-pane fade" id="documents">
    <h5>Employee Documents</h5>

    <table class="document-table">
        <thead>
            <tr>
                <th>Document</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>CV & Certificate</td>
                <td>
                    <a href="#" class="btn-view" onclick="previewDocument('{{ asset('storage/' . $employee->cv_file) }}')">Preview</a>
                    <a href="{{ asset('storage/' . $employee->cv_file) }}" target="_blank" class="btn btn-sm btn-pink">Download</a>
                </td>
            </tr>
            <tr>
                <td>NIDA</td>
                <td>
                    <a href="#" class="btn-view" onclick="previewDocument('{{ asset('storage/' . $employee->nida_file) }}')">Preview</a>
                    <a href="{{ asset('storage/' . $employee->nida_file) }}" target="_blank" class="btn btn-sm btn-pink">Download</a>
                </td>
            </tr>
            <tr>
                <td>Contract</td>
                <td>
                    <a href="#" class="btn-view" onclick="previewDocument('{{ asset('storage/' . $employee->contract_file) }}')">Preview</a>
                    <a href="{{ asset('storage/' . $employee->contract_file) }}" target="_blank" class="btn btn-sm btn-pink">Download</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

        <!-- Contract -->
        <div class="tab-pane fade" id="contract">
    <h5>Employee Contract Information</h5>

    <table class="contract-info-table">
        <tr>
            <td>Salary Group</td>
            <td>{{ optional($employee->salaryGroup)->group_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Basic Salary</td>
            <td>{{ optional($employee->salaryGroup)->basic_salary ? number_format($employee->salaryGroup->basic_salary, 2) . ' TZS' : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Contract Type</td>
            <td>{{ $employee->contractType->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Contract Duration</td>
            <td>{{ $employee->contract->contract_duration ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Start Date</td>
            <td>{{ $employee->contract->start_date ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>End Date</td>
            <td>{{ $employee->contract->end_date ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>
                @if($employee->status === 'terminated')
                    <span class="label-status label-danger">Terminated</span>
                @else
                    <span class="label-status label-success">Active</span>
                @endif
            </td>
        </tr>
    </table>
</div>

     <!-- Bank -->
     <div class="tab-pane fade" id="bank_details">
    <h4>Bank Account Details</h4>

    <!-- Add Button -->
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addBankModal">Add Bank Account</button>

    <!-- Bank Account Table -->
    <table class="table table-bordered table-striped" style="margin-top: 10px;">
        <thead>
            <tr>
                <th>Bank Name</th>
                <th>Account Number</th>
                <th>Account Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employee->bankAccount as $bank)
            <tr>
                <td>{{ $bank->bank->name }}</td>
                <td>{{ $bank->account_number }}</td>
                <td>{{ $bank->account_name }}</td>
                <td>
                    <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#editBankModal{{ $bank->id }}">Edit</button>
                    <form action="{{ route('employee.bank.destroy', $bank->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-danger" onclick="return confirm('Delete this account?')">Delete</button>
                    </form>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editBankModal{{ $bank->id }}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('employee.bank.update', $bank->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header"><h5>Edit Bank Account</h5></div>
                            <div class="modal-body">
                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                <div class="form-group">
                                <label>Bank Name</label>
                                <select name="bank_id" class="form-control" required>
                                    <option value="">-- Select Bank --</option>
                                    @foreach($banks as $bankOption)
                                        <option value="{{ $bankOption->id }}" {{ $bankOption->name == $bank->bank_name ? 'selected' : '' }}>
                                            {{ $bankOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="text" name="account_number" class="form-control" value="{{ $bank->account_number }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Account Name</label>
                                    <input type="text" name="account_name" class="form-control" value="{{ $bank->account_name }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addBankModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('employee.bank.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <div class="modal-header"><h5>Add Bank Account</h5></div>
                <div class="modal-body">
                <div class="form-group">
                <label>Bank Name</label>
                <select name="bank_id" class="form-control" required>
                    <option value="">-- Select Bank --</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="text" name="account_number" class="form-control" placeholder="Account number" required>
                    </div>
                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" name="account_name" class="form-control" placeholder="Account Name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- NSSF -->

<div class="tab-pane fade" id="nssf_details">
    <h4>NSSF Details</h4>

    <!-- Add Button -->
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNssfModal">Add NSSF Member No</button>

    <!-- NSSF Table -->
    <table class="table table-bordered table-striped" style="margin-top: 10px;">
        <thead>
            <tr>
                <th>NSSF Member Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employee->nssfDetails ?? [] as $nssf)
            <tr>
                <td>{{ $nssf->member_number }}</td>
                <td>
                    <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#editNssfModal{{ $nssf->id }}">Edit</button>
                    <form action="{{ route('employee.nssf.destroy', $nssf->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-danger" onclick="return confirm('Delete this NSSF entry?')">Delete</button>
                    </form>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editNssfModal{{ $nssf->id }}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('employee.nssf.update', $nssf->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header"><h5>Edit NSSF Member No</h5></div>
                            <div class="modal-body">
                                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                <div class="form-group">
                                    <label>NSSF Member Number</label>
                                    <input type="text" name="member_number" class="form-control" value="{{ $nssf->member_number }}" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addNssfModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('employee.nssf.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <div class="modal-header"><h5>Add NSSF Member No</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>NSSF Member Number</label>
                        <input type="text" name="member_number" class="form-control" placeholder="Enter NSSF Member No" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

        <!-- Termination -->
        <div class="tab-pane fade" id="termination">
            @if ($employee->status === 'terminated')
                <p><strong>Termination Date:</strong> {{ $employee->termination_date ?? 'N/A' }}</p>
                <p><strong>Reason:</strong> {{ $employee->termination_reason ?? 'N/A' }}</p>
            @else
                <p>This employee has not been terminated.</p>
            @endif
        </div>
    </div>
</div>
</div>
</div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">Document Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <iframe id="documentFrame" src=""></iframe>
      </div>
    </div>
  </div>
</div>


<script>
    function previewDocument(url) {
        document.getElementById('documentFrame').src = url;
        let modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }
</script>

<!-- Modal -->
<div id="documentPreviewModal" class="modal" tabindex="-1" role="dialog" style="display:none; position:fixed; top:10%; left:0; width:100%; height:90%; background:rgba(0,0,0,0.7); z-index:1050;">
    <div style="background:white; margin:0 auto; width:80%; height:90%; padding:20px; position:relative;">
        <button onclick="closePreview()" style="position:absolute; top:10px; right:20px;">&times;</button>
        <iframe id="documentPreviewFrame" src="" width="100%" height="100%" frameborder="0"></iframe>
    </div>
</div>

<script>
    function previewDocument(fileUrl) {
        event.preventDefault();
        document.getElementById('documentPreviewFrame').src = fileUrl;
        document.getElementById('documentPreviewModal').style.display = 'block';
    }

    function closePreview() {
        document.getElementById('documentPreviewModal').style.display = 'none';
        document.getElementById('documentPreviewFrame').src = '';
    }
</script>

@endsection
