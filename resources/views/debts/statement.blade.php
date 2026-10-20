@extends('layouts.app_header')
@section('content')
<style>
    :root {
        --brand-50:      #eef3f8;
        --brand-100:     #d9e3ee;
        --brand-200:     #b3c7dd;
        --brand-500:     #2c5282;
        --brand:         #5790da;
        --brand-700:     #172d49;
        --brand-900:     #0f1f33;

        --ink-900:       #1a2332;
        --ink-800:       #2c3e50;
        --ink-700:       #3d4d60;
        --ink-500:       #6c757d;
        --ink-400:       #8a949e;
        --ink-300:       #b8c1ca;
        --ink-200:       #dde3e9;
        --ink-100:       #eef1f4;
        --ink-50:        #f7f9fb;

        --border:        #e2e6ea;
        --border-strong: #cbd3db;
        --bg-soft:       #f7f9fb;
        --bg-hover:      #f3f6f9;

        --success:       #059669;
        --success-50:    #ecfdf5;
        --success-200:   #a7f3d0;
        --warning:       #b45309;
        --warning-50:    #fef7e6;
        --warning-200:   #fcd98b;
        --danger:        #c0392b;
        --danger-50:     #fdf2f1;
        --danger-200:    #f5c2bc;

        --ease:          cubic-bezier(0.16, 1, 0.3, 1);
        --radius-sm:     4px;
        --radius:        6px;
        --radius-md:     8px;

        --shadow-xs:     0 1px 2px rgba(15, 31, 51, 0.04);
        --shadow-sm:     0 1px 3px rgba(15, 31, 51, 0.06), 0 1px 2px rgba(15, 31, 51, 0.03);
        --shadow-md:     0 4px 10px -2px rgba(15, 31, 51, 0.08), 0 2px 4px -2px rgba(15, 31, 51, 0.04);
        --shadow-lg:     0 12px 22px -6px rgba(15, 31, 51, 0.12), 0 4px 8px -4px rgba(15, 31, 51, 0.06);
    }
    body { color: var(--ink-800); }

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
        font-size: 19px; font-weight: 700; color: var(--ink-900);
        margin: 0; letter-spacing: -0.015em;
    }

    /* Summary cards */
    .summary-row { margin-bottom: 22px; }
    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-top: 3px solid var(--brand);
        border-radius: var(--radius);
        padding: 20px 22px;
        box-shadow: var(--shadow-sm);
        transition: all 0.22s var(--ease);
    }
    .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
    .stat-card .label {
        text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em;
        color: var(--ink-500); font-weight: 700; margin-bottom: 6px;
    }
    .stat-card .value {
        font-size: 22px; font-weight: 700; color: var(--ink-900);
        line-height: 1.1; letter-spacing: -0.02em;
        font-variant-numeric: tabular-nums;
    }
    .summary-row > div:nth-child(2) .stat-card { border-top-color: var(--success); }
    .summary-row > div:nth-child(2) .stat-card .value { color: var(--success); }
    .summary-row > div:nth-child(3) .stat-card { border-top-color: var(--danger); }
    .summary-row > div:nth-child(3) .stat-card .value { color: var(--danger); }

    /* Buttons */
    .btn { font-weight: 500; letter-spacing: 0.01em; border-radius: var(--radius-sm);
           transition: all 0.18s var(--ease); }
    .btn-primary {
        background: var(--brand) !important; border-color: var(--brand) !important;
        color: #fff !important; box-shadow: 0 1px 2px rgba(30, 58, 95, 0.15);
    }
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
        background: var(--brand-700) !important; border-color: var(--brand-700) !important;
        box-shadow: 0 4px 10px rgba(30, 58, 95, 0.22); transform: translateY(-1px);
    }
    .btn-default {
        background: #fff; border: 1px solid var(--border-strong);
        color: var(--ink-700); box-shadow: var(--shadow-xs);
    }
    .btn-default:hover { background: var(--bg-soft); border-color: var(--ink-400); color: var(--brand); }
    .btn-rounded { border-radius: var(--radius-sm) !important; }
    .btn-sm { padding: 5px 10px; font-size: 12px; }

    .btn-danger {
        background: var(--danger) !important;
        border-color: var(--danger) !important;
        color: #fff !important;
    }
    .btn-danger:hover, .btn-danger:focus {
        background: #a93226 !important;
        border-color: #a93226 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(192, 57, 43, 0.22);
    }

    /* Statement header */
    .stmt-title { font-size: 18px; font-weight: 700; color: var(--ink-900);
                  margin: 0 0 4px; letter-spacing: -0.01em; }
    .stmt-meta { color: var(--ink-500); font-size: 13px; line-height: 1.7; }

    /* Status badge */
    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 700; letter-spacing: 0.05em;
        text-transform: uppercase; border: 1px solid transparent;
    }
    .status-badge::before {
        content: ''; width: 6px; height: 6px; border-radius: 50%;
        background: currentColor; flex-shrink: 0;
    }
    .badge-paid    { color: var(--success); background: var(--success-50); border-color: var(--success-200); }
    .badge-partial { color: var(--warning); background: var(--warning-50); border-color: var(--warning-200); }
    .badge-unpaid  { color: var(--danger);  background: var(--danger-50);  border-color: var(--danger-200); }

    /* Payment history table */
    .stmt-table thead th {
        background: var(--brand) !important;
        color: #fff !important;
        text-transform: uppercase;
        font-size: 11px !important;
        font-weight: 600; letter-spacing: 0.07em;
        padding: 12px !important;
        border: none !important;
    }
    .stmt-table thead th:first-child { border-top-left-radius: var(--radius-sm); }
    .stmt-table thead th:last-child  { border-top-right-radius: var(--radius-sm); }

    .stmt-table tbody td {
        font-size: 13px;
        padding: 12px !important;
        border-top: 1px solid var(--ink-100) !important;
        border-bottom: none !important;
        vertical-align: middle;
        color: var(--ink-800);
        background: #fff;
    }
    .stmt-table tbody tr:hover td { background: var(--bg-hover); }
    .stmt-table tbody tr:last-child td { border-bottom: 1px solid var(--ink-100) !important; }

    .stmt-table .text-right { text-align: right; font-variant-numeric: tabular-nums; }

    .text-success { color: var(--success) !important; font-weight: 600; }
    .text-danger  { color: var(--danger)  !important; font-weight: 600; }

    .stmt-table tfoot td {
        padding: 12px !important;
        background: var(--bg-soft) !important;
        font-weight: 700;
        border-top: 2px solid var(--brand) !important;
        font-variant-numeric: tabular-nums;
    }

    /* Modals */
    .modal-content {
        border-radius: var(--radius-md); border: none;
        box-shadow: var(--shadow-lg); background: #fff; overflow: hidden;
    }
    .modal-header { border-bottom: 1px solid var(--border); padding: 17px 22px; background: #fff; }
    .modal-header .close { font-size: 22px; opacity: 0.35; transition: opacity 0.15s var(--ease); }
    .modal-header .close:hover { opacity: 1; }
    .modal-title { font-size: 16px; font-weight: 700; color: var(--ink-900); letter-spacing: -0.01em; }
    .modal-body { padding: 22px; }
    .modal-footer { border-top: 1px solid var(--border); padding: 13px 22px; background: var(--bg-soft); }
    .modal.active { z-index: 2000; }

    .modal .form-control {
        border: 1px solid var(--border-strong); border-radius: var(--radius-sm);
        height: 38px; font-size: 13px; color: var(--ink-900);
        box-shadow: var(--shadow-xs); transition: all 0.15s var(--ease);
    }
    .modal .form-control:focus {
        border-color: var(--brand); box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.10);
    }
    .modal label.control-label { font-size: 12px; font-weight: 600; color: var(--ink-700); margin-bottom: 6px; }

    .alert-success {
        background: var(--success-50); color: #065f46;
        border: none; border-left: 3px solid var(--success);
        border-radius: var(--radius-sm);
    }
    .alert-danger {
        background: var(--danger-50); color: #991b1b;
        border: none; border-left: 3px solid var(--danger);
        border-radius: var(--radius-sm);
    }

    @media print { .no-print { display: none !important; } .panel-custom { box-shadow: none; } }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <div class="no-print" style="float:right">
                    <a href="{{ route('debts.index') }}" class="btn btn-default btn-rounded waves-effect">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('debts.statement.pdf', $debt->id) }}" target="_blank"
                       class="btn btn-primary btn-rounded waves-effect">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-default btn-rounded waves-effect">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
                <h3 class="panel-title1"> Debt Statement </h3>
            </div>

            <div class="panel-body" style="padding: 24px;">

                {{-- Flash + validation --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <ul class="m-b-0">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                {{-- Header info --}}
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="stmt-title">{{ strtoupper($debt->particulars) }}</h4>
                        <p class="stmt-meta">
                            Category: <strong>{{ $debt->category->name ?? '-' }}</strong><br>
                            Period: <strong>{{ $debt->period }}</strong><br>
                            Created: {{ $debt->created_at ? $debt->created_at->format('d M, Y') : '-' }}
                            &nbsp;|&nbsp; Reference: DEBT-{{ str_pad($debt->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                    <div class="col-md-4 text-right" style="padding-top: 8px;">
                        @php
                            $badge = ['Paid' => 'badge-paid', 'Partial' => 'badge-partial', 'Unpaid' => 'badge-unpaid'][$debt->status] ?? 'badge-unpaid';
                        @endphp
                        <span class="status-badge {{ $badge }}">{{ strtoupper($debt->status) }}</span>
                    </div>
                </div>

               

                {{-- Payment history --}}
                <h5 style="font-size: 14px; font-weight: 700; color: var(--ink-900); margin: 20px 0 10px;">
                    Payment History
                    @if($debt->payments->count())
                        <span class="label" style="font-size:10px; text-transform:uppercase; letter-spacing:0.08em;
                              background:var(--brand-50); color:var(--brand); padding:3px 10px;
                              border-radius:20px; margin-left:8px; font-weight:600;">
                            {{ $debt->payments->count() }} payment{{ $debt->payments->count() > 1 ? 's' : '' }}
                        </span>
                    @endif
                </h5>

                <table class="table stmt-table">
                    <thead>
                        <tr>
                            <th width="50">SN</th>
                            <th width="130">Date</th>
                            <th>Notes / Reference</th>
                            <th class="text-right" width="130">Amount Paid</th>
                            <th class="text-right" width="130">Running Balance</th>
                            <th class="no-print" width="140">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $running = (float) $debt->amount; @endphp
                        @forelse($debt->payments as $p)
                            @php $running -= (float) $p->amount; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->paid_date)->format('d M, Y') }}</td>
                                <td>{{ $p->notes ?: '—' }}</td>
                                <td class="text-right text-success">{{ number_format($p->amount, 2) }}</td>
                                <td class="text-right">{{ number_format($running, 2) }}</td>
                                <td class="no-print">
                                    <button type="button"
                                            class="btn btn-sm btn-default btn-rounded editPayment"
                                            title="Edit this payment"
                                            data-id="{{ $p->id }}"
                                            data-amount="{{ number_format($p->amount, 2, '.', '') }}"
                                            data-paid-date="{{ \Carbon\Carbon::parse($p->paid_date)->format('Y-m-d') }}"
                                            data-notes="{{ $p->notes }}">
                                        <i class="fa fa-pencil"></i> Edit
                                    </button>
                                    <form action="{{ route('debts.payments.destroy', $p->id) }}"
                                          method="POST"
                                          style="display:inline-block;"
                                          onsubmit="return confirm('Delete this payment of {{ number_format($p->amount, 2) }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger btn-rounded"
                                                title="Delete this payment">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding:32px 20px;
                                    color:var(--ink-400); font-style:italic; background:var(--bg-soft);">
                                    No payments recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($debt->payments->count())
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">TOTAL</td>
                            <td class="text-right text-success">{{ number_format($debt->paid_amount, 2) }}</td>
                            <td class="text-right text-danger">{{ number_format($debt->unpaid_amount, 2) }}</td>
                            <td class="no-print"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>

                {{-- Pay button (add new payment) --}}
                @if($debt->unpaid_amount > 0)
                    <div class="m-t-20">
                        <button type="button"
                                class="btn btn-primary btn-rounded waves-effect addPayment"
                                data-id="{{ $debt->id }}"
                                data-particulars="{{ $debt->particulars }}"
                                data-category="{{ $debt->category->name ?? '-' }}"
                                data-period="{{ $debt->period }}"
                                data-amount="{{ number_format($debt->amount, 2, '.', '') }}"
                                data-paid="{{ number_format($debt->paid_amount, 2, '.', '') }}"
                                data-unpaid="{{ number_format($debt->unpaid_amount, 2, '.', '') }}">
                            <i class="fa fa-plus"></i> Record New Payment
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ================= EDIT PAYMENT MODAL ================= --}}
<div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <form id="editPaymentForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Edit Payment</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="editError" style="display:none;"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01"
                                       name="amount" id="edit_amount"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Payment Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="paid_date" id="edit_paid_date"
                                           class="form-control datepicker"
                                           autocomplete="off" required>
                                    <span class="input-group-addon bg-info b-0 text-white">
                                        <i class="ti-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Notes / Reference</label>
                        <input type="text" name="notes" id="edit_notes"
                               class="form-control" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-rounded" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-rounded">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= PAYMENT MODAL (add new) ================= --}}
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
                    <table class="table pay-summary" style="margin-bottom:16px;">
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
                                    <span class="input-group-addon bg-info b-0 text-white">
                                        <i class="ti-calendar"></i>
                                    </span>
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

    if ($.fn.datepicker) {
        $('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'yyyy-mm-dd' });
    }

    function numberFormat(n) {
        return parseFloat(n || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }

    /* -------- EDIT a payment -------- */
    $(document).on('click', '.editPayment', function () {
        var b = $(this);

        var url = "{{ route('debts.payments.update', ':id') }}".replace(':id', b.data('id'));
        $('#editPaymentForm').attr('action', url);
        $('#edit_amount').val(b.data('amount'));
        $('#edit_paid_date').val(b.data('paid-date'));
        $('#edit_notes').val(b.data('notes') || '');
        $('#editError').hide().text('');

        $('#editPaymentModal').modal('show');
    });

    /* -------- ADD a new payment -------- */
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

    /* -------- Server-side validation error → reopen edit modal with message -------- */
    @if($errors->any() && old('_method') === 'PATCH')
        $('#edit_amount').val("{{ old('amount') }}");
        $('#edit_paid_date').val("{{ old('paid_date') }}");
        $('#edit_notes').val("{{ old('notes') }}");
        $('#editPaymentForm').attr('action', "{{ url()->previous() }}#");
        $('#editError').text("{{ $errors->first() }}").show();
        $('#editPaymentModal').modal('show');
    @endif
});
</script>

@endsection