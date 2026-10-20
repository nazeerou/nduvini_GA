@extends('layouts.app_header')
@section('content')

<style>
    /* ===================== Client Payments – Professional UI ===================== */
    :root{
        --cp-primary:#1f3a5f;
        --cp-primary-dark:#152a45;
        --cp-ink:#111827;
        --cp-ink-soft:#374151;
        --cp-muted:#6b7280;
        --cp-line:#e5e7eb;
        --cp-line-soft:#f3f4f6;
        --cp-soft:#f9fafb;
        --cp-white:#ffffff;
        --cp-paid:#166534;
        --cp-paid-bg:#dcfce7;
        --cp-unpaid:#92400e;
        --cp-unpaid-bg:#fef3c7;
        --cp-overpaid:#065f46;
        --cp-overpaid-bg:#d1fae5;
    }

    .cp-wrap *{box-sizing:border-box;}
    .cp-wrap .panel-custom{
        background:var(--cp-white);border:1px solid var(--cp-line);
        border-radius:12px;overflow:hidden;
        box-shadow:0 1px 3px rgba(17,24,39,.05), 0 1px 2px rgba(17,24,39,.03);
        border-top:none;
    }

    /* ---------- Step indicator ---------- */
    .cp-steps{display:flex;justify-content:flex-end;align-items:center;gap:8px;margin-bottom:20px;}
    .cp-steps a{
        font-size:12px;font-weight:500;letter-spacing:.02em;padding:7px 14px;border-radius:8px;
        background:var(--cp-white);color:var(--cp-muted);text-decoration:none;
        border:1px solid var(--cp-line);transition:all .2s ease;
    }
    .cp-steps a:hover{background:var(--cp-line-soft);color:var(--cp-ink);text-decoration:none;}
    .cp-steps a.completed{background:var(--cp-white);color:var(--cp-ink-soft);}
    .cp-steps a.active{
        background:var(--cp-primary);color:var(--cp-white);border-color:var(--cp-primary);
        box-shadow:0 2px 4px rgba(31,58,95,.12);
    }
    .cp-steps .cp-sep{color:#d1d5db;font-size:11px;}

    /* ---------- Card header ---------- */
    .cp-head{
        display:flex;justify-content:space-between;align-items:flex-start;gap:20px;
        padding:24px 28px;border-bottom:1px solid var(--cp-line);background:var(--cp-white);
    }
    .cp-head-text{min-width:0;}
    .cp-head-text h3{
        margin:0 0 6px;font-size:19px;font-weight:700;color:var(--cp-ink);letter-spacing:-.2px;
    }
    .cp-head-text h3 i{color:var(--cp-primary);margin-right:8px;}
    .cp-head-text p{margin:0;font-size:13px;color:var(--cp-muted);line-height:1.5;}

    /* ---------- Buttons ---------- */
    .cp-btn-primary{
        display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border:none;
        border-radius:8px;font-size:13px;font-weight:600;letter-spacing:.01em;cursor:pointer;
        color:var(--cp-white);background:var(--cp-primary);
        box-shadow:0 1px 2px rgba(31,58,95,.15);transition:all .18s ease;
    }
    .cp-btn-primary:hover{background:var(--cp-primary-dark);transform:translateY(-1px);color:#fff;}
    .cp-btn-primary i{font-size:12px;}

    /* ---------- Summary KPI cards ---------- */
    .cp-summary{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:14px;
        padding:22px 28px 10px;
    }
    @media (max-width:1199px){ .cp-summary{grid-template-columns:repeat(2,1fr);} }
    @media (max-width:575px){ .cp-summary{grid-template-columns:1fr;} }

    .cp-kpi{
        position:relative;
        background:var(--cp-white);
        border:1px solid var(--cp-line);
        border-radius:10px;
        padding:16px 18px 16px 22px;
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:12px;
        overflow:hidden;
        transition:border-color .18s ease, box-shadow .18s ease;
    }
    .cp-kpi:hover{border-color:#d1d5db;box-shadow:0 1px 3px rgba(17,24,39,.06);}
    .cp-kpi::before{
        content:"";position:absolute;left:0;top:0;bottom:0;width:3px;
        background:var(--cp-primary);
    }
    .cp-kpi.kpi-paid::before{background:var(--cp-paid);}
    .cp-kpi.kpi-unpaid::before{background:#b45309;}
    .cp-kpi.kpi-count::before{background:var(--cp-muted);}

    .cp-kpi-body{min-width:0;flex:1;}
    .cp-kpi-label{
        font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;
        color:var(--cp-muted);margin:0 0 6px;
    }
    .cp-kpi-value{
        font-family:'SFMono-Regular',Consolas,monospace;
        font-size:19px;font-weight:700;color:var(--cp-ink);line-height:1.1;
        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
    }
    .cp-kpi-sub{
        font-size:11.5px;color:var(--cp-muted);margin-top:6px;line-height:1.3;
    }
    .cp-kpi-icon{
        width:36px;height:36px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;
        border-radius:8px;background:var(--cp-soft);color:var(--cp-primary);
        font-size:15px;
    }
    .cp-kpi.kpi-paid .cp-kpi-icon{background:var(--cp-paid-bg);color:var(--cp-paid);}
    .cp-kpi.kpi-unpaid .cp-kpi-icon{background:var(--cp-unpaid-bg);color:#b45309;}
    .cp-kpi.kpi-count .cp-kpi-icon{background:var(--cp-line-soft);color:var(--cp-ink-soft);}

    .cp-kpi.kpi-paid .cp-kpi-value{color:var(--cp-paid);}
    .cp-kpi.kpi-unpaid .cp-kpi-value{color:#b45309;}

    .cp-kpi-progress{
        position:relative;height:4px;background:var(--cp-line);
        border-radius:999px;margin-top:10px;overflow:hidden;
    }
    .cp-kpi-progress > span{
        display:block;height:100%;background:var(--cp-paid);border-radius:999px;
        transition:width .4s ease;
    }

    /* ---------- Table ---------- */
    .cp-body{padding:0 0 8px;}
    .cp-table-wrap{width:100%;overflow-x:auto;padding:14px 28px 0;}
    .cp-wrap #datatable{
        width:100%!important;border-collapse:collapse;margin:0;font-size:13px;
        border:1px solid var(--cp-line);border-radius:0;
    }
    .cp-wrap #datatable thead th{
        background:var(--cp-primary);color:#fff;
        font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;
        padding:14px 14px;text-align:left;border:none;white-space:nowrap;
        vertical-align:middle;
    }
    .cp-wrap #datatable thead th:last-child{text-align:center;}
    .cp-wrap #datatable tbody td{
        padding:13px 14px;border-bottom:1px solid var(--cp-line);
        vertical-align:middle;color:var(--cp-ink-soft);
    }
    .cp-wrap #datatable tbody tr:last-child td{border-bottom:none;}
    .cp-wrap #datatable tbody tr:hover td{background:var(--cp-soft);}
    .cp-wrap #datatable tbody td.num{
        font-family:'SFMono-Regular', Consolas, monospace;
        font-size:12.5px;white-space:nowrap;text-align:right;
    }
    .cp-wrap #datatable tbody td.center{text-align:center;}

    /* Amount emphasis */
    .cp-amount{font-weight:600;color:var(--cp-ink);}
    .cp-unpaid{color:var(--cp-unpaid);font-weight:600;}
    .cp-zero{color:var(--cp-muted);font-weight:500;}
    .cp-balance{color:var(--cp-primary);font-weight:700;}
    .cp-overpaid-txt{color:var(--cp-overpaid);font-weight:700;}

    /* Status pills */
    .cp-pill{
        display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:999px;
        font-size:11.5px;font-weight:600;line-height:1.4;
    }
    .cp-pill-paid{background:var(--cp-paid-bg);color:var(--cp-paid);}
    .cp-pill-unpaid{background:var(--cp-unpaid-bg);color:var(--cp-unpaid);}
    .cp-pill i{font-size:10px;}

    /* ---------- Action buttons ---------- */
    .cp-actions{display:flex;gap:6px;justify-content:center;flex-wrap:nowrap;}
    .cp-act{
        display:inline-flex;align-items:center;gap:5px;padding:6px 11px;border-radius:6px;
        font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;border:1px solid transparent;
        transition:all .15s ease;white-space:nowrap;
    }
    .cp-act i{font-size:11px;}
    .cp-act:hover{text-decoration:none;transform:translateY(-1px);}

    .cp-act-pay{background:var(--cp-primary);color:#fff;border-color:var(--cp-primary);}
    .cp-act-pay:hover{background:var(--cp-primary-dark);color:#fff;}

    .cp-act-view{background:var(--cp-white);color:var(--cp-ink-soft);border-color:var(--cp-line);}
    .cp-act-view:hover{background:var(--cp-soft);color:var(--cp-ink);border-color:#d1d5db;}

    .cp-act-paid{background:var(--cp-paid-bg);color:var(--cp-paid);border-color:transparent;cursor:default;}
    .cp-act-paid:hover{transform:none;}

    /* ---------- Modals ---------- */
    .cp-wrap .modal-content, .cp-modal .modal-content{
        border:none;border-radius:14px;overflow:hidden;
        box-shadow:0 24px 60px rgba(17,24,39,.20);
    }
    .cp-wrap .modal-header, .cp-modal .modal-header{
        background:var(--cp-white);border-bottom:1px solid var(--cp-line);
        padding:20px 24px;
    }
    .cp-wrap .modal-header h4, .cp-modal .modal-header h4{
        margin:0;font-size:16px;font-weight:700;color:var(--cp-ink);
    }
    .cp-wrap .modal-header .close, .cp-modal .modal-header .close{
        font-size:24px;color:var(--cp-muted);opacity:.7;margin-top:-2px;
    }
    .cp-wrap .modal-header .close:hover, .cp-modal .modal-header .close:hover{opacity:1;color:var(--cp-ink);}

    .cp-wrap .modal-body, .cp-modal .modal-body{padding:24px;background:var(--cp-white);}

    .cp-wrap .modal-footer, .cp-modal .modal-footer{
        background:var(--cp-soft);border-top:1px solid var(--cp-line);
        padding:14px 24px;
    }

    .cp-wrap .form-group, .cp-modal .form-group{margin-bottom:18px;}
    .cp-wrap .control-label, .cp-modal .control-label{
        display:block;margin-bottom:7px;font-size:12px;font-weight:600;
        color:var(--cp-ink-soft);letter-spacing:.02em;
    }
    .cp-wrap .control-label .req, .cp-modal .control-label .req{color:#dc2626;font-weight:700;}

    .cp-wrap .form-control, .cp-modal .form-control{
        width:100%;height:42px;padding:0 12px;font-size:14px;color:var(--cp-ink);
        background:var(--cp-white);border:1px solid var(--cp-line);border-radius:8px;
        outline:none;box-shadow:none;transition:all .18s ease;
    }
    .cp-wrap select.form-control, .cp-modal select.form-control{
        height:42px;padding:0 34px 0 12px;
    }
    .cp-wrap .form-control:hover, .cp-modal .form-control:hover{border-color:#d1d5db;}
    .cp-wrap .form-control:focus, .cp-modal .form-control:focus{
        border-color:var(--cp-primary);box-shadow:0 0 0 3px rgba(31,58,95,.10);
    }
    .cp-wrap .form-control[readonly], .cp-modal .form-control[readonly]{
        background:var(--cp-soft);color:var(--cp-muted);cursor:not-allowed;
    }

    .cp-wrap .input-group-addon, .cp-modal .input-group-addon{
        background:var(--cp-primary)!important;color:#fff!important;border:none;
        border-radius:0 8px 8px 0;
    }

    /* Footer buttons */
    .cp-wrap .btn-default, .cp-modal .btn-default{
        background:var(--cp-white);border:1px solid var(--cp-line);color:var(--cp-ink-soft);
        border-radius:8px;padding:9px 18px;font-weight:600;font-size:13px;
    }
    .cp-wrap .btn-default:hover, .cp-modal .btn-default:hover{
        background:var(--cp-line-soft);color:var(--cp-ink);border-color:#d1d5db;
    }
    .cp-wrap .btn-primary, .cp-modal .btn-primary{
        background:var(--cp-primary);border-color:var(--cp-primary);color:#fff;
        border-radius:8px;padding:9px 20px;font-weight:600;font-size:13px;
        box-shadow:0 1px 2px rgba(31,58,95,.15);
    }
    .cp-wrap .btn-primary:hover, .cp-modal .btn-primary:hover{
        background:var(--cp-primary-dark);border-color:var(--cp-primary-dark);
    }
    .cp-wrap .btn-rounded, .cp-modal .btn-rounded{border-radius:8px;}

    /* Section divider inside modals */
    .cp-section-title{
        font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
        color:var(--cp-muted);margin:0 0 14px;padding-bottom:8px;
        border-bottom:1px solid var(--cp-line);
    }

    @media (max-width:767px){
        .cp-head{flex-direction:column;align-items:stretch;padding:20px;}
        .cp-head-text h3{font-size:17px;}
        .cp-actions{flex-wrap:wrap;}
        .cp-summary{padding:18px 20px 4px;}
        .cp-table-wrap{padding:14px 20px 0;}
    }
</style>

<div class="cp-wrap">

    {{-- ===== Step indicator ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="cp-steps">
                <a href="{{ url('home') }}" class="completed"><i class="fa fa-home"></i> Home</a>
                <span class="cp-sep"><i class="fa fa-angle-right"></i></span>
                <a href="#" class="active">Client Payments</a>
            </div>
        </div>
    </div>

    {{-- ===== Main panel ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-custom panel-border">

                <div class="cp-head">
                    <div class="cp-head-text">
                        <h3><i class="fa fa-credit-card"></i> Client Payments</h3>
                        <p>Manage client invoices, payments and outstanding balances.</p>
                    </div>
                    <div>
                        <button type="button"
                                class="cp-btn-primary"
                                data-toggle="modal"
                                data-target=".bs-example-modal-lg">
                            <i class="fa fa-plus"></i> Add Debit
                        </button>
                    </div>
                </div>

                <div class="cp-body">

                    @php
                        $totalInvoiced = (float) $sales->sum(fn($p) => (float) $p->bill_amount);
                        $totalPaid     = (float) $sales->sum(fn($p) => (float) $p->paid_amount);
                        $totalUnpaid   = (float) $sales->sum(fn($p) => max(0, (float) $p->bill_amount - (float) $p->paid_amount));

                        $withBalance   = $sales->filter(fn($p) => (float) $p->bill_amount - (float) $p->paid_amount > 0)->count();
                        $fullyPaid     = $sales->filter(fn($p) => (float) $p->bill_amount - (float) $p->paid_amount <= 0)->count();
                        $invoiceCount  = $sales->count();

                        $paidPct = $totalInvoiced > 0
                            ? min(100, round(($totalPaid / $totalInvoiced) * 100, 1))
                            : 0;

                        $invLabel = $invoiceCount === 1 ? 'invoice' : 'invoices';
                        $balLabel = $withBalance === 1 ? 'invoice' : 'invoices';
                    @endphp

                    {{-- ===== Summary KPI cards ===== --}}
                    <div class="cp-summary">

                        <div class="cp-kpi">
                            <div class="cp-kpi-body">
                                <p class="cp-kpi-label">Total Invoiced</p>
                                <div class="cp-kpi-value">{{ number_format($totalInvoiced, 2) }}</div>
                                <div class="cp-kpi-sub">{{ $invoiceCount }} {{ $invLabel }}</div>
                            </div>
                            <div class="cp-kpi-icon"><i class="fa fa-file-text-o"></i></div>
                        </div>

                        <div class="cp-kpi kpi-paid">
                            <div class="cp-kpi-body">
                                <p class="cp-kpi-label">Total Paid</p>
                                <div class="cp-kpi-value">{{ number_format($totalPaid, 2) }}</div>
                                <div class="cp-kpi-progress" title="{{ $paidPct }}% collected">
                                    <span style="width: {{ $paidPct }}%;"></span>
                                </div>
                                <div class="cp-kpi-sub">{{ $paidPct }}% collected · {{ $fullyPaid }} settled</div>
                            </div>
                            <div class="cp-kpi-icon"><i class="fa fa-check"></i></div>
                        </div>

                        <div class="cp-kpi kpi-unpaid">
                            <div class="cp-kpi-body">
                                <p class="cp-kpi-label">Total Unpaid</p>
                                <div class="cp-kpi-value">{{ number_format($totalUnpaid, 2) }}</div>
                                <div class="cp-kpi-sub">{{ $withBalance }} {{ $balLabel }} with balance</div>
                            </div>
                            <div class="cp-kpi-icon"><i class="fa fa-exclamation-triangle"></i></div>
                        </div>

                        <div class="cp-kpi kpi-count">
                            <div class="cp-kpi-body">
                                <p class="cp-kpi-label">Invoices</p>
                                <div class="cp-kpi-value">{{ $invoiceCount }}</div>
                                <div class="cp-kpi-sub">{{ $fullyPaid }} paid · {{ $withBalance }} pending</div>
                            </div>
                            <div class="cp-kpi-icon"><i class="fa fa-list-alt"></i></div>
                        </div>

                    </div>

                    {{-- ===== Table ===== --}}
                    <div class="cp-table-wrap">
                        <table id="datatable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:48px;">#</th>
                                    <th>Client Name</th>
                                    <th>Vehicle No</th>
                                    <th>Invoice No</th>
                                    <th class="text-right">Invoice Amount</th>
                                    <th>Created Date</th>
                                    <th class="text-right">Paid Amount</th>
                                    <th class="text-right">Unpaid Amount</th>
                                    <th class="text-right">Balance</th>
                                    <th style="width:180px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $key => $product)
                                    <?php $check = $product->bill_amount - $product->paid_amount; ?>
                                    <tr>
                                        <td class="center">{{ $key + 1 }}</td>
                                        <td><strong style="color:var(--cp-ink);">{{ strtoupper($product->client_name) }}</strong></td>
                                        <td>{{ strtoupper($product->vehicle_reg) }}</td>
                                        <td>
                                            <span style="font-family:'SFMono-Regular',Consolas,monospace;font-size:12.5px;">
                                                {{ $product->invoice_number }}
                                            </span>
                                        </td>
                                        <td class="num">
                                            <span class="cp-amount">{{ number_format($product->bill_amount, 2) }}</span>
                                        </td>
                                        <td>{{ strtoupper($product->created_date) }}</td>
                                        <td class="num">
                                            <span class="cp-amount">{{ number_format($product->paid_amount, 2) }}</span>
                                        </td>
                                        <td class="num">
                                            @if($product->bill_amount > $product->paid_amount)
                                                <span class="cp-unpaid">{{ number_format($product->bill_amount - $product->paid_amount, 2) }}</span>
                                            @else
                                                <span class="cp-zero">0.00</span>
                                            @endif
                                        </td>
                                        <td class="num">
                                            @if($product->bill_amount - $product->paid_amount > 0)
                                                <span class="cp-balance">{{ number_format($product->bill_amount - $product->paid_amount, 2) }}</span>
                                            @elseif($product->bill_amount - $product->paid_amount < 0)
                                                <span class="cp-overpaid-txt">{{ number_format($product->bill_amount - $product->paid_amount, 2) }}</span>
                                            @else
                                                <span class="cp-zero">0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="cp-actions">
                                                @if ($check == 0)
                                                    <button type="button"
                                                            value="{{ $product->invoice_number }}"
                                                            class="cp-act cp-act-paid">
                                                        <i class="fa fa-check"></i> Paid
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            value="{{ $product->invoice_number }}"
                                                            class="cp-act cp-act-pay addPayment">
                                                        <i class="fa fa-plus"></i> Pay
                                                    </button>
                                                @endif

                                                @if(!$product->invoice_number)
                                                    <a class="cp-act cp-act-view viewModal">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                @else
                                                    <a href="{{ url('clients/payments/view-payments/'.$product->invoice_number) }}"
                                                       class="cp-act cp-act-view">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== Payment Modal ===================== --}}
<div class="modal fade bs-example-modal-md cp-modal" tabindex="-1" role="dialog" aria-hidden="true" id="payment_modal">
    <div class="modal-dialog modal-md">
        <form id="formUpdate" method="post" action="{{ url('clients/add-payments') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Client Payment</h4>
                </div>

                <div class="modal-body">

                    <div class="cp-section-title">Invoice details</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoice Number</label>
                                <input type="hidden" name="client_id" readonly id="client_id">
                                <input type="text" name="invoice_number" readonly id="invoice_no" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoiced Amount</label>
                                <input type="text" name="bill_amount" readonly id="bill_amount" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="cp-section-title" style="margin-top:8px;">Payment details</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Amount <span class="req">*</span></label>
                                <input type="text" name="paid_amount" id="paid_amount" class="form-control" placeholder="Amount to pay">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Date <span class="req">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control created_date" required autocomplete="off"
                                           name="created_date" placeholder="Payment date"
                                           id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                                    <span class="input-group-addon"><i class="ti-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Pay</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===================== Add Debit Modal ===================== --}}
<div class="modal fade bs-example-modal-lg cp-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ url('clients/add-clients-debit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Add Client Debit</h4>
                </div>

                <div class="modal-body">

                    <div class="cp-section-title">Client &amp; Vehicle</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Client Name <span class="req">*</span></label>
                                <select name="client_id" class="form-control select2" id="client_id" required>
                                    <option>Select Client</option>
                                    @foreach($clients as $s)
                                        <option value="{{ $s->id }}">{{ $s->client_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Vehicle Reg <span class="req">*</span></label>
                                <input type="text" name="vehicle_reg" id="vehicle_reg" class="form-control" placeholder="Vehicle Reg Number" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Make &amp; Model <span class="req">*</span></label>
                                <input type="text" name="make" id="make" class="form-control" placeholder="e.g. TOYOTA PRADO" required>
                            </div>
                        </div>
                    </div>

                    <div class="cp-section-title" style="margin-top:8px;">Invoice details</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoice No. <span class="req">*</span></label>
                                <input type="text" name="invoice_no" id="invoice" class="form-control" placeholder="Invoice No." required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoice Amount <span class="req">*</span></label>
                                <input type="text" name="invoice_amount" id="invoice_amount" class="form-control" placeholder="Invoice amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoice Date <span class="req">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control created_date" required autocomplete="off"
                                           name="created_date" placeholder="Invoice date"
                                           id="datepicker1" data-date-format="yyyy-mm-dd">
                                    <span class="input-group-addon"><i class="ti-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery('#datepicker1').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    $('.addPayment').click(function() {
         var invoice_no = $(this).val();
         $.ajax({
             url: `../clients/payments-details/${invoice_no}`,
             type: "GET",
             success: function (response) {
                 $('#payment_modal').modal('show');
                 $("#invoice_no").val(response[0].invoice_number);
                 $("#bill_amount").val(response[0].bill_amount);
                 $("#client_id").val(response[0].client_id);
               }
           });
       });

       $('.viewModal').click(function() {
               alert('Bill number not Created');
       });
  });
</script>

@endsection