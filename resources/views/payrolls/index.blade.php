@extends('layouts.app_header')

@section('content')
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
                        <a href="#salary-groups" aria-controls="salary-groups" role="tab" data-toggle="tab">Contributions & Tax Settings</a>
                    </li>
                    <li role="presentation">
                        <a href="#attendance" aria-controls="attendance" role="tab" data-toggle="tab">Employee Attendances</a>
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
                                        <h5 class="card-title">Contributions & Taxes </h5>
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
                <th>Contributions & Taxes </th>
              </tr>
            </thead>
            <tbody>
              @foreach($employees as $employee)
              <tr>
                <td>
                  <input type="checkbox" name="employees[{{ $employee->id }}][selected]" class="employee-checkbox" data-employee-id="{{ $employee->id }}">
                </td>
                <td>{{ $employee->name }}</td>
                <td>
                  {{ number_format($employee->basic_salary, 2) }}
                  <input type="hidden" name="employees[{{ $employee->id }}][basic_salary]" value="{{ $employee->basic_salary }}">
                </td>
                <td>
                  <input type="text" name="employees[{{ $employee->id }}][allowance]" class="form-control allowance-field" data-employee-id="{{ $employee->id }}" placeholder="e.g. 2000" disabled>
                </td>
                <td width="300px;">
                @foreach($contributions as $contribution)
                    <label class="checkbox-inline">
                    <input type="checkbox" 
                            name="employees[{{ $employee->id }}][contributions][]" 
                            value="{{ $contribution->id }}" 
                            disabled 
                            class="contrib-checkbox" 
                            data-employee-id="{{ $employee->id }}">
                    {{ $contribution->name }}
                    </label>
                @endforeach
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
            <th>Total Amount</th>
            <th>NSSF</th>
            <th>PAYE</th>
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
                <td>{{ number_format($m['paye'], 2) }}</td>
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
            <i class="fa fa-bank"></i> Bank Details
        </button>

        <!-- Voucher Buttons -->
        <a href="{{ url('payrolls/nssf-voucher/' . $m['month']) }}" class="btn btn-default btn-sm" target="_blank">
            <i class="fa fa-file-pdf-o"></i> NSSF Voucher
        </a>
        <a href="{{ url('payrolls/nhif-voucher/' . $m['month']) }}" class="btn btn-default btn-sm" target="_blank">
            <i class="fa fa-file-pdf-o"></i> NHIF Voucher
        </a>
        <a href="{{ url('payrolls/wcf-voucher/' . $m['month']) }}" class="btn btn-default btn-sm" target="_blank">
            <i class="fa fa-file-pdf-o"></i> WCF Voucher
        </a>
        <a href="{{ url('payrolls/paye-voucher/' . $m['month']) }}" class="btn btn-default btn-sm" target="_blank">
            <i class="fa fa-file-pdf-o"></i> PAYE Voucher
        </a>
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
  <div class="modal-dialog modal-lg" role="document">
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
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
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


<hr>

<!-- Salary slip will be displayed here -->
<div id="salary-slip-result"></div>

                    </div>
</div>
                    <!-- Attendance Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="attendance">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#markAttendanceModal" style="float: right;">Mark Attendance</button>
                        <br/>
                        <table class="table table-bordered">
                            <thead><tr><th>Employee</th><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach(App\Models\Attendance::with('employee')->orderBy('date', 'desc')->get() as $a)
                                <tr>
                                    <td>{{ $a->employee->name }}</td>
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



<script>
  $(document).ready(function () {
    // Handle "Select All"
    $('#select-all').change(function () {
      var isChecked = $(this).is(':checked');
      
      $('.employee-checkbox').prop('checked', isChecked).trigger('change');
    });

    // Handle single checkbox change
    $('.employee-checkbox').change(function () {
      var employeeId = $(this).data('employee-id');
      var isChecked = $(this).is(':checked');

      // Enable/disable allowance input
      $('.allowance-field[data-employee-id="' + employeeId + '"]').prop('disabled', !isChecked);

      // Enable/disable contributions checkboxes
      $('.contrib-checkbox[data-employee-id="' + employeeId + '"]').prop('disabled', !isChecked);
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

        if (data.success) {
            const slip = data.slip;

            container.innerHTML = `
                <br/><h4>Salary Slip - ${data.month}</h4><br/>
                <table class="table table-bordered">
                    <tr><th>Employee Name</th><td>${slip.employee_name}</td></tr>
                    <tr><th>Basic Salary</th><td>${slip.basic_salary}</td></tr>
                    <tr><th>Allowance</th><td>${slip.allowance}</td></tr>
                    <tr><th>NSSF</th><td>${slip.nssf}</td></tr>
                    <tr><th>PAYE</th><td>${slip.paye}</td></tr>
                    <tr><th>NHIF</th><td>${slip.nhif}</td></tr>
                    <tr><th>Net Salary</th><td><strong>${slip.net_salary}</strong></td></tr>
                </table>
                <div class="text-right mt-2">
                    <a class="btn btn-pink" href="/payrolls/salary-slip-download/view/pdf?employee_id=${employeeId}&month=${month}" target="_blank">
                        <i class="fa fa-download"></i> Download PDF
                    </a>
                </div>
            `;
        } else {
            container.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        spinner.style.display = 'none';
        container.innerHTML = `<div class="alert alert-danger">An error occurred while fetching the salary slip.</div>`;
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
                                    <td>${slip.nssf ?? 0}</td>
                                    <td>${slip.paye ?? 0}</td>
                                    <td>${slip.nhif ?? 0}</td>
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
                                        <th>NSSF</th>
                                        <th>PAYE</th>
                                        <th>NHIF</th>
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
        if (confirm('Are you sure you want to rollback/delete this payroll?')) {
            $.ajax({
                url: '/payrolls/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Failed to rollback payroll.');
                }
            });
        }
    });

    // $(document).on('click', '.view-bank-btn', function() {
    //     let reference = $(this).data('reference');
    //     // Use AJAX to fetch and show bank info in a modal
    //     $('#bankModal').modal('show');
    //     $('#bankModal .modal-body').html('Loading...');

    //     $.get('/payrolls/' + reference + '/bank-details', function(data) {
    //         $('#bankModal .modal-body').html(data);
    //     });
    // });

    $(document).on('click', '.view-bank-btn', function () {
    let reference = $(this).data('reference');
    $('#bankModal').modal('show');
    $('#bankModal .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');

    $.get(`/payrolls/${reference}/bank-details`, function (data) {
        $('#bankModal .modal-body').html(data);
    }).fail(function () {
        $('#bankModal .modal-body').html('<div class="alert alert-danger">Failed to load bank details.</div>');
    });
});

</script>


@endsection
