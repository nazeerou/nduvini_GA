@extends('layouts.app_header')
@section('content')
<style>
    /* ============================================================
       REFINED PALETTE — single navy brand, toned neutrals,
       modern semantic accents. No decorative colors added.
       ============================================================ */
    :root {
        /* Brand — navy, expanded into a proper tonal scale */
        --brand-50:      #eef3f8;
        --brand-100:     #d9e3ee;
        --brand-200:     #b3c7dd;
        --brand-500:     #2c5282;
        --brand:         #5790da;   /* primary — unchanged */
        --brand-700:     #172d49;
        --brand-900:     #0f1f33;

        /* Neutrals — clean, no blue cast */
        --ink-900:       #1a2332;
        --ink-800:       #2c3e50;
        --ink-700:       #3d4d60;
        --ink-500:       #6c757d;
        --ink-400:       #8a949e;
        --ink-300:       #b8c1ca;
        --ink-200:       #dde3e9;
        --ink-100:       #eef1f4;
        --ink-50:        #f7f9fb;

        /* Borders / surfaces */
        --border:        #e2e6ea;
        --border-strong: #cbd3db;
        --bg-soft:       #f7f9fb;
        --bg-hover:      #f3f6f9;

        /* Semantic — refined, modern, accessible */
        --success:       #059669;
        --success-50:    #ecfdf5;
        --success-200:   #a7f3d0;
        --warning:       #b45309;
        --warning-50:    #fef7e6;
        --warning-200:   #fcd98b;
        --danger:        #c0392b;
        --danger-50:     #fdf2f1;
        --danger-200:    #f5c2bc;

        /* Motion */
        --ease:          cubic-bezier(0.16, 1, 0.3, 1);
        --radius-sm:     4px;
        --radius:        6px;
        --radius-md:     8px;

        /* Shadows — layered, softer */
        --shadow-xs:     0 1px 2px rgba(15, 31, 51, 0.04);
        --shadow-sm:     0 1px 3px rgba(15, 31, 51, 0.06), 0 1px 2px rgba(15, 31, 51, 0.03);
        --shadow-md:     0 4px 10px -2px rgba(15, 31, 51, 0.08), 0 2px 4px -2px rgba(15, 31, 51, 0.04);
        --shadow-lg:     0 12px 22px -6px rgba(15, 31, 51, 0.12), 0 4px 8px -4px rgba(15, 31, 51, 0.06);
    }

    body { color: var(--ink-800); }

    /* ============================================================
       PANEL
       ============================================================ */
    .panel-custom {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: #fff !important;
        box-shadow: var(--shadow-sm);
    }
    .panel-custom .panel-heading {
        border-bottom: 1px solid var(--border);
        padding: 22px 24px;
        background: #fff;
        border-radius: var(--radius-md) var(--radius-md) 0 0;
    }
    .panel-title1 {
        font-size: 19px;
        font-weight: 700;
        color: var(--ink-900);
        margin: 0;
        letter-spacing: -0.015em;
    }

    /* ============================================================
       SUMMARY CARDS — refined tones per state
       ============================================================ */
    .summary-row { margin-bottom: 22px; }

    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-top: 3px solid var(--brand);
        border-radius: var(--radius);
        padding: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: var(--shadow-sm);
        transition: all 0.22s var(--ease);
    }
    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
        border-color: var(--border-strong);
    }
    .stat-card .label {
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.1em;
        color: var(--ink-500);
        font-weight: 700;
        margin-bottom: 8px;
    }
    .stat-card .value {
        font-size: 24px;
        font-weight: 700;
        color: var(--ink-900);
        line-height: 1.1;
        letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
    }
    .stat-card .icon {
        width: 48px; height: 48px;
        border-radius: 50%;
        background: var(--brand-50);
        color: var(--brand);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        transition: transform 0.22s var(--ease);
    }
    .stat-card:hover .icon { transform: scale(1.08); }

    /* Card 2 — Total Paid: tinted success top + icon */
    .summary-row > div:nth-child(2) .stat-card { border-top-color: var(--success); }
    .summary-row > div:nth-child(2) .stat-card .icon {
        background: var(--success-50);
        color: var(--success);
    }
    .summary-row > div:nth-child(2) .stat-card .value { color: var(--success); }

    /* Card 3 — Outstanding: tinted danger top + icon */
    .summary-row > div:nth-child(3) .stat-card { border-top-color: var(--danger); }
    .summary-row > div:nth-child(3) .stat-card .icon {
        background: var(--danger-50);
        color: var(--danger);
    }
    .summary-row > div:nth-child(3) .stat-card .value { color: var(--danger); }

    /* ============================================================
       BUTTONS — richer states
       ============================================================ */
    .btn {
        font-weight: 500;
        letter-spacing: 0.01em;
        border-radius: var(--radius-sm);
        transition: all 0.18s var(--ease);
    }
    .btn-primary {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
        box-shadow: 0 1px 2px rgba(30, 58, 95, 0.15);
    }
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
        background: var(--brand-700) !important;
        border-color: var(--brand-700) !important;
        box-shadow: 0 4px 10px rgba(30, 58, 95, 0.22);
        transform: translateY(-1px);
    }
    .btn-default {
        background: #fff;
        border: 1px solid var(--border-strong);
        color: var(--ink-700);
        box-shadow: var(--shadow-xs);
    }
    .btn-default:hover, .btn-default:focus {
        background: var(--bg-soft);
        border-color: var(--ink-400);
        color: var(--brand);
    }
    .btn-rounded { border-radius: var(--radius-sm) !important; }
    .btn-sm { padding: 5px 10px; font-size: 12px; }

    /* ============================================================
       FILTER FORM — soft surface + refined inputs
       ============================================================ */
    .filter-form label {
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.07em;
        color: var(--ink-500); margin-bottom: 5px;
    }
    .filter-form .form-control {
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        height: 38px;
        font-size: 13px;
        color: var(--ink-900);
        background: #fff;
        box-shadow: var(--shadow-xs);
        transition: all 0.15s var(--ease);
    }
    .filter-form .form-control:hover { border-color: var(--ink-400); }
    .filter-form .form-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.10);
        background: #fff;
    }
    .filter-form .btn { height: 38px; }

    /* ============================================================
       TABLE — refined header, softer rows, calmer hover
       ============================================================ */
    table.dataTable { border-collapse: separate; border-spacing: 0; }
    #datatable thead th {
        background: var(--brand) !important;
        color: #fff !important;
        text-transform: uppercase;
        font-size: 11px !important;
        font-weight: 600;
        letter-spacing: 0.07em;
        padding: 13px 12px !important;
        border: none !important;
        white-space: nowrap;
    }
    #datatable thead th:first-child { border-top-left-radius: var(--radius-sm); }
    #datatable thead th:last-child  { border-top-right-radius: var(--radius-sm); }

    #datatable tbody td {
        font-size: 13px;
        padding: 13px 12px !important;
        border-top: 1px solid var(--ink-100) !important;
        border-bottom: none !important;
        vertical-align: middle;
        color: var(--ink-800);
        background: #fff;
        transition: background 0.12s var(--ease);
    }
    #datatable tbody tr:last-child td { border-bottom: 1px solid var(--ink-100) !important; }
    #datatable tbody tr:hover td { background: var(--bg-hover); }

    #datatable tbody td:nth-child(1) { color: var(--ink-400); font-weight: 500; font-size: 12px; }
    #datatable tbody td:nth-child(2) { color: var(--ink-900); }
    #datatable tbody td:nth-child(5),
    #datatable tbody td:nth-child(6),
    #datatable tbody td:nth-child(7) {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .text-success { color: var(--success) !important; font-weight: 600; }
    .text-danger  { color: var(--danger)  !important; font-weight: 600; }

    /* DataTables empty message */
    .dataTables_empty {
        padding: 40px 20px !important;
        text-align: center;
        color: var(--ink-400) !important;
        font-style: italic;
        background: var(--bg-soft) !important;
    }

    /* DataTables pagination — tinted for the theme */
    .dataTables_paginate .paginate_button {
        border-radius: var(--radius-sm) !important;
        border: 1px solid var(--border) !important;
        background: #fff !important;
        color: var(--ink-700) !important;
        margin: 0 2px;
        transition: all 0.15s var(--ease);
    }
    .dataTables_paginate .paginate_button:hover {
        background: var(--brand-50) !important;
        border-color: var(--brand-200) !important;
        color: var(--brand) !important;
    }
    .dataTables_paginate .paginate_button.current,
    .dataTables_paginate .paginate_button.current:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
    }
    .dataTables_info { color: var(--ink-500) !important; font-size: 12px; }

    /* ============================================================
       STATUS BADGES — tinted pills with colored dot (not flat)
       ============================================================ */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px 4px 8px;
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        line-height: 1;
        border: 1px solid transparent;
    }
    .status-badge::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
    }
    .badge-paid {
        color: var(--success);
        background: var(--success-50);
        border-color: var(--success-200);
    }
    .badge-partial {
        color: var(--warning);
        background: var(--warning-50);
        border-color: var(--warning-200);
    }
    .badge-unpaid {
        color: var(--danger);
        background: var(--danger-50);
        border-color: var(--danger-200);
    }

    /* ============================================================
       MODALS
       ============================================================ */
    .modal-content {
        border-radius: var(--radius-md);
        border: none;
        box-shadow: var(--shadow-lg);
        background: #fff;
        overflow: hidden;
    }
    .modal-header {
        border-bottom: 1px solid var(--border);
        padding: 17px 22px;
        background: #fff;
    }
    .modal-header .close {
        font-size: 22px;
        opacity: 0.35;
        transition: opacity 0.15s var(--ease);
    }
    .modal-header .close:hover { opacity: 1; }
    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--ink-900);
        letter-spacing: -0.01em;
    }
    .modal-body { padding: 22px; }
    .modal-footer {
        border-top: 1px solid var(--border);
        padding: 13px 22px;
        background: var(--bg-soft);
    }
    .modal.active { z-index: 2000; }

    .modal .form-control {
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-sm);
        height: 38px;
        font-size: 13px;
        color: var(--ink-900);
        box-shadow: var(--shadow-xs);
        transition: all 0.15s var(--ease);
    }
    .modal .form-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.10);
    }
    .modal label.control-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--ink-700);
        margin-bottom: 6px;
    }

    /* Payment summary table */
    .pay-summary { margin-bottom: 18px; }
    .pay-summary tr:not(:last-child) th,
    .pay-summary tr:not(:last-child) td {
        border-bottom: 1px solid var(--ink-100) !important;
    }
    .pay-summary th {
        background: var(--bg-soft) !important;
        color: var(--ink-500) !important;
        text-transform: none !important;
        font-weight: 500 !important;
        font-size: 12.5px !important;
        width: 42%;
        padding: 9px 0 !important;
        border: none !important;
    }
    .pay-summary td {
        font-size: 13px;
        font-weight: 600;
        padding: 9px 0 !important;
        border: none !important;
        text-align: right;
        font-variant-numeric: tabular-nums;
        color: var(--ink-900);
    }

    /* ============================================================
       PAID (disabled) button — soft neutral, not flat white
       ============================================================ */
    .btn-paid-disabled {
        background: var(--ink-50) !important;
        color: var(--ink-500) !important;
        border: 1px solid var(--border) !important;
        cursor: default;
    }
    .btn-paid-disabled i { color: var(--success); }

    /* ============================================================
       ALERTS — accent border, tinted bg
       ============================================================ */
    .alert-success {
        background: var(--success-50);
        color: #065f46;
        border: none;
        border-left: 3px solid var(--success);
        border-radius: var(--radius-sm);
    }
    .alert-danger {
        background: var(--danger-50);
        color: #991b1b;
        border: none;
        border-left: 3px solid var(--danger);
        border-radius: var(--radius-sm);
    }
    .alert .close { opacity: 0.5; }
    .alert .close:hover { opacity: 1; }

    /* ============================================================
       RESPONSIVE + PRINT
       ============================================================ */
    @media (max-width: 991px) {
        .stat-card { padding: 18px; }
        .stat-card .value { font-size: 21px; }
        .stat-card .icon { width: 42px; height: 42px; font-size: 18px; }
    }
    @media (max-width: 767px) {
        .panel-custom .panel-heading { padding: 18px; }
        .filter-form .col-md-3,
        .filter-form .col-md-2 { margin-bottom: 10px; }
    }
    @media print { .no-print { display: none !important; } }

    .btn-danger {
    background: var(--danger) !important;
    border-color: var(--danger) !important;
    color: #fff !important;
}
.btn-danger:hover, .btn-danger:focus {
    background: #a52f22 !important;
    border-color: #a52f22 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(192, 57, 43, 0.25);
}

