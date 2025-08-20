@extends('layouts.app_header')
@section('content')
<div class="container">
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">Home</a> 
            <a class="step" href="#">Payroll</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="pull-left">Payrolls</h4>
            </div>
            <br/><br/>

            <div class="panel-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                    <li role="presentation" class="active">
                        <a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">Overviews</a>
                    </li>
                    <li role="presentation">
                        <a href="#salary" aria-controls="salary" role="tab" data-toggle="tab">Salary</a>
                    </li>
                    <li role="presentation">
                        <a href="#salarySlip" aria-controls="salary-slip" role="tab" data-toggle="tab">Salary Slip</a>
                    </li>
                    <li role="presentation">
                        <a href="#salary-advances" aria-controls="salary-advances" role="tab" data-toggle="tab">Salary Advance</a>
                    </li>
                    <li role="presentation">
                        <a href="#loan-details" aria-controls="loan-details" role="tab" data-toggle="tab">Employee Loans</a>
                    </li>
                    <li role="presentation">
                        <a href="#salary-groups" aria-controls="salary-groups" role="tab" data-toggle="tab">Contributions Settings</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Overview Tab -->
                    <div role="tabpanel" class="tab-pane fade in active" id="overview">
                        <div class="row mb-4 m-t-2" style="background: #ddd; padding: 10px;">
                            <div class="col-md-2">
                                <label for="filterMonth">Month</label>
                                <select id="filterMonth" class="form-control">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $m == now()->month ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filterYear">Year</label>
                                <select id="filterYear" class="form-control">
                                    @for ($y = now()->year; $y <= now()->year + 5; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Employees</h5>
                                        <p class="card-text display-4">{{ $totalEmployees }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card text-center shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">This Month's Payroll</h5>
                                        <p class="card-text display-4">TZS {{ number_format($currentMonthPayroll, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Contributions & Taxes</h5>
                                        <p class="card-text display-4">{{ $salaryGroupsCount }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Attendance Rate</h5>
                                        <p class="card-text display-4">{{ $attendanceRate }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="salary">
                        <!-- Trigger Button -->
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#generatePayrollModal" style="float: right;">
                            Generate Payroll
                        </button>
                        <br/>
                        <!-- Payroll Modal -->
                        <div class="modal fade" id="generatePayrollModal" tabindex="-1" role="dialog" aria-labelledby="generatePayrollModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" style="width: 1000px;">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('payrolls.store') }}">
                                        @csrf

                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="generatePayrollModalLabel">Generate Payroll</h4>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="payroll-month">Payroll Month</label>
                                                <input type="month" name="month" id="payroll-month" class="form-control" required style="width: 200px;">
                                            </div>

                                            <h4>Select Employees and Contributions</h4>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="select-all"></th>
                                                        <th>Name</th>
                                                        <th>Basic Salary</th>
                                                        <th>Allowance</th>
                                                        <th>Contributions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($employees as $employee)
<tr>
    <td>
        <input type="checkbox" name="employees[{{ $employee->id }}][selected]" class="employee-checkbox" data-employee-id="{{ $employee->id }}">
    </td>
    <td>{{ $employee->firstname }} {{ $employee->middlename }} {{ $employee->surname }}</td>
    <td>
        {{ number_format($employee->basic_salary, 2) }}
        <input type="hidden" name="employees[{{ $employee->id }}][basic_salary]" value="{{ $employee->basic_salary }}">
    </td>
    <td>
        <input type="text" name="employees[{{ $employee->id }}][allowance]" value="{{ $employee->allowance }}" class="form-control allowance-field" style="width: 100px;" data-employee-id="{{ $employee->id }}" placeholder="e.g. 2000">
    </td>
    <td width="400px;">
        <div class="contributions-container" data-employee-id="{{ $employee->id }}">
            <!-- Contributions checkboxes will be dynamically inserted here -->
            <em>Select employee to load contributions</em>
        </div>
    </td>
</tr>
@endforeach

                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Generate Payroll</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <table class="table table-bordered table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Paid Employees</th>
                                    <th>Total Gross Salary</th>
                                    <th>NSSF</th>
                                    <th>NHIF</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($months as $m)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($m['month'].'-01')->format('M Y') }}</td>
                                        <td>{{ $m['paid_employees'] }}</td>
                                        <td>{{ number_format($m['total_amount'], 2) }}</td>
                                        <td>{{ number_format($m['nssf'], 2) }}</td>
                                        <td>{{ number_format($m['nhif'], 2) }}</td>
                                        <td>
                                            @if($m['exists'])
                                                <button class="btn btn-info btn-sm view-salary-btn"
                                                    data-reference="{{ $m['reference'] }}"
                                                    data-month="{{ $m['month'] }}">
                                                    <i class="fa fa-eye"></i> View
                                                </button>

                                                <button class="btn btn-danger btn-sm rollback-btn"
                                                    data-id="{{ $m['id'] }}">
                                                    <i class="fa fa-trash"></i> Rollback
                                                </button>

                                                <button class="btn btn-success btn-sm view-bank-btn"
                                                    data-reference="{{ $m['reference'] }}">
                                                    <i class="fa fa-bank"></i> Bank Voucher
                                                </button>

                                                <!-- NSSF Voucher -->
                                            <button class="btn btn-info btn-sm view-nssf-btn"
                                                    data-month="{{ $m['month'] }}">
                                                <i class="fa fa-file-excel-o"></i> NSSF Voucher
                                            </button>

                                            <!-- TUICO Voucher -->
                                            <button class="btn btn-primary btn-sm view-tuico-btn"
                                                    data-month="{{ $m['month'] }}">
                                                <i class="fa fa-file-excel-o"></i> TUICO Voucher
                                            </button>
                                                <!-- Voucher Buttons -->
                                                <!-- <a href="{{ url('payrolls/nssf-voucher/' . $m['month']) }}" class="btn btn-default btn-sm" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i> NSSF Voucher
                                                </a>
                                                <a href="{{ url('payrolls/paye-voucher/' . $m['month']) }}" class="btn btn-default btn-sm" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i> TUICO Voucher
                                                </a> -->
                                                <!-- <a href="{{ url('payrolls/wcf-voucher/' . $m['month']) }}" class="btn btn-default btn-sm" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i> WCF Voucher
                                                </a>
                                                <a href="{{ url('payrolls/nhif-voucher/' . $m['month']) }}" class="btn btn-default btn-sm" target="_blank">
                                                    <i class="fa fa-file-pdf-o"></i> NHIF Voucher
                                                </a> -->
                                                
                                            @else
                                                <span class="text-muted">Not Generated</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Salary Detail Modal -->
                        <div class="modal fade" id="salaryDetailModal" tabindex="-1" role="dialog" aria-labelledby="salaryDetailModalLabel">
                            <div class="modal-dialog modal-lg" role="document" style="width: 1200px;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="salaryDetailModalLabel">Salary Details</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body" id="salary-detail-content">
                                        <div class="text-center">
                                            <i class="fa fa-spinner fa-spin fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Groups Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="salary-groups">
                        <button class="btn btn-primary" data-toggle="modal" style="float: right;" data-target="#addContributionModal">Add Contribution or Tax</button>
                       
                        <h4>Contributions & Taxes</h4>
                        <div class="clearfix"></div>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Rate (%)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contributions as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ ucfirst($item->type) }}</td>
                                    <td>{{ $item->rate ?? '' }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#editContributionModal{{ $item->id }}">Edit</button>
                                        <form action="{{ route('contributions.destroy', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editContributionModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editContributionLabel{{ $item->id }}">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('contributions.update', $item->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <h4 class="modal-title" id="editContributionLabel{{ $item->id }}">Edit Contribution or Tax</h4>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="name{{ $item->id }}">Name</label>
                                                        <input type="text" name="name" id="name{{ $item->id }}" class="form-control" value="{{ $item->name }}" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="type{{ $item->id }}">Type</label>
                                                        <select name="type" id="type{{ $item->id }}" class="form-control" required>
                                                            <option value="fixed" {{ $item->type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                            <option value="percentage" {{ $item->type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                                            <option value="tax" {{ $item->type == 'tax' ? 'selected' : '' }}>Tax</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="rate{{ $item->id }}">Rate (%)</label>
                                                        <input type="number" name="rate" id="rate{{ $item->id }}" class="form-control" value="{{ $item->rate }}" step="0.01" required>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Update</button>
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

                    <!-- Salary slip Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="salarySlip">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Employee</label>
                                    <select id="employee_id" class="form-control select2" required>
                                        <option value="">Select Name</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->firstname }} {{ $employee->middlename }} {{ $employee->surname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Month</label>
                                    <input type="month" id="month" class="form-control" required>
                                </div>

                                <div class="col-md-3 m-t-30">
                                    <button id="generate-slip" class="btn btn-success">Generate Salary Slip</button>
                                </div>
                            </div>

                            <div id="loading-spinner" class="text-center" style="margin-top: 20px; display: none;">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                            </div>

                            <div id="salary-slip-result" style="margin-top: 20px;"></div>
                        </div>
                    </div>

                    <!-- Loan Details Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="loan-details">
                       <a href="{{ route('loans.pdf') }}" class="btn btn-pink" style="float:right;">
                            <i class="fa fa-file-pdf-o"></i>&nbsp; Loan PDF
                        </a>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addLoanModal" style="float:right;">
                           + Add Loan
                        </button>
                        <h4>Employee Loans</h4>
                        <div class="clearfix"></div>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Loan Type</th>
                                    <th>Loan Amount (TZS)</th>
                                    <th>Monthly Deduction (TZS)</th>
                                    <th>Outstanding Balance (TZS)</th>
                                    <th>Start Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $loan)
                                <tr>
                                    <td>{{ $loan->employee->firstname }} {{ $loan->employee->surname }}</td>
                                    <td>{{ $loan->type }}</td>
                                    <td>{{ number_format($loan->principal, 2) }}</td>
                                    <td>{{ number_format($loan->monthly_deduction, 2) }}</td>
                                    <td>{{ number_format($loan->balance, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($loan->start_date)->format('M Y') }}</td>
                                    <td>
                                        <!-- DETAILS BUTTON -->
                                        <button class="btn btn-xs btn-primary"
                                                onclick="loadLoanDetails({{ $loan->id }})"
                                                data-toggle="modal"
                                                data-target="#detailsLoanModal">
                                            Details
                                        </button>

                                        <button class="btn btn-xs btn-info" data-toggle="modal"
                                                data-target="#editLoanModal{{ $loan->id }}">Edit</button>

                                        <form method="POST" action="{{ route('loans.destroy', $loan->id) }}"
                                              style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-danger"
                                                    onclick="return confirm('Delete this loan?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Loan Modal -->
                                <div class="modal fade" id="editLoanModal{{ $loan->id }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('loans.update', $loan->id) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h4>Edit Loan</h4>
                                                </div>
                                                <div class="modal-body">
                                                    @include('payrolls.loan_form_fields', ['loan' => $loan])
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-success">Update</button>
                                                    <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Salary Advance Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="salary-advances">
                       
                        <a href="{{ url('salary-advances/pdf') }}" class="btn btn-pink" style="float:right;">
                           <i class="fa fa-file-pdf-o"></i>&nbsp; Salary Advance PDF
                        </a>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addAdvanceModal" style="float:right;">
                            + Add Salary Advance
                        </button> &nbsp;&nbsp;
                        <h4>Employee Salary Advances</h4>
                        <div class="clearfix"></div>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Amount (TZS)</th>
                                    <th>Month</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($salary_advances as $advance)
                            <tr>
                                <td>
                                    @if ($advance->employee)
                                        {{ $advance->employee->firstname }} {{ $advance->employee->surname }}
                                    @else
                                        <em>No employee</em>
                                    @endif
                                </td>
                                <td>{{ number_format($advance->amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($advance->month . '-01')->format('M Y') }}</td>
                                <td>
                                    <button class="btn btn-xs btn-info" data-toggle="modal"
                                            data-target="#editAdvanceModal{{ $advance->id }}">Edit</button>

                                    <form method="POST" action="{{ route('salary-advances.destroy', $advance->id) }}"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger"
                                                onclick="return confirm('Delete this advance?')">Delete</button>
                                    </form>
                                </td>
                                </tr>

                                <!-- Edit Salary Advance Modal -->
                                <div class="modal fade" id="editAdvanceModal{{ $advance->id }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('salary-advances.update', $advance->id) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h4>Edit Salary Advance</h4>
                                                </div>
                                                <div class="modal-body">
                                                    @include('payrolls.advance_form_fields', ['advance' => $advance])
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-success">Update</button>
                                                    <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Attendance Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="attendance">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#markAttendanceModal" style="float: right;">Mark Attendance</button>
                        <br/>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(App\Models\Attendance::with('employee')->orderBy('date', 'desc')->get() as $a)
                                <tr>
                                    <td>{{ $a->employee->firstname }}</td>
                                    <td>{{ $a->date }}</td>
                                    <td>{{ $a->status }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modals -->
                <!-- Create Salary Group Modal -->
                <!-- Add Contribution Modal -->
                <div class="modal fade" id="addContributionModal" tabindex="-1" role="dialog" aria-labelledby="addContributionLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('contributions.store') }}">
                                @csrf

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="addContributionLabel">Add Contribution or Tax</h4>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Eg. NSSF" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="type">Type</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="fixed">Fixed</option>
                                            <option value="percentage">Percentage</option>
                                            <option value="tax">Tax</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="rate">Rate or (%)</label>
                                        <input type="number" name="rate" id="rate" class="form-control" step="0.01" required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Add</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- Bank Details Modal -->
<div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="bankModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bank Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<!-- Add Loan Modal -->
<div class="modal fade" id="addLoanModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('loans.store') }}">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add New Loan</h4>
                </div>
                <div class="modal-body">
                    @include('payrolls.loan_form_fields', ['loan' => null])
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Loan</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Salary Advance Modal -->
<div class="modal fade" id="addAdvanceModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('salary-advances.store') }}">
                @csrf
                <div class="modal-header">
                    <h4>Add Salary Advance</h4>
                </div>
                <div class="modal-body">
                    @include('payrolls.advance_form_fields', ['advance' => null])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<!-- Loan Details Modal -->
<div class="modal fade" id="detailsLoanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" id="loanDetailsModalContent">
            <div class="modal-header">
                <h4 class="modal-title"><h2 class="title" style="text-align: center;">NDUVINI AUTOWORKS (NAW)</h2>
                </h4>
            </div>
            <div class="modal-body" id="loanDetailsBody">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                    <p>Loading loan Statement...</p>
                </div>
            </div>
            <div class="modal-footer no-print">
                <button class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-success" onclick="printLoanDetails('loanDetailsModalContent')">
                    Print
                </button>
            </div>
        </div>
    </div>
</div>
<!-- </div> -->
<script>

    $(document).ready(function() {
    // When select all checkbox is toggled
    $('#select-all').change(function() {
        let checked = $(this).is(':checked');
        $('.employee-checkbox').prop('checked', checked).trigger('change');
    });

    // When individual employee checkbox toggled
    $('.employee-checkbox').change(function() {
        let employeeId = $(this).data('employee-id');
        let contributionsContainer = $('.contributions-container[data-employee-id="' + employeeId + '"]');
        let allowanceField = $('.allowance-field[data-employee-id="' + employeeId + '"]');

        if ($(this).is(':checked')) {
            allowanceField.prop('disabled', false);

            // Fetch contributions via AJAX
            $.ajax({
                url: '/employee/' + employeeId + '/contributions', // Adjust URL to your route
                method: 'GET',
                success: function(response) {
                    // Response expected: array of contributions [{id, name}]
                    let html = '';
                    $.each(response, function(i, contribution) {
                        html += `
                            <label class="checkbox-inline" style="margin-right:10px;">
                                <input type="checkbox" 
                                       name="employees[${employeeId}][contributions][]" 
                                       value="${contribution.id}" 
                                       checked>
                                ${contribution.name}
                            </label>
                        `;
                    });
                    contributionsContainer.html(html);
                },
                error: function() {
                    contributionsContainer.html('<em>Failed to load contributions.</em>');
                }
            });
        } else {
            allowanceField.prop('disabled', true).val('');
            contributionsContainer.html('<em>Select employee to load contributions</em>');
        }
    });
});

</script>

<script>
    document.getElementById('generate-slip').addEventListener('click', function () {
        const employeeId = document.getElementById('employee_id').value;
        const month = document.getElementById('month').value;
        const spinner = document.getElementById('loading-spinner');
        const container = document.getElementById('salary-slip-result');

        container.innerHTML = '';

        if (!employeeId || !month) {
            alert("Please select both employee and month.");
            return;
        }

        spinner.style.display = 'block';

        fetch(`/payrolls/salary-slip-fetch/view?employee_id=${employeeId}&month=${month}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            spinner.style.display = 'none';

            if (data.success && Array.isArray(data.slips) && data.slips.length > 0) {
                const slip = data.slips[0];

                const toFloat = val => parseFloat((val || '0').toString().replace(/,/g, ''));

                const grossSalary = toFloat(slip.basic_salary) + toFloat(slip.allowance);
                const totalDeductions = 
                    toFloat(slip.salary_advance) +
                    toFloat(slip.nssf) +
                    toFloat(slip.wcf) +
                    toFloat(slip.nhif) +
                    toFloat(slip.tuico) +
                    toFloat(slip.loan) +
                    toFloat(slip.paye);

                container.innerHTML = `
                    <br/><h4>Salary Slip - ${data.month}</h4><br/>
                    <table class="table table-bordered">
                        <tr><th>Employee Name</th><td>${slip.employee_name}</td></tr>
                        <tr><th>Basic Salary</th><td>${slip.basic_salary}</td></tr>
                        <tr><th>Allowance</th><td>${slip.allowance}</td></tr>
                        <tr><th>Gross Salary</th><td>${grossSalary.toLocaleString()}</td></tr>
                        <tr><th>Salary Advance</th><td>${slip.salary_advance}</td></tr>
                        <tr><th>Loan</th><td>${slip.loan}</td></tr>
                        <tr><th>NSSF</th><td>${slip.nssf}</td></tr>
                        <tr><th>NHIF</th><td>${slip.nhif}</td></tr>
                        <tr><th>TUICO</th><td>${slip.tuico}</td></tr>
                        <tr><th>PAYE</th><td>${slip.paye}</td></tr>
                        <tr><th>Total Deduction</th><td>${totalDeductions.toLocaleString()}</td></tr>
                        <tr><th>Net Salary</th><td><strong>${slip.net_salary}</strong></td></tr>
                    </table>
                    <div class="text-right mt-2">
                        <a class="btn btn-pink" href="/payrolls/salary-slip-download/view/pdf?employee_id=${employeeId}&month=${month}" target="_blank">
                            <i class="fa fa-download"></i> Download PDF
                        </a>
                    </div>
                `;
            } else {
                container.innerHTML = `<div class="alert alert-warning">No salary slip data found for this month.</div>`;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            spinner.style.display = 'none';
            container.innerHTML = `
                <div class="alert alert-danger">
                    <strong>Error:</strong> ${error.message}
                </div>
            `;
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.view-salary-btn');

        buttons.forEach(btn => {
            btn.addEventListener('click', function () {
                const reference = this.dataset.reference;
                const month = this.dataset.month;
                const modal = $('#salaryDetailModal');
                const content = document.getElementById('salary-detail-content');

                content.innerHTML = `<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>`;
                modal.modal('show');

                fetch(`/payrolls/details/${reference}?month=${month}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let rows = '';
                            data.slips.forEach((slip, index) => {
                                rows += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${slip.employee_name}</td>
                                        <td>${slip.basic_salary}</td>
                                        <td>${slip.allowance}</td>
                                        <td>${slip.salary_advance}</td>
                                        <td>${slip.loan ?? 0}</td>
                                        <td>${slip.nssf ?? 0}</td>
                                        <td>${slip.nhif ?? 0}</td>
                                        <td>${slip.tuico ?? 0}</td>
                                        <td>${slip.paye ?? 0}</td>
                                        <td>${slip.net_salary}</td>
                                    </tr>
                                `;
                            });

                            content.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Salary for ${data.month}</h5>
                                    <a href="/payrolls/download/view/details/${reference}" class="btn btn-sm btn-pink" style="float: right;" target="_blank">
                                        <i class="fa fa-download"></i> Download PDF
                                    </a>
                                </div>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Employee</th>
                                            <th>Basic Salary</th>
                                            <th>Allowance</th>
                                            <th>Advance Pay</th>
                                            <th>LOAN</th>
                                            <th>NSSF</th>
                                            <th>NHIF</th>
                                            <th>TUICO</th>
                                            <th>PAYE</th>
                                            <th>Net Salary</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rows}</tbody>
                                </table>
                            `;
                        } else {
                            content.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                        }
                    });
            });
        });
    });
</script>

<!-- Add JS handlers for view-salary-btn, rollback-btn, and view-bank-btn -->
<script>
    
    $(document).on('click', '.rollback-btn', function() {
    let id = $(this).data('id');
    console.log(id);
    
    if (confirm('Are you sure you want to rollback/delete this payroll?')) {
        $.ajax({
            url: '/payrolls/delete/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                // Try to show the exact error message from the backend
                let errorMessage = 'Failed to rollback payroll.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error(xhr.responseJSON);
            }
        });
    }
});


    $(document).on('click', '.view-bank-btn', function () {
        let reference = $(this).data('reference');
        $('#bankModal').modal('show');
        $('#bankModal .modal-title').text('Bank Voucher');
        $('#bankModal .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');

        $.get(`/payrolls/${reference}/bank-details`, function (data) {
            $('#bankModal .modal-body').html(data);
        }).fail(function () {
            $('#bankModal .modal-body').html('<div class="alert alert-danger">Failed to load bank details.</div>');
        });
    });
// NSSF Voucher Modal
$(document).on('click', '.view-nssf-btn', function () {
    let month = $(this).data('month');
    $('#bankModal').modal('show');
    $('#bankModal .modal-title').text('NSSF PAYEMENT');
    $('#bankModal .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');

    $.get(`/payrolls/${month}/nssf-voucher-view`, function (data) {
        $('#bankModal .modal-title').text(`NSSF PAYEMENT  - ${month}`);
        $('#bankModal .modal-body').html(data);
    }).fail(function (xhr) {
        let message = 'Failed to load NSSF voucher.';
        if (xhr.responseJSON && xhr.responseJSON.error) {
            message = xhr.responseJSON.error;
        }

        $('#bankModal .modal-body').html(`<div class="alert alert-danger">${message}</div>`);
    });
});


// TUICO Voucher Modal
$(document).on('click', '.view-tuico-btn', function () {
    let month = $(this).data('month');
    $('#bankModal').modal('show');
    $('#bankModal .modal-title').text('TUICO Payment Voucher');
    $('#bankModal .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');

    $.get(`/payrolls/${month}/tuico-voucher-view`, function (data) {
        $('#bankModal .modal-body').html(data);
    }).fail(function () {
        $('#bankModal .modal-body').html('<div class="alert alert-danger">Failed to load TUICO Payment.</div>');
    });
});

</script>

<script>
    function loadLoanDetails(loanId) {
        // Show spinner while loading
        document.getElementById('loanDetailsBody').innerHTML = `
            <div class="text-center">
                <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                <p>Loading loan details...</p>
            </div>
        `;

        // AJAX request to get loan details
        fetch('/loans/' + loanId + '/statement')
            .then(response => response.text())
            .then(html => {
                document.getElementById('loanDetailsBody').innerHTML = html;
            })
            .catch(error => {
                console.error(error);
                document.getElementById('loanDetailsBody').innerHTML = `
                    <div class="text-danger text-center">Failed to load loan details.</div>
                `;
            });
    }

    // Print function
    function printLoanDetails(modalContentId) {
        var printContents = document.getElementById(modalContentId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Optional: Refresh page
    }
</script>

@endsection