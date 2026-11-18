
@extends('layouts.app_header')

@section('content')

<div class="container-fluid sms-management">

    <style>
        .sms-management { padding-bottom: 30px; }
        .sms-management .page-title {
            font-size: 20px;
            margin: 10px 0 20px;
            color: #34495e;
        }
        .sms-management .nav-tabs {
            border-bottom: 1px solid #ddd;
        }
        .sms-management .nav-tabs > li > a {
            font-weight: 600;
            padding: 12px 18px;
        }
        .sms-management .panel {
            border-radius: 4px;
            border: 1px solid #e5e9ed;
            box-shadow: 0 2px 7px rgba(0,0,0,.035);
        }
        .sms-management .panel-heading {
            padding: 14px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e9ed;
        }
        .sms-management .panel-title {
            font-size: 15px;
            font-weight: 600;
        }
        .sms-management .form-control {
            border-radius: 4px;
            box-shadow: none;
            height: 38px;
        }
        .sms-management textarea.form-control {
            height: auto;
        }
        .sms-management .recipient-box {
            border: 1px solid #e5e9ed;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .sms-management .recipient-box h5 {
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 15px;
            color: #34495e;
        }
        .sms-management .selection-list {
            border: 1px solid #eee;
            border-radius: 4px;
            max-height: 260px;
            overflow-y: auto;
            padding: 10px;
        }
        .sms-management .selection-list label {
            display: block;
            padding: 7px 5px;
            border-bottom: 1px solid #f3f3f3;
            font-weight: normal;
            margin: 0;
        }
        .sms-management .selection-list label:last-child {
            border-bottom: 0;
        }
        .sms-management .selection-tools {
            margin-bottom: 10px;
        }
        .sms-management .selection-tools button {
            margin-right: 5px;
        }
        .sms-management .recipient-summary {
            background: #f5f8fb;
            border: 1px solid #e5e9ed;
            border-radius: 4px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        .sms-management .recipient-summary strong {
            font-size: 20px;
        }
        .sms-management .status-approved {
            background: #e8f6ee;
            color: #21834b;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .sms-management .status-pending {
            background: #fff5df;
            color: #a87512;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .sms-management .status-rejected {
            background: #fdecec;
            color: #b52b2b;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .sms-management .table > thead > tr > th {
            background: #f8fafc;
            font-weight: 600;
        }
        .sms-management .section-description {
            color: #89939d;
            font-size: 12px;
            margin-bottom: 12px;
        }
        .sms-management .required { color: #d9534f; }
        .sms-management .recipient-type {
            margin-right: 15px;
            font-weight: 500;
        }
        .sms-management .recipient-type input {
            margin-right: 5px;
        }
        .sms-management .branch-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 10px;
        }
        .sms-management .branch-item label {
            font-weight: normal;
        }
        .sms-management .sms-counter {
            color: #89939d;
            font-size: 12px;
        }
        @media(max-width:767px) {
            .sms-management .nav-tabs > li > a {
                padding: 10px;
                font-size: 12px;
            }
            .sms-management .selection-list {
                max-height: 220px;
            }
        }
    </style>

    <div class="row">
        <div class="col-md-12">

            <h4 class="page-title">
                <i class="fa fa-comments"></i> SMS Management
                <small class="text-muted">Manage sender IDs, recipients and SMS communication</small>
            </h4>

            @if ($errors->any())
    <div class="alert alert-danger">
        <strong><i class="fa fa-exclamation-triangle"></i> SMS Error</strong>

        <ul style="margin-top: 8px; margin-bottom: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('message'))
    <div class="alert alert-info">
        {{ session('message') }}
    </div>
@endif
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#sendSms">
                        <i class="fa fa-paper-plane"></i> Send SMS
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#manageSms">
                        <i class="fa fa-id-card"></i> Sender IDs
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#smsReport">
                        <i class="fa fa-bar-chart"></i> SMS Reports
                    </a>
                </li>
            </ul>

            <div class="tab-content" style="margin-top:20px;">

                <!-- ================================================= -->
                <!-- SEND SMS -->
                <!-- ================================================= -->

                <div id="sendSms" class="tab-pane fade in active">

                    <form method="POST" action="{{ route('sms.send') }}" id="sendSmsForm">
                        @csrf

                        <div class="panel panel-default">

                            <div class="panel-heading">
                                <strong><i class="fa fa-envelope"></i> Compose SMS</strong>
                            </div>

                            <div class="panel-body">

                                <!-- SENDER ID -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Sender ID <span class="required">*</span></label>

                                            <select name="sender_id" class="form-control" required>
                                                <option value="">-- Select Approved Sender ID --</option>

                                                @foreach($senderIds as $sender)
                                                    @if(strtolower($sender->status) == 'approved')
                                                        <option value="{{ $sender->id }}"
                                                            {{ old('id') == $sender->name ? 'selected' : '' }}>
                                                            {{ $sender->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>

                                            <small class="text-muted">
                                                Only approved Sender IDs are available.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- RECIPIENT TYPES -->

                                <h4 class="panel-title">
                                    <i class="fa fa-users"></i> Select Recipients
                                </h4>

                                <p class="section-description">
                                    Select one or more recipient categories. You can combine staff, customers and custom numbers.
                                </p>

                                <div class="form-group">
                                    <label class="recipient-type">
                                        <input type="checkbox" class="recipient-category" value="staff" checked>
                                        Staff
                                    </label>

                                    <label class="recipient-type">
                                        <input type="checkbox" class="recipient-category" value="customers">
                                        Customers
                                    </label>

                                    <label class="recipient-type">
                                        <input type="checkbox" class="recipient-category" value="custom">
                                        Custom Recipients
                                    </label>
                                </div>

                                <!-- STAFF RECIPIENTS -->

                                <div class="recipient-box recipient-section" id="staffSection">

                                    <h5>
                                        <i class="fa fa-user"></i> Staff Recipients
                                    </h5>

                                    <p class="section-description">
                                        Choose branches first, then select all staff or specific employees.
                                    </p>

                                    <div class="form-group">
                                        <label>Select Branches</label>

                                        <div class="selection-tools">
                                            <label>
                                                <input type="checkbox" id="allBranches" checked>
                                                All Branches
                                            </label>
                                        </div>

                                        <div class="well well-sm" style="background:#fafafa;margin-bottom:0;">

                                            @foreach($branches as $branch)
                                                <div class="branch-item">
                                                    <label>
                                                        <input type="checkbox"
                                                               class="branch-checkbox"
                                                               name="branch_ids[]"
                                                               value="{{ $branch->id }}"
                                                               checked>
                                                        {{ $branch->branch_name }}
                                                    </label>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Select Staff</label>

                                        <div class="selection-tools">
                                            <label>
                                                <input type="checkbox" id="allStaff" checked>
                                                All Staff
                                            </label>

                                            <button type="button" class="btn btn-xs btn-default" id="selectStaff">
                                                Select All
                                            </button>

                                            <button type="button" class="btn btn-xs btn-default" id="clearStaff">
                                                Clear
                                            </button>
                                        </div>

                                        <input type="text"
                                               class="form-control"
                                               id="staffSearch"
                                               placeholder="Search staff by name or mobile number..."
                                               style="margin-bottom:10px;">

                                        <div class="selection-list" id="staffList">

                                            @foreach($employees as $employee)
                                                <label class="staff-item"
                                                       data-branch="{{ $employee->branch_id }}"
                                                       data-search="{{ strtolower(($employee->firstname ?? '') . ' ' . ($employee->mobile ?? '')) }}">

                                                    <input type="checkbox"
                                                           class="staff-checkbox"
                                                           name="employee_ids[]"
                                                           value="{{ $employee->id }}"
                                                           checked>

                                                    {{ $employee->firstname }}
                                                    <span class="text-muted">
                                                        ({{ $employee->mobile }})
                                                    </span>

                                                </label>
                                            @endforeach

                                            @if($employees->count() == 0)
                                                <p class="text-muted">No staff members found.</p>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <!-- CUSTOMER RECIPIENTS -->

                                <div class="recipient-box recipient-section" id="customersSection" style="display:none;">

                                    <h5>
                                        <i class="fa fa-address-book"></i> Customer Recipients
                                    </h5>

                                    <p class="section-description">
                                        Send SMS to all customers or select specific customers.
                                    </p>

                                    <div class="selection-tools">

                                        <label>
                                            <input type="checkbox" id="allCustomers" checked>
                                            All Customers
                                        </label>

                                        <button type="button" class="btn btn-xs btn-default" id="selectCustomers">
                                            Select All
                                        </button>

                                        <button type="button" class="btn btn-xs btn-default" id="clearCustomers">
                                            Clear
                                        </button>

                                    </div>

                                    <input type="text"
                                           class="form-control"
                                           id="customerSearch"
                                           placeholder="Search customer by name or mobile number..."
                                           style="margin-bottom:10px;">

                                    <div class="selection-list" id="customerList">

                                        @foreach($customers as $customer)
                                            <label class="customer-item"
                                                   data-search="{{ strtolower(($customer->client_name ?? '') . ' ' . ($customer->mobile ?? '')) }}">

                                                <input type="checkbox"
                                                       class="customer-checkbox"
                                                       name="customer_ids[]"
                                                       value="{{ $customer->id }}"
                                                       checked>

                                                {{ $customer->client_name }}
                                                <span class="text-muted">
                                                    ({{ $customer->phone }})
                                                </span>

                                            </label>
                                        @endforeach

                                        @if($customers->count() == 0)
                                            <p class="text-muted">No customers found.</p>
                                        @endif

                                    </div>
                                </div>

                                <!-- CUSTOM RECIPIENTS -->

                                <div class="recipient-box recipient-section" id="customSection" style="display:none;">

                                    <h5>
                                        <i class="fa fa-phone"></i> Custom Recipients
                                    </h5>

                                    <p class="section-description">
                                        Enter mobile numbers that are not registered as staff or customers.
                                    </p>

                                    <div class="form-group">
                                        <label>Mobile Number(s)</label>

                                        <textarea name="custom_recipients"
                                                  id="customRecipients"
                                                  class="form-control"
                                                  rows="3"
                                                  placeholder="255712345678, 255765432109"></textarea>

                                        <small class="text-muted">
                                            Separate numbers using commas, spaces or new lines.
                                            Use international format, e.g. 2557XXXXXXXX.
                                        </small>
                                    </div>

                                </div>

                                <!-- RECIPIENT SUMMARY -->

                                <div class="recipient-summary">
                                    <div class="row">
                                        <div class="col-xs-7">
                                            <span class="text-muted">Selected recipients</span>
                                        </div>
                                        <div class="col-xs-5 text-right">
                                            <strong id="recipientCount">0</strong>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- CHANNEL -->

                                <div class="form-group">
                                    <label>Communication Channel</label><br>

                                    <label class="radio-inline">
                                        <input type="radio" name="channel" value="sms" checked>
                                        <i class="fa fa-comment"></i> SMS
                                    </label>

                                    <label class="radio-inline">
                                        <input type="radio" name="channel" value="whatsapp">
                                        <i class="fa fa-whatsapp"></i> WhatsApp
                                    </label>
                                </div>

                                <!-- MESSAGE -->

                                <div class="form-group">
                                    <label>Message <span class="required">*</span></label>

                                    <textarea name="message"
                                              id="smsMessage"
                                              class="form-control"
                                              rows="5"
                                              maxlength="160"
                                              placeholder="Type your message here..."
                                              required>{{ old('message') }}</textarea>

                                    <div class="clearfix">
                                        <small class="text-muted">
                                            Standard SMS supports 160 characters per segment.
                                        </small>

                                        <small class="pull-right sms-counter">
                                            <span id="charCount">0</span>/160
                                        </small>
                                    </div>
                                </div>

                            </div>

                            <div class="panel-footer text-right">

                                <button type="reset" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>

                                <button type="submit" class="btn btn-success" id="sendSmsButton">
                                    <i class="fa fa-paper-plane"></i> Send SMS
                                </button>

                            </div>

                        </div>
                    </form>

                </div>

                <!-- ================================================= -->
                <!-- SENDER ID CRUD -->
                <!-- ================================================= -->

                <div id="manageSms" class="tab-pane fade">

                    <div class="panel panel-default">

                        <div class="panel-heading clearfix">

                            <strong>
                                <i class="fa fa-id-card"></i> Sender ID Management
                            </strong>

                            <button type="button"
                                    class="btn btn-success btn-sm pull-right"
                                    data-toggle="modal"
                                    data-target="#senderModal"
                                    onclick="resetSenderForm()">

                                <i class="fa fa-plus"></i> Add Sender ID

                            </button>

                        </div>

                        <div class="panel-body">

                            <p class="text-muted">
                                Register and manage the Sender IDs used when sending SMS messages.
                            </p>

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped">

                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Sender ID</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($senderIds as $key => $sender)

                                            <tr>

                                                <td>{{ $key + 1 }}</td>

                                                <td>
                                                    <strong>{{ $sender->name }}</strong>
                                                </td>

                                                <td>
                                                    @if(strtolower($sender->status) == 'approved')
                                                        <span class="status-approved">Approved</span>
                                                    @elseif(strtolower($sender->status) == 'rejected')
                                                        <span class="status-rejected">Rejected</span>
                                                    @else
                                                        <span class="status-pending">Pending</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ \Carbon\Carbon::parse($sender->created_at)->format('d M Y') }}
                                                </td>

                                                <td>

                                                    <button type="button"
                                                            class="btn btn-xs btn-primary edit-sender"
                                                            data-id="{{ $sender->id }}"
                                                            data-sender="{{ $sender->name }}"
                                                            data-status="{{ $sender->status }}"
                                                            data-toggle="modal"
                                                            data-target="#senderModal">

                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <form method="POST"
                                                          action="{{ route('sms.sender-ids.destroy', $sender->id) }}"
                                                          style="display:inline;"
                                                          onsubmit="return confirm('Are you sure you want to delete this Sender ID?')">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-xs btn-danger">
                                                            <i class="fa fa-trash"></i>
                                                        </button>

                                                    </form>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    No Sender IDs registered.
                                                </td>
                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- ================================================= -->
                <!-- SMS REPORT -->
                <!-- ================================================= -->

                <div id="smsReport" class="tab-pane fade">

                    <div class="panel panel-default">

                        <div class="panel-heading">
                            <strong>
                                <i class="fa fa-bar-chart"></i> SMS Delivery Reports
                            </strong>
                        </div>

                        <div class="panel-body">

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped">

                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Sender ID</th>
                                            <th>Recipient</th>
                                            <th>Message</th>
                                            <th>Channel</th>
                                            <th>Status</th>
                                            <th>Sent At</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($logs as $key => $r)

                                            <tr>
                                                <td>{{ $key + 1 }}</td>

                                                <td>{{ $r->sender_id ?? '-' }}</td>

                                                <td>{{ $r->recipient }}</td>

                                                <td>{{ $r->message }}</td>

                                                <td>{{ ucfirst($r->channel) }}</td>

                                                <td>
                                                    @if(strtolower($r->status) == 'sent' || strtolower($r->status) == 'delivered')
                                                        <span class="label label-success">{{ ucfirst($r->status) }}</span>
                                                    @elseif(strtolower($r->status) == 'failed')
                                                        <span class="label label-danger">{{ ucfirst($r->status) }}</span>
                                                    @else
                                                        <span class="label label-warning">{{ ucfirst($r->status) }}</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}
                                                </td>
                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    No SMS records found.
                                                </td>
                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>


