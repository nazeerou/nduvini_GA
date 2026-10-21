@extends('layouts.app_header')

@section('content')

<style>
    /* =====================================================
       PAGE HEADER — clean & elegant (no background colour)
       ===================================================== */
    .emp-page-header {
        background: #fff;
        border: 1px solid #e9ecef;
        border-left: 4px solid #4e54c8;
        border-radius: 8px;
        padding: 18px 24px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    }
    .emp-page-header h2 {
        margin: 0;
        font-weight: 600;
        font-size: 20px;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .emp-page-header h2 i { color: #4e54c8; }
    .emp-page-header p {
        margin: 6px 0 0;
        color: #6c757d;
        font-size: 13px;
    }
    .emp-page-header .btn-add {
        background: #4e54c8;
        color: #fff;
        font-weight: 500;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        transition: .2s;
        box-shadow: 0 2px 6px rgba(78, 84, 200, .25);
    }
    .emp-page-header .btn-add:hover,
    .emp-page-header .btn-add:focus {
        background: #3f45b5;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(78, 84, 200, .3);
    }

    /* =====================================================
       TABS
       ===================================================== */
    .emp-tabs.nav-tabs { border-bottom: 2px solid #e9ecef; margin-bottom: 0; }
    .emp-tabs.nav-tabs > li > a {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 12px 20px;
        border-radius: 0;
        border-bottom: 3px solid transparent;
        transition: .2s;
    }
    .emp-tabs.nav-tabs > li.active > a,
    .emp-tabs.nav-tabs > li > a:hover {
        color: #4e54c8;
        background: transparent;
        border-bottom-color: #4e54c8;
    }

    /* =====================================================
       FILTER CARD
       ===================================================== */
    .filter-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 18px 20px;
        margin: 20px 0;
    }
    .filter-card label {
        font-weight: 600;
        font-size: 12px;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 6px;
    }
    .filter-card .form-control { border-radius: 6px; }

    /* =====================================================
       TABLE — fits container, no horizontal scroll
       ===================================================== */
    .emp-table-wrap {
        width: 100%;
        overflow: hidden;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: #fff;
    }

    .emp-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 13px;
    }

    .emp-table thead th {
        background: #f8f9fa;
        color: #495057;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: .4px;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef !important;
        padding: 10px 8px;
        white-space: nowrap;
    }

    .emp-table tbody td {
        padding: 10px 8px;
        vertical-align: middle;
        border-top: 1px solid #f1f3f5;
        word-break: break-word;
    }

    .emp-table tbody tr { transition: background .12s; }
    .emp-table tbody tr:hover { background: #f8f9ff; }

    /* Compact serial-number column */
    .emp-table .sn-col {
        width: 34px;
        max-width: 34px;
        text-align: center;
        color: #adb5bd;
        font-size: 12px;
        padding-left: 4px;
        padding-right: 4px;
    }

    /* =====================================================
       AVATAR + NAME
       ===================================================== */
    .emp-name { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .emp-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
        color: #fff; font-weight: 600; font-size: 13px;
        display: inline-flex; align-items: center; justify-content: center;
        flex: 0 0 32px;
    }
    .emp-name span:last-child {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* =====================================================
       ACTION BUTTONS — soft, rounded, uniform
       ===================================================== */
    .emp-actions {
        display: inline-flex;
        gap: 4px;
        align-items: center;
        justify-content: center;
    }
    .emp-actions form { margin: 0; }

    .emp-actions .btn-icon {
        width: 30px; height: 30px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        padding: 0;
        line-height: 1;
        cursor: pointer;
        transition: transform .12s ease, background .12s ease, color .12s ease;
    }
    .emp-actions .btn-view { background: #eaf1ff; color: #2f6fed; }
    .emp-actions .btn-view:hover {
        background: #2f6fed; color: #fff; transform: translateY(-1px);
    }
    .emp-actions .btn-edit { background: #e6f7ec; color: #1e9e52; }
    .emp-actions .btn-edit:hover {
        background: #1e9e52; color: #fff; transform: translateY(-1px);
    }
    .emp-actions .btn-del  { background: #fdecec; color: #d93433; }
    .emp-actions .btn-del:hover {
        background: #d93433; color: #fff; transform: translateY(-1px);
    }

    /* =====================================================
       MOBILE FINE-TUNING
       ===================================================== */
    @media (max-width: 767px) {
        .emp-page-header { padding: 16px 18px; }
        .emp-page-header h2 { font-size: 18px; }
        .emp-table { font-size: 12.5px; }
        .emp-table thead th,
        .emp-table tbody td { padding: 8px 6px; }
    }


    /* =====================================================
   ACTION BUTTONS — modern icon buttons
   ===================================================== */
.emp-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.emp-actions form { margin: 0; }

.emp-actions .btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    border: none;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    line-height: 1;
    cursor: pointer;
    transition:
        transform .15s ease,
        background-color .15s ease,
        color .15s ease,
        box-shadow .15s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .03);
}

/* View — blue */
.emp-actions .btn-view {
    background: #eaf1ff;
    color: #2f6fed;
}
.emp-actions .btn-view:hover,
.emp-actions .btn-view:focus {
    background: #2f6fed;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(47, 111, 237, .35);
    outline: none;
}

/* Edit — green */
.emp-actions .btn-edit {
    background: #e6f7ec;
    color: #1e9e52;
}
.emp-actions .btn-edit:hover,
.emp-actions .btn-edit:focus {
    background: #1e9e52;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(30, 158, 82, .35);
    outline: none;
}

/* Delete — red */
.emp-actions .btn-del {
    background: #fdecec;
    color: #d93433;
}
.emp-actions .btn-del:hover,
.emp-actions .btn-del:focus {
    background: #d93433;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(217, 52, 51, .35);
    outline: none;
}

/* Compact serial-number column */
.emp-table .sn-col {
    width: 34px;
    max-width: 34px;
    text-align: center;
    color: #adb5bd;
    font-size: 12px;
    padding-left: 4px;
    padding-right: 4px;
}

/* Mobile — slightly smaller tap targets */
@media (max-width: 767px) {
    .emp-actions { gap: 4px; }
    .emp-actions .btn-icon {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
}
</style>
<br/><br/>
{{-- ============ PAGE HEADER ============ --}}
<div class="emp-page-header">
    <div>
        <h2><i class="fa fa-users"></i> Employee Management</h2>
        <p>Manage all employees, contracts, transfers and exits from a single dashboard.</p>
    </div>
    <button class="btn btn-add" data-toggle="modal" data-target="#addEmployeeModal">
        <i class="fa fa-plus"></i> Add Employee
    </button>
</div>

{{-- ============ MAIN CARD ============ --}}
<div class="panel panel-default" style="border-radius:10px; overflow:hidden;">
    <ul class="nav nav-tabs emp-tabs">
        <li class="active"><a href="#tab_employees" data-toggle="tab"><i class="fa fa-list"></i> Employee Lists</a></li>
        <li><a href="#tab_contracts" data-toggle="tab"><i class="fa fa-file-text"></i> Contracts</a></li>
        <li><a href="#tab_transfers" data-toggle="tab"><i class="fa fa-exchange"></i> Transfers</a></li>
        <li><a href="#tab_promotions" data-toggle="tab"><i class="fa fa-sign-out"></i> Termination &amp; Exit</a></li>
    </ul>

    <div class="tab-content" style="padding: 20px;">

        {{-- ============ TAB 1: EMPLOYEE LISTS ============ --}}
        <div class="tab-pane fade in active" id="tab_employees">

            {{-- Filter bar --}}
            <div class="filter-card">
                <div class="row">
                    <div class="col-md-4">
                        <label for="filterBranch">Branch</label>
                        <select id="filterBranch" class="form-control">
                            <option value="">— All Branches —</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}"
                                    {{ Auth::user()->branch_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="filterContract">Contract Type</label>
                        <select id="filterContract" class="form-control">
                            <option value="">— All Contract Types —</option>
                            @foreach ($contractTypes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <div>
                            <button id="generate-slip" type="button" class="btn btn-pink btn-block1">
                                <i class="fa fa-file-pdf-o"></i> Generate Empoyee
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table (no horizontal scroll) --}}
            <div class="emp-table-wrap">
                <table class="table emp-table" id="datatable">
                    <thead>
                        <tr>
                            <th class="sn-col">SN</th>
                            <th>Full Name</th>
                            <th class="hidden-xs hidden-sm">Email</th>
                            <th class="hidden-xs hidden-sm">Department</th>
                            <th class="hidden-xs">Designation</th>
                            <th class="hidden-xs">Contract Type</th>
                            <th class="hidden-xs hidden-sm">Mobile</th>
                            <th class="hidden-xs">Branch</th>
                            <th class="text-center" style="width:130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employee_table_body">
                        @include('employees.partials.employee_table', ['employees' => $employees])
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============ TAB 2: CONTRACTS ============ --}}
        <div class="tab-pane fade" id="tab_contracts">
            <div class="text-center text-muted py-5">
                <i class="fa fa-file-text-o fa-3x d-block mb-3"></i>
                <h4>Contracts &amp; Probation</h4>
                <p>Coming soon — track contracts near expiry and employees on probation.</p>
            </div>
        </div>

        {{-- ============ TAB 3: TRANSFERS ============ --}}
        <div class="tab-pane fade" id="tab_transfers">
            <div class="text-center text-muted py-5">
                <i class="fa fa-exchange fa-3x d-block mb-3"></i>
                <h4>Transfer Records</h4>
                <p>Coming soon — history of branch &amp; department transfers.</p>
            </div>
        </div>

        {{-- ============ TAB 4: TERMINATION & EXIT ============ --}}
        <div class="tab-pane fade" id="tab_promotions">
            <div class="text-center text-muted py-5">
                <i class="fa fa-sign-out fa-3x d-block mb-3"></i>
                <h4>Termination &amp; Exit</h4>
                <p>Coming soon — record terminations, resignations and exit interviews.</p>
            </div>
        </div>
    </div>
</div>

{{-- ============ ADD EMPLOYEE MODAL ============ --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ url('staffs/managements/employees/save') }}"
              method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header" style="color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:.9;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-plus"></i> Add New Employee</h4>
            </div>

            <div class="modal-body">
                <h5 class="text-uppercase text-muted mb-3" style="font-size:12px; letter-spacing:1px;">
                    <i class="fa fa-user"></i> Personal Information
                </h5>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>First Name <span class="text-danger">*</span></label>
                        <input type="text" name="firstname" class="form-control" required placeholder="First name">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Middle Name</label>
                        <input type="text" name="middlename" class="form-control" placeholder="Optional">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Surname <span class="text-danger">*</span></label>
                        <input type="text" name="surname" class="form-control" required placeholder="Surname">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Mobile</label>
                        <input type="text" name="mobile" class="form-control" placeholder="Mobile number">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email address">
                    </div>
                    <div class="form-group col-md-4">
                        <label>NIDA Number</label>
                        <input type="text" name="nida_number" class="form-control" placeholder="NIDA number">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Designation</label>
                        <select name="designation_id" class="form-control">
                            <option value="">— Select —</option>
                            @foreach ($designations as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Department</label>
                        <select name="department_id" class="form-control">
                            <option value="">— Select —</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Branch</label>
                        <select name="branch_id" class="form-control">
                            <option value="">— Select —</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <h5 class="text-uppercase text-muted mb-3" style="font-size:12px; letter-spacing:1px;">
                    <i class="fa fa-file-text"></i> Contract &amp; Salary
                </h5>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Salary Group</label>
                        <select name="salary_group_id" class="form-control">
                            <option value="">— Select —</option>
                            @foreach ($salaryGroups as $g)
                                <option value="{{ $g->id }}">{{ $g->group_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contract Type</label>
                        <select name="contract_type_id" class="form-control">
                            <option value="">— Select —</option>
                            @foreach ($contractTypes as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contract Duration</label>
                        <input type="text" name="contract_duration" class="form-control" placeholder="e.g. 12 months">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-8">
                        <label>Contract Period</label>
                        <div class="input-daterange input-group" id="date-range" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control" name="start_date" placeholder="Start date" autocomplete="off">
                            <span class="input-group-addon" style="background:#4e54c8; color:#fff;">to</span>
                            <input type="text" class="form-control" name="end_date" placeholder="End date" autocomplete="off">
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="text-uppercase text-muted mb-3" style="font-size:12px; letter-spacing:1px;">
                    <i class="fa fa-paperclip"></i> Attach Documents
                </h5>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>CV / Certificates</label>
                        <input type="file" name="cv_file" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>NIDA</label>
                        <input type="file" name="nida_file" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Contract File</label>
                        <input type="file" name="contract_file" class="form-control">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Employee
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============ SCRIPTS ============ --}}
<script>
$(function () {

    function reloadEmployeeTable() {
        $.ajax({
            url: "{{ route('employees.filter') }}",
            type: "GET",
            data: {
                branch_id:        $('#filterBranch').val(),
                contract_type_id: $('#filterContract').val()
            },
            beforeSend: function () {
                $('#employee_table_body').html(
                    '<tr><td colspan="9" class="text-center py-4">' +
                    '<i class="fa fa-spinner fa-spin"></i> Loading employees…</td></tr>'
                );
            },
            success: function (html) {
                $('#employee_table_body').html(html);
            },
            error: function () {
                $('#employee_table_body').html(
                    '<tr><td colspan="9" class="text-center text-danger py-4">' +
                    'Failed to load employees. Please try again.</td></tr>'
                );
            }
        });
    }

    $('#filterBranch, #filterContract').on('change', reloadEmployeeTable);

    $('#generate-slip').on('click', function () {
        var params = $.param({
            branch_id:        $('#filterBranch').val() || '',
            contract_type_id: $('#filterContract').val() || ''
        });
        window.location.href = "{{ route('employees.generatePdf') }}?" + params;
    });

    if ($.fn.datepicker) {
        $('.input-daterange').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }

    @if(session('success'))
        $('#display_message').show().html(
            '<div class="alert alert-success alert-dismissible fade in">' +
            '<button class="close" data-dismiss="alert">&times;</button>' +
            '<i class="fa fa-check-circle"></i> {{ session('success') }}</div>'
        );
    @endif
    @if(session('error'))
        $('#display_message').show().html(
            '<div class="alert alert-danger alert-dismissible fade in">' +
            '<button class="close" data-dismiss="alert">&times;</button>' +
            '<i class="fa fa-exclamation-circle"></i> {{ session('error') }}</div>'
        );
    @endif
});
</script>

@endsection