/* ============================================================
   PAID (completed) indicator — success-tinted status pill
   ============================================================ */
.btn-paid-disabled {
    background: var(--success-50) !important;
    color: var(--success) !important;
    border: 1px solid var(--success-200) !important;
    cursor: default;
    font-weight: 600;
    box-shadow: none !important;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.btn-paid-disabled i {
    color: var(--success);
    font-size: 11px;
}
.btn-paid-disabled:hover {
    /* keep static — it's a status, not an action */
    background: var(--success-50) !important;
    color: var(--success) !important;
    border-color: var(--success-200) !important;
    transform: none !important;
    box-shadow: none !important;
}

/* ============================================================
   VIEW action — refined ghost/outline with brand fill on hover
   ============================================================ */
.btn-view-action {
    background: #fff;
    color: var(--brand-700);
    border: 1px solid var(--brand-200);
    font-weight: 500;
    box-shadow: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.18s var(--ease);
}
.btn-view-action:hover,
.btn-view-action:focus {
    background: var(--brand-700);
    color: #fff;
    border-color: var(--brand-700);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(23, 45, 73, 0.22);
    outline: none;
    text-decoration: none;
}
.btn-view-action i {
    font-size: 11px;
}

/* ============================================================
   Actions cell — even spacing
   ============================================================ */
#datatable tbody td.actions-cell {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
}
#datatable tbody td.actions-cell > * {
    margin: 0 !important;
}
#datatable tbody td.actions-cell form {
    margin: 0 !important;
    display: inline-flex;
}
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float:right">
            <a class="step completed" href="{{ url('home') }}">Home</a>
            <a class="step" href="{{ url('/accounting/debts') }}"> Debtors </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="row"><div class="col-lg-12">
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('success') }}
        </div>
    </div></div>