<!-- ================================================= -->
<!-- ADD / EDIT SENDER ID MODAL -->
<!-- ================================================= -->

<div class="modal fade" id="senderModal" tabindex="-1" role="dialog">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <form method="POST" action="{{ route('sms.sender-ids.store') }}" id="senderForm">

                @csrf

                <div id="senderMethod"></div>

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                    <h4 class="modal-title" id="senderModalTitle">
                        <i class="fa fa-plus"></i> Add Sender ID
                    </h4>

                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Sender ID <span class="required">*</span></label>

                        <input type="text"
                               name="sender_id"
                               id="senderName"
                               class="form-control"
                               maxlength="11"
                               placeholder="e.g. MANANI TECH"
                               required>

                        <small class="text-muted">
                            Maximum 11 characters. The final limit depends on your SMS provider and regulatory approval.
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Approval Status</label>

                        <select name="status" id="senderStatus" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>

                        <small class="text-muted">
                            Only authorized administrators should change approval status.
                        </small>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Save Sender ID
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
$(document).ready(function () {

    // =====================================================
    // RECIPIENT CATEGORY VISIBILITY
    // =====================================================

    function updateRecipientSections() {

        $('.recipient-section').hide();

        if ($('.recipient-category[value="staff"]').is(':checked')) {
            $('#staffSection').show();
        }

        if ($('.recipient-category[value="customers"]').is(':checked')) {
            $('#customersSection').show();
        }

        if ($('.recipient-category[value="custom"]').is(':checked')) {
            $('#customSection').show();
        }

        updateRecipientCount();
    }

    $('.recipient-category').on('change', updateRecipientSections);

    // =====================================================
    // BRANCH SELECTION
    // =====================================================

    function updateBranchSelection() {

        let allBranches = $('.branch-checkbox').length;
        let selectedBranches = $('.branch-checkbox:checked').length;

        $('#allBranches').prop(
            'checked',
            allBranches > 0 && allBranches === selectedBranches
        );

        $('.staff-item').each(function () {

            let branchId = String($(this).data('branch'));

            let selected = $('.branch-checkbox:checked').map(function () {
                return String($(this).val());
            }).get();

            let visible = selected.includes(branchId);

            $(this).toggle(visible);

            if (!visible) {
                $(this).find('.staff-checkbox').prop('checked', false);
            }

        });

        updateStaffSelection();
        updateRecipientCount();
    }

    $('#allBranches').on('change', function () {

        $('.branch-checkbox').prop('checked', $(this).is(':checked'));

        updateBranchSelection();

    });

    $('.branch-checkbox').on('change', updateBranchSelection);

    // =====================================================
    // STAFF SELECTION
    // =====================================================

    function updateStaffSelection() {

        let visibleStaff = $('.staff-item:visible .staff-checkbox');
        let selectedStaff = visibleStaff.filter(':checked');

        $('#allStaff').prop(
            'checked',
            visibleStaff.length > 0 && visibleStaff.length === selectedStaff.length
        );

        updateRecipientCount();
    }

    $('#allStaff').on('change', function () {

        $('.staff-item:visible .staff-checkbox').prop(
            'checked',
            $(this).is(':checked')
        );

        updateStaffSelection();

    });

    $('#selectStaff').on('click', function () {
        $('.staff-item:visible .staff-checkbox').prop('checked', true);
        updateStaffSelection();
    });

    $('#clearStaff').on('click', function () {
        $('.staff-checkbox').prop('checked', false);
        updateStaffSelection();
    });

    $(document).on('change', '.staff-checkbox', updateStaffSelection);

    // =====================================================
    // STAFF SEARCH
    // =====================================================

    $('#staffSearch').on('keyup', function () {

        let search = $(this).val().toLowerCase();

        $('.staff-item').each(function () {

            let branchSelected = $('.branch-checkbox:checked').map(function () {
                return String($(this).val());
            }).get();

            let branchVisible = branchSelected.includes(
                String($(this).data('branch'))
            );

            let matches = String($(this).data('search')).includes(search);

            $(this).toggle(branchVisible && matches);

        });

    });

    // =====================================================
    // CUSTOMER SELECTION
    // =====================================================

    function updateCustomerSelection() {

        let customers = $('.customer-checkbox');
        let selected = customers.filter(':checked');

        $('#allCustomers').prop(
            'checked',
            customers.length > 0 && customers.length === selected.length
        );

        updateRecipientCount();
    }

    $('#allCustomers').on('change', function () {

        $('.customer-checkbox').prop('checked', $(this).is(':checked'));

        updateCustomerSelection();

    });

    $('#selectCustomers').on('click', function () {
        $('.customer-item:visible .customer-checkbox').prop('checked', true);
        updateCustomerSelection();
    });

    $('#clearCustomers').on('click', function () {
        $('.customer-checkbox').prop('checked', false);
        updateCustomerSelection();
    });

    $(document).on('change', '.customer-checkbox', updateCustomerSelection);

    // =====================================================
    // CUSTOMER SEARCH
    // =====================================================

    $('#customerSearch').on('keyup', function () {

        let search = $(this).val().toLowerCase();

        $('.customer-item').each(function () {

            let matches = String($(this).data('search')).includes(search);

            $(this).toggle(matches);

        });

    });

    // =====================================================
    // RECIPIENT COUNT
    // =====================================================

    function updateRecipientCount() {

        let count = 0;

        if ($('.recipient-category[value="staff"]').is(':checked')) {
            count += $('.staff-checkbox:checked').filter(function () {
                return $(this).closest('.staff-item').is(':visible');
            }).length;
        }

        if ($('.recipient-category[value="customers"]').is(':checked')) {
            count += $('.customer-checkbox:checked').length;
        }

        if ($('.recipient-category[value="custom"]').is(':checked')) {

            let numbers = $('#customRecipients').val()
                .split(/[\s,;]+/)
                .map(function (number) {
                    return number.trim();
                })
                .filter(Boolean);

            count += [...new Set(numbers)].length;
        }

        $('#recipientCount').text(count);
    }

    $('#customRecipients').on('input', updateRecipientCount);

    // =====================================================
    // MESSAGE COUNTER
    // =====================================================

    function updateMessageCounter() {
        $('#charCount').text($('#smsMessage').val().length);
    }

    $('#smsMessage').on('input', updateMessageCounter);

    updateMessageCounter();

    // =====================================================
    // SENDER ID EDIT
    // =====================================================

    $('.edit-sender').on('click', function () {

        let id = $(this).data('id');
        let sender = $(this).data('sender');
        let status = $(this).data('status');

        $('#senderModalTitle').html('<i class="fa fa-edit"></i> Edit Sender ID');

        $('#senderForm').attr(
            'action',
            "{{ url('/sms/sender-ids') }}/" + id
        );

        $('#senderMethod').html('<input type="hidden" name="_method" value="PUT">');

        $('#senderName').val(sender);
        $('#senderStatus').val(String(status).toLowerCase());

    });

    // =====================================================
    // RESET SENDER FORM
    // =====================================================

    window.resetSenderForm = function () {

        $('#senderForm').attr(
            'action',
            "{{ route('sms.sender-ids.store') }}"
        );

        $('#senderMethod').html('');

        $('#senderName').val('');
        $('#senderStatus').val('pending');

        $('#senderModalTitle').html('<i class="fa fa-plus"></i> Add Sender ID');

    };

    // =====================================================
    // FORM VALIDATION
    // =====================================================

    $('#sendSmsForm').on('submit', function (e) {

        let selectedCategories = $('.recipient-category:checked').length;

        if (selectedCategories === 0) {
            e.preventDefault();
            alert('Please select at least one recipient category.');
            return false;
        }

        let staffSelected = $('.recipient-category[value="staff"]').is(':checked');
        let customersSelected = $('.recipient-category[value="customers"]').is(':checked');
        let customSelected = $('.recipient-category[value="custom"]').is(':checked');

        let count = 0;

        if (staffSelected) {
            count += $('.staff-checkbox:checked').filter(function () {
                return $(this).closest('.staff-item').is(':visible');
            }).length;
        }

        if (customersSelected) {
            count += $('.customer-checkbox:checked').length;
        }

        if (customSelected) {
            let numbers = $('#customRecipients').val()
                .split(/[\s,;]+/)
                .filter(Boolean);

            count += [...new Set(numbers)].length;
        }

        if (count === 0) {
            e.preventDefault();
            alert('Please select at least one recipient.');
            return false;
        }

        if (!confirm('Are you sure you want to send this SMS to ' + count + ' recipient(s)?')) {
            e.preventDefault();
            return false;
        }

        $('#sendSmsButton')
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Processing...');

    });

    // INITIALIZE
    updateRecipientSections();
    updateBranchSelection();
    updateCustomerSelection();

});
</script>

@endsection
