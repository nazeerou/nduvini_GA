@extends('layouts.app_header')

@section('content')

<style>
    /* ===================== Supplier Payments – Professional UI ===================== */
    :root{
        --sp-primary:#1f3a5f;
        --sp-primary-dark:#152a45;
        --sp-ink:#111827;
        --sp-ink-soft:#374151;
        --sp-muted:#6b7280;
        --sp-line:#e5e7eb;
        --sp-line-soft:#f3f4f6;
        --sp-soft:#f9fafb;
        --sp-white:#ffffff;
        --sp-paid:#166534;
        --sp-paid-bg:#dcfce7;
        --sp-unpaid:#92400e;
        --sp-unpaid-bg:#fef3c7;
        --sp-overpaid:#065f46;
    }

    .sp-wrap *{box-sizing:border-box;}
    .sp-wrap .panel-custom{
        background:var(--sp-white);border:1px solid var(--sp-line);
        border-radius:12px;overflow:hidden;
        box-shadow:0 1px 3px rgba(17,24,39,.05), 0 1px 2px rgba(17,24,39,.03);
        border-top:none;
    }

    /* ---------- Step indicator ---------- */
    .sp-steps{display:flex;justify-content:flex-end;align-items:center;gap:8px;margin-bottom:20px;}
    .sp-steps a{
        font-size:12px;font-weight:500;letter-spacing:.02em;padding:7px 14px;border-radius:8px;
        background:var(--sp-white);color:var(--sp-muted);text-decoration:none;
        border:1px solid var(--sp-line);transition:all .2s ease;
    }
    .sp-steps a:hover{background:var(--sp-line-soft);color:var(--sp-ink);text-decoration:none;}
    .sp-steps a.completed{background:var(--sp-white);color:var(--sp-ink-soft);}
    .sp-steps a.active{
        background:var(--sp-primary);color:var(--sp-white);border-color:var(--sp-primary);
        box-shadow:0 2px 4px rgba(31,58,95,.12);
    }
    .sp-steps .sp-sep{color:#d1d5db;font-size:11px;}

    /* ---------- Card header ---------- */
    .sp-head{
        display:flex;justify-content:space-between;align-items:flex-start;gap:20px;
        padding:24px 28px;border-bottom:1px solid var(--sp-line);background:var(--sp-white);
    }
    .sp-head-text{min-width:0;}
    .sp-head-text h3{
        margin:0 0 6px;font-size:19px;font-weight:700;color:var(--sp-ink);letter-spacing:-.2px;
    }
    .sp-head-text h3 i{color:var(--sp-primary);margin-right:8px;}
    .sp-head-text p{margin:0;font-size:13px;color:var(--sp-muted);line-height:1.5;}

    /* ---------- Summary KPI cards ---------- */
    .sp-summary{
        display:grid;
        grid-template-columns:repeat(4,1fr);
        gap:14px;
        padding:22px 28px 10px;
    }
    @media (max-width:1199px){ .sp-summary{grid-template-columns:repeat(2,1fr);} }
    @media (max-width:575px){ .sp-summary{grid-template-columns:1fr;} }

    .sp-kpi{
        position:relative;
        background:var(--sp-white);
        border:1px solid var(--sp-line);
        border-radius:10px;
        padding:16px 18px 16px 22px;
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:12px;
        overflow:hidden;
        transition:border-color .18s ease, box-shadow .18s ease;
    }
    .sp-kpi:hover{border-color:#d1d5db;box-shadow:0 1px 3px rgba(17,24,39,.06);}
    .sp-kpi::before{
        content:"";position:absolute;left:0;top:0;bottom:0;width:3px;
        background:var(--sp-primary);
    }
    .sp-kpi.kpi-paid::before{background:var(--sp-paid);}
    .sp-kpi.kpi-unpaid::before{background:#b45309;}
    .sp-kpi.kpi-count::before{background:var(--sp-muted);}

    .sp-kpi-body{min-width:0;flex:1;}
    .sp-kpi-label{
        font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;
        color:var(--sp-muted);margin:0 0 6px;
    }
    .sp-kpi-value{
        font-family:'SFMono-Regular',Consolas,monospace;
        font-size:19px;font-weight:700;color:var(--sp-ink);line-height:1.1;
        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
    }
    .sp-kpi-sub{
        font-size:11.5px;color:var(--sp-muted);margin-top:6px;line-height:1.3;
    }
    .sp-kpi-icon{
        width:36px;height:36px;flex-shrink:0;
        display:flex;align-items:center;justify-content:center;
        border-radius:8px;background:var(--sp-soft);color:var(--sp-primary);
        font-size:15px;
    }
    .sp-kpi.kpi-paid .sp-kpi-icon{background:var(--sp-paid-bg);color:var(--sp-paid);}
    .sp-kpi.kpi-unpaid .sp-kpi-icon{background:var(--sp-unpaid-bg);color:#b45309;}
    .sp-kpi.kpi-count .sp-kpi-icon{background:var(--sp-line-soft);color:var(--sp-ink-soft);}

    .sp-kpi.kpi-paid .sp-kpi-value{color:var(--sp-paid);}
    .sp-kpi.kpi-unpaid .sp-kpi-value{color:#b45309;}

    .sp-kpi-progress{
        position:relative;height:4px;background:var(--sp-line);
        border-radius:999px;margin-top:10px;overflow:hidden;
    }
    .sp-kpi-progress > span{
        display:block;height:100%;background:var(--sp-paid);border-radius:999px;
        transition:width .4s ease;
    }

    /* ---------- Table ---------- */
    .sp-body{padding:0 0 8px;}
    .sp-table-wrap{width:100%;overflow-x:auto;padding:14px 28px 0;}
    .sp-wrap #datatable{
        width:100%!important;border-collapse:collapse;margin:0;font-size:13px;
        border:1px solid var(--sp-line);border-radius:0;
    }
    .sp-wrap #datatable thead th{
        background:var(--sp-primary);color:#fff;
        font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;
        padding:14px 14px;text-align:left;border:none;white-space:nowrap;
        vertical-align:middle;
    }
    .sp-wrap #datatable thead th:last-child{text-align:center;}
    .sp-wrap #datatable tbody td{
        padding:13px 14px;border-bottom:1px solid var(--sp-line);
        vertical-align:middle;color:var(--sp-ink-soft);
    }
    .sp-wrap #datatable tbody tr:last-child td{border-bottom:none;}
    .sp-wrap #datatable tbody tr:hover td{background:var(--sp-soft);}
    .sp-wrap #datatable tbody td.num{
        font-family:'SFMono-Regular', Consolas, monospace;
        font-size:12.5px;white-space:nowrap;text-align:right;
    }
    .sp-wrap #datatable tbody td.center{text-align:center;}

    /* Amount emphasis */
    .sp-amount{font-weight:600;color:var(--sp-ink);}
    .sp-unpaid{color:var(--sp-unpaid);font-weight:600;}
    .sp-zero{color:var(--sp-muted);font-weight:500;}
    .sp-balance{color:var(--sp-primary);font-weight:700;}
    .sp-overpaid-txt{color:var(--sp-overpaid);font-weight:700;}

    /* Invoice link */
    .sp-invoice-link{
        font-family:'SFMono-Regular',Consolas,monospace;font-size:12.5px;
        color:var(--sp-primary);font-weight:600;text-decoration:none;
        border-bottom:1px dashed transparent;transition:border-color .15s ease;
    }
    .sp-invoice-link:hover{color:var(--sp-primary-dark);text-decoration:none;border-bottom-color:var(--sp-primary);}

    /* ---------- Action buttons ---------- */
    .sp-actions{display:flex;gap:6px;justify-content:center;flex-wrap:nowrap;}
    .sp-act{
        display:inline-flex;align-items:center;gap:5px;padding:6px 11px;border-radius:6px;
        font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;border:1px solid transparent;
        transition:all .15s ease;white-space:nowrap;
    }
    .sp-act i{font-size:11px;}
    .sp-act:hover{text-decoration:none;transform:translateY(-1px);}

    .sp-act-pay{background:var(--sp-primary);color:#fff;border-color:var(--sp-primary);}
    .sp-act-pay:hover{background:var(--sp-primary-dark);color:#fff;}

    .sp-act-view{background:var(--sp-white);color:var(--sp-ink-soft);border-color:var(--sp-line);}
    .sp-act-view:hover{background:var(--sp-soft);color:var(--sp-ink);border-color:#d1d5db;}

    .sp-act-paid{background:var(--sp-paid-bg);color:var(--sp-paid);border-color:transparent;cursor:default;}
    .sp-act-paid:hover{transform:none;}

    /* ---------- Modals ---------- */
    .sp-modal .modal-content{
        border:none;border-radius:14px;overflow:hidden;
        box-shadow:0 24px 60px rgba(17,24,39,.20);
    }
    .sp-modal .modal-header{
        background:var(--sp-white);border-bottom:1px solid var(--sp-line);
        padding:20px 24px;
    }
    .sp-modal .modal-header h4{
        margin:0;font-size:16px;font-weight:700;color:var(--sp-ink);
    }
    .sp-modal .modal-header .close{
        font-size:24px;color:var(--sp-muted);opacity:.7;margin-top:-2px;
    }
    .sp-modal .modal-header .close:hover{opacity:1;color:var(--sp-ink);}

    .sp-modal .modal-body{padding:24px;background:var(--sp-white);}

    .sp-modal .modal-footer{
        background:var(--sp-soft);border-top:1px solid var(--sp-line);
        padding:14px 24px;
    }

    .sp-modal .form-group{margin-bottom:18px;}
    .sp-modal .control-label{
        display:block;margin-bottom:7px;font-size:12px;font-weight:600;
        color:var(--sp-ink-soft);letter-spacing:.02em;
    }
    .sp-modal .control-label .req{color:#dc2626;font-weight:700;}

    .sp-modal .form-control{
        width:100%;height:42px;padding:0 12px;font-size:14px;color:var(--sp-ink);
        background:var(--sp-white);border:1px solid var(--sp-line);border-radius:8px;
        outline:none;box-shadow:none;transition:all .18s ease;
    }
    .sp-modal .form-control:hover{border-color:#d1d5db;}
    .sp-modal .form-control:focus{
        border-color:var(--sp-primary);box-shadow:0 0 0 3px rgba(31,58,95,.10);
    }
    .sp-modal .form-control[readonly]{
        background:var(--sp-soft);color:var(--sp-muted);cursor:not-allowed;
    }

    /* Footer buttons */
    .sp-modal .btn-default{
        background:var(--sp-white);border:1px solid var(--sp-line);color:var(--sp-ink-soft);
        border-radius:8px;padding:9px 18px;font-weight:600;font-size:13px;
    }
    .sp-modal .btn-default:hover{
        background:var(--sp-line-soft);color:var(--sp-ink);border-color:#d1d5db;
    }
    .sp-modal .btn-primary{
        background:var(--sp-primary);border-color:var(--sp-primary);color:#fff;
        border-radius:8px;padding:9px 20px;font-weight:600;font-size:13px;
        box-shadow:0 1px 2px rgba(31,58,95,.15);
    }
    .sp-modal .btn-primary:hover{
        background:var(--sp-primary-dark);border-color:var(--sp-primary-dark);
    }

    /* Section divider inside modals */
    .sp-section-title{
        font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
        color:var(--sp-muted);margin:0 0 14px;padding-bottom:8px;
        border-bottom:1px solid var(--sp-line);
    }

    @media (max-width:767px){
        .sp-head{flex-direction:column;align-items:stretch;padding:20px;}
        .sp-head-text h3{font-size:17px;}
        .sp-actions{flex-wrap:wrap;}
        .sp-summary{padding:18px 20px 4px;}
        .sp-table-wrap{padding:14px 20px 0;}
    }
</style>

<div class="sp-wrap">

    {{-- ===== Step indicator ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="sp-steps">
                <a href="/" class="completed"><i class="fa fa-home"></i> Home</a>
                <span class="sp-sep"><i class="fa fa-angle-right"></i></span>
                <a href="#" class="active">Supplier Payments</a>
            </div>
        </div>
    </div>

    {{-- ===== Main panel ===== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-custom panel-border">

                <div class="sp-head">
                    <div class="sp-head-text">
                        <h3><i class="fa fa-truck"></i> Supplier Payment Info</h3>
                        <p>Track supplier invoices, payments and outstanding balances.</p>
                    </div>
                </div>

                <div class="sp-body">

                    @php
                        $totalInvoiced = (float) $purchases->sum(fn($p) => (float) $p->total_amount);
                        $totalPaid     = (float) $purchases->sum(fn($p) => (float) $p->total_paid_amount);
                        $totalUnpaid   = (float) $purchases->sum(fn($p) => max(0, (float) $p->total_amount - (float) $p->total_paid_amount));

                        $withBalance   = $purchases->filter(fn($p) => (float) $p->total_amount - (float) $p->total_paid_amount > 0)->count();
                        $fullyPaid     = $purchases->filter(fn($p) => (float) $p->total_amount - (float) $p->total_paid_amount <= 0)->count();
                        $invoiceCount  = $purchases->count();

                        $paidPct = $totalInvoiced > 0
                            ? min(100, round(($totalPaid / $totalInvoiced) * 100, 1))
                            : 0;

                        $invLabel = $invoiceCount === 1 ? 'invoice' : 'invoices';
                        $balLabel = $withBalance === 1 ? 'invoice' : 'invoices';
                    @endphp

                    {{-- ===== Summary KPI cards ===== --}}
                    <div class="sp-summary">

                        <div class="sp-kpi">
                            <div class="sp-kpi-body">
                                <p class="sp-kpi-label">Total Invoiced</p>
                                <div class="sp-kpi-value">{{ number_format($totalInvoiced, 2) }}</div>
                                <div class="sp-kpi-sub">{{ $invoiceCount }} {{ $invLabel }}</div>
                            </div>
                            <div class="sp-kpi-icon"><i class="fa fa-file-text-o"></i></div>
                        </div>

                        <div class="sp-kpi kpi-paid">
                            <div class="sp-kpi-body">
                                <p class="sp-kpi-label">Total Paid</p>
                                <div class="sp-kpi-value">{{ number_format($totalPaid, 2) }}</div>
                                <div class="sp-kpi-progress" title="{{ $paidPct }}% settled">
                                    <span style="width: {{ $paidPct }}%;"></span>
                                </div>
                                <div class="sp-kpi-sub">{{ $paidPct }}% settled · {{ $fullyPaid }} cleared</div>
                            </div>
                            <div class="sp-kpi-icon"><i class="fa fa-check"></i></div>
                        </div>

                        <div class="sp-kpi kpi-unpaid">
                            <div class="sp-kpi-body">
                                <p class="sp-kpi-label">Total Unpaid</p>
                                <div class="sp-kpi-value">{{ number_format($totalUnpaid, 2) }}</div>
                                <div class="sp-kpi-sub">{{ $withBalance }} {{ $balLabel }} with balance</div>
                            </div>
                            <div class="sp-kpi-icon"><i class="fa fa-exclamation-triangle"></i></div>
                        </div>

                        <div class="sp-kpi kpi-count">
                            <div class="sp-kpi-body">
                                <p class="sp-kpi-label">Invoices</p>
                                <div class="sp-kpi-value">{{ $invoiceCount }}</div>
                                <div class="sp-kpi-sub">{{ $fullyPaid }} paid · {{ $withBalance }} pending</div>
                            </div>
                            <div class="sp-kpi-icon"><i class="fa fa-list-alt"></i></div>
                        </div>

                    </div>

                    {{-- ===== Table ===== --}}
                    <div class="sp-table-wrap">
                        <table id="datatable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:48px;">#</th>
                                    <th>Supplier Name</th>
                                    <th>Invoice Number</th>
                                    <th class="text-right">Invoice Amount (VAT incl.)</th>
                                    <th class="text-right">Paid Amount</th>
                                    <th class="text-right">Unpaid Amount</th>
                                    <th class="text-right">Balance</th>
                                    <th style="width:160px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $key => $purchase)
                                    @php
                                        $balance = (float) $purchase->total_amount - (float) $purchase->total_paid_amount;
                                    @endphp
                                    <tr>
                                        <td class="center">{{ $key + 1 }}</td>
                                        <td><strong style="color:var(--sp-ink);">{{ $purchase->supplier_name }}</strong></td>
                                        <td>
                                            <a href="{{ url('suppliers/payments/invoice-details/'.$purchase->invoice_number) }}"
                                               class="sp-invoice-link">
                                                {{ $purchase->invoice_number }}
                                            </a>
                                        </td>
                                        <td class="num">
                                            <span class="sp-amount">{{ number_format($purchase->total_amount, 2) }}</span>
                                        </td>
                                        <td class="num">
                                            <span class="sp-amount">{{ number_format($purchase->total_paid_amount, 2) }}</span>
                                        </td>
                                        <td class="num">
                                            @if($balance > 0)
                                                <span class="sp-unpaid">{{ number_format($balance, 2) }}</span>
                                            @else
                                                <span class="sp-zero">0.00</span>
                                            @endif
                                        </td>
                                        <td class="num">
                                            @if($balance > 0)
                                                <span class="sp-balance">{{ number_format($balance, 2) }}</span>
                                            @elseif($balance < 0)
                                                <span class="sp-overpaid-txt">{{ number_format($balance, 2) }}</span>
                                            @else
                                                <span class="sp-zero">0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="sp-actions">
                                                @if($balance <= 0)
                                                    <button type="button"
                                                            value="{{ $purchase->invoice_number }}"
                                                            class="sp-act sp-act-paid">
                                                        <i class="fa fa-check"></i> Paid
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            value="{{ $purchase->invoice_number }}"
                                                            class="sp-act sp-act-pay addPayment">
                                                        <i class="fa fa-plus"></i> Pay
                                                    </button>
                                                @endif

                                                <a href="{{ url('suppliers/payments/'.$purchase->invoice_number) }}"
                                                   class="sp-act sp-act-view">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
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

{{-- ===================== Add Supplier Payment Modal ===================== --}}
<div class="modal fade bs-example-modal-sm sp-modal" tabindex="-1" role="dialog" aria-hidden="true" id="payment_modal">
    <div class="modal-dialog modal-md">
        <form id="AddPaymentForm" method="post" action="{{ url('suppliers/purchase-payments/add-payment') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Add Supplier Payment</h4>
                </div>

                <div class="modal-body">

                    <div class="sp-section-title">Invoice details</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Supplier Name</label>
                                <input type="text" name="supplier_name" id="supplier_name" readonly class="form-control">
                                <input type="hidden" name="supplier_id" id="supplier_id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Invoice Number</label>
                                <input type="text" name="invoice_number" id="invoice_number" readonly class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Invoice Amount (VAT inclusive)</label>
                                <input type="text" name="total" id="total_amount" readonly class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="sp-section-title" style="margin-top:8px;">Payment details</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Amount <span class="req">*</span></label>
                                <input type="text" name="paid_amount" id="paid_amount" class="form-control"
                                       required placeholder="Enter amount to pay">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Date <span class="req">*</span></label>
                                <input type="text" class="form-control" autocomplete="off"
                                       name="created_date" placeholder="Date paid"
                                       id="datepicker-autoclose" data-date-format="yyyy-mm-dd" required>
                            </div>
                        </div>
                    </div>

                    {{-- Account select intentionally left commented as before --}}
                    {{--
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Account <span class="req">*</span></label>
                                <select name="account_id" required class="form-control select2" id="account_id"></select>
                            </div>
                        </div>
                    </div>
                    --}}

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
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

    $('.addPayment').click(function() {
         var invoice_id = $(this).val();
         $.ajax({
             url: `../accounts/payments-details/${invoice_id}`,
             type: "GET",
             success: function (response) {
                 $('#payment_modal').modal('show');
                 var a = parseFloat(response[0].total, 2);
                 var b = parseFloat(response[0].calculated_vat_amount, 2);
                 $("#supplier_id").val(response[0].supplier_id);
                 $("#supplier_name").val(response[0].supplier_name);
                 $("#invoice_number").val(response[0].invoice_number);
                 $("#total_amount").val(a + b);
                 $("#vat_amount").val(response[0].vat_amount);
               }
         });
    });

  });
</script>

@endsection