@endif

@if($errors->any())
    <div class="row"><div class="col-lg-12">
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <ul class="m-b-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    </div></div>
@endif

{{-- Summary cards --}}
<div class="row summary-row">
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">Total Debts</div>
                <div class="value">{{ number_format($summary['total'], 2) }}</div>
            </div>
            <div class="icon"><i class="fa fa-list-alt"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">Total Paid</div>
                <div class="value">{{ number_format($summary['paid'], 2) }}</div>
            </div>
            <div class="icon"><i class="fa fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="label">Total Outstanding</div>
                <div class="value">{{ number_format($summary['unpaid'], 2) }}</div>
            </div>
            <div class="icon"><i class="fa fa-exclamation-circle"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <div class="no-print" style="float:right">
                    <button type="button" id="pdfReportBtn"
                            class="btn btn-default btn-rounded waves-effect">
                        <i class="fa fa-file-pdf-o"></i> Download Report
                    </button>
                    <!-- <button type="button" id="allStatementBtn"
                            class="btn btn-default btn-rounded waves-effect">
                        <i class="fa fa-file-text-o"></i> All Statement
                    </button> -->
                    <button type="button" class="btn btn-primary btn-rounded waves-effect"
                            data-toggle="modal" data-target="#addDebtModal">
                        <i class="fa fa-plus"></i> Add Debt
                    </button>
                </div>
                <h3 class="panel-title1"> Debtors </h3>
                <br/>

                <form method="GET" action="{{ route('debts.index') }}"
                      id="filterForm" class="row no-print filter-form"
                      style="margin-bottom:14px;">
                    <div class="col-md-3">
                        <label>Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            @foreach(['paid' => 'Fully Paid', 'partial' => 'Partially Paid', 'unpaid' => 'Not Paid', 'outstanding' => 'Outstanding'] as $k => $v)
                                <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Month</label>
                        <select name="month" class="form-control">
                            <option value="">All</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Year</label>
                        <select name="year" class="form-control">
                            <option value="">All</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   value="{{ request('search') }}" placeholder="Particulars...">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                <a href="{{ route('debts.index') }}" class="btn btn-default"><i class="fa fa-refresh"></i></a>
                            </span>
                        </div>
                    </div>
                </form>

                <div class="table">
                    <table id="datatable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Particulars</th>
                                <th>Category</th>
                                <th>Period</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Unpaid</th>
                                <th>Status</th>
                                <th>Recorded By</th>
                                <th class="no-print" width="220px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($debts as $i => $d)
                            @php
                                $badge = ['Paid' => 'badge-paid', 'Partial' => 'badge-partial', 'Unpaid' => 'badge-unpaid'][$d->status] ?? 'badge-unpaid';
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ strtoupper($d->particulars) }}</strong></td>
                                <td>{{ $d->category->name ?? '-' }}</td>
                                <td>{{ $d->period }}</td>
                                <td>{{ number_format($d->amount, 2) }}</td>
                                <td class="text-success">{{ number_format($d->paid_amount, 2) }}</td>
                                <td class="text-danger">{{ number_format($d->unpaid_amount, 2) }}</td>
                                <td><span class="status-badge {{ $badge }}">{{ $d->status }}</span></td>
                                <td>
                                 @if($d->user)
                                                {{ $d->user->fname }} {{ strtoupper(substr($d->user->lname ?? '', 0, 1)) }}.
                                            @else
                                                —
                                            @endif   
                            </td>
                                <td class="no-print">
                                    @if($d->unpaid_amount > 0)
                                        <button type="button"
                                                class="btn btn-sm btn-primary btn-rounded addPayment"
                                                data-id="{{ $d->id }}"
                                                data-particulars="{{ $d->particulars }}"
                                                data-category="{{ $d->category->name ?? '-' }}"
                                                data-period="{{ $d->period }}"
                                                data-amount="{{ number_format($d->amount, 2, '.', '') }}"
                                                data-paid="{{ number_format($d->paid_amount, 2, '.', '') }}"
                                                data-unpaid="{{ number_format($d->unpaid_amount, 2, '.', '') }}">
                                            <i class="fa fa-plus"></i> Pay
                                        </button>
                                    @else
                                        <button type="button"
                                                class="btn btn-sm btn-rounded btn-paid-disabled" disabled>
                                            <i class="fa fa-check"></i> Paid
                                        </button>
                                    @endif
                                    <a href="{{ route('debts.statement', $d->id) }}"
                                       class="btn btn-sm btn-default btn-rounded">
                                        <i class="fa fa-file-text-o"></i> View
                                    </a>
                                      @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                                    <form action="{{ route('debts.destroy', $d->id) }}" method="POST" style="display:inline-block;"
                                    onsubmit="return confirm('Delete this debt? This will also remove its payment history.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-rounded">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                                @else 
                                @endif
                                </td>
                            </tr>
                        @empty
                            {{-- No row output here — DataTables will show its own empty message --}}
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= ADD DEBT MODAL ================= --}}
<div class="modal fade" id="addDebtModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <form action="{{ route('debts.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Add Debt</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">Category <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="input-group-btn">
                                <button type="button" id="openCategoryModal" class="btn btn-primary"
                                        title="Add new category">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Particulars <span class="text-danger">*</span></label>
                        <input type="text" name="particulars" class="form-control"
                               value="{{ old('particulars') }}" placeholder="e.g. NHIF contributions - July" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                               value="{{ old('amount') }}" placeholder="0.00" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Month <span class="text-danger">*</span></label>
                                <select name="month" class="form-control" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}"
                                            {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Year <span class="text-danger">*</span></label>
                                <select name="year" class="form-control" required>
                                    @foreach(range(date('Y') + 1, 2020) as $y)
                                        <option value="{{ $y }}"
                                            {{ old('year', date('Y')) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-rounded" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-rounded">Save Debt</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= ADD CATEGORY MODAL ================= --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Add Category</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" id="cat_name" class="form-control" placeholder="e.g. TRA">
                </div>
                <div class="form-group">
                    <label class="control-label">Description</label>
                    <input type="text" id="cat_desc" class="form-control" placeholder="Optional">
                </div>
                <div id="cat_error" class="text-danger small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-rounded" data-dismiss="modal">Cancel</button>
                <button type="button" id="saveCategory" class="btn btn-primary btn-rounded">Save Category</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= PAYMENT MODAL ================= --}}
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <form id="paymentForm" method="POST" action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Record Payment</h4>
                </div>
                <div class="modal-body">
                    <table class="table pay-summary">
                        <tr><th>Particulars</th><td id="pay_particulars"></td></tr>
                        <tr><th>Category</th><td id="pay_category"></td></tr>
                        <tr><th>Period</th><td id="pay_period"></td></tr>
                        <tr><th>Amount</th><td id="pay_amount"></td></tr>
                        <tr><th>Paid</th><td id="pay_paid" class="text-success"></td></tr>
                        <tr><th>Outstanding Balance</th><td id="pay_unpaid" class="text-danger"></td></tr>
                    </table>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Amount to Pay <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="amount" id="amount"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Payment Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="paid_date" id="paid_date" class="form-control datepicker"
                                           autocomplete="off" required value="{{ date('Y-m-d') }}">
                                    <span class="input-group-addon bg-info b-0 text-white"><i class="ti-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Notes / Reference</label>
                        <input type="text" name="notes" class="form-control" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-rounded" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-rounded">Save Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    /* -------- DataTable -------- */
    if ($.fn.DataTable) {
        var table = $('#datatable').DataTable({
            order: [],
            pageLength: 25,
            language: {
                emptyTable: "No debts found.",
                zeroRecords: "No matching records found."
            }
        });
    }

    if ($.fn.datepicker) {
        $('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'yyyy-mm-dd' });
    }

    @if($errors->any())
        $('#addDebtModal').modal('show');
    @endif

    function numberFormat(n) {
        return parseFloat(n || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }

    /* -------- Filter-aware buttons -------- */
    function buildFilterQuery() {
        var params = {};
        $('#filterForm').find('input, select').each(function () {
            if (this.name && this.value !== '') {
                params[this.name] = this.value;
            }
        });
        return $.param(params);
    }

    function openWithFilters(baseUrl, newTab) {
        var q   = buildFilterQuery();
        var url = q ? baseUrl + '?' + q : baseUrl;

        if (newTab) {
            window.open(url, '_blank');
        } else {
            window.location.href = url;
        }
    }

    $('#pdfReportBtn').on('click', function (e) {
        e.preventDefault();
        openWithFilters("{{ route('debts.statement.all.pdf') }}", true);
    });

    $('#allStatementBtn').on('click', function (e) {
        e.preventDefault();
        openWithFilters("{{ route('debts.statement.all') }}", false);
    });

    /* -------- Payment modal -------- */
    $(document).on('click', '.addPayment', function () {
        var b      = $(this);
        var unpaid = parseFloat(b.data('unpaid')) || 0;

        var payUrl = "{{ route('debts.pay', ':id') }}".replace(':id', b.data('id'));
        $('#paymentForm').attr('action', payUrl);

        $('#pay_particulars').text(b.data('particulars'));
        $('#pay_category').text(b.data('category'));
        $('#pay_period').text(b.data('period'));
        $('#pay_amount').text(numberFormat(b.data('amount')));
        $('#pay_paid').text(numberFormat(b.data('paid')));
        $('#pay_unpaid').text(numberFormat(unpaid));
        $('#amount').val(unpaid.toFixed(2)).attr('max', unpaid);

        $('#paymentModal').modal('show');
    });

    /* -------- Add category flow -------- */
    var reopenDebt = false;

    $('#openCategoryModal').on('click', function () {
        reopenDebt = true;
        $('#addDebtModal').modal('hide');
        $('#addCategoryModal').modal('show');
    });

    $('#addCategoryModal').on('hidden.bs.modal', function () {
        if (reopenDebt) {
            reopenDebt = false;
            setTimeout(function () { $('#addDebtModal').modal('show'); }, 200);
        }
    });

    $('#saveCategory').on('click', function () {
        var btn = $(this).prop('disabled', true);

        $.ajax({
            url: "{{ route('debts.categories.store') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: $('#cat_name').val(),
                description: $('#cat_desc').val()
            },
            success: function (res) {
                $('#category_id').append(new Option(res.category.name, res.category.id, true, true));
                $('#cat_name, #cat_desc').val('');
                $('#cat_error').text('');
                $('#addCategoryModal').modal('hide');
            },
            error: function (xhr) {
                var msg = 'Unable to save category.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors)[0][0];
                }
                $('#cat_error').text(msg);
            },
            complete: function () { btn.prop('disabled', false); }
        });
    });
});
</script>
@endsection