
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Petty Cash Ledger Report</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f3f5f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #343a40;
            font-size: 13px;
        }

        .page-wrapper {
            padding: 25px 15px;
        }

        .report-card {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,.06);
        }

        .page-title {
            font-size: 23px;
            font-weight: 700;
        }

        .subtitle {
            color: #6c757d;
        }

        .filter-panel {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            padding: 18px;
            border-radius: 7px;
            margin: 22px 0;
        }

        .filter-panel label {
            font-weight: 600;
        }

        .summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            padding: 17px;
            height: 100%;
        }

        .summary-label {
            color: #6c757d;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 19px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .ledger-title {
            font-weight: 700;
            font-size: 17px;
            text-align: center;
            margin-top: 25px;
        }

        .ledger-period {
            text-align: center;
            color: #6c757d;
            margin-bottom: 18px;
        }

        .table thead th {
            background: #e9ecef;
            border: 1px solid #d8dde2;
            white-space: nowrap;
            font-size: 12px;
        }

        .table td {
            border: 1px solid #e0e4e8;
            vertical-align: middle;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
        }

        .opening-row,
        .totals-row {
            font-weight: 700;
            background: #f3f5f7;
        }

        .closing-row {
            font-weight: 700;
            background: #e9ecef;
        }

        .nowrap {
            white-space: nowrap;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 12mm;
            }

            body {
                background: #fff;
                font-size: 10px;
            }

            .page-wrapper {
                padding: 0;
            }

            .report-card {
                box-shadow: none;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .summary-card {
                padding: 8px;
            }

            .summary-value {
                font-size: 13px;
            }

            .table {
                font-size: 9px;
            }

            .table thead th {
                background: #eee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
<div class="container-fluid page-wrapper">
    <div class="report-card">

        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <div class="page-title">Petty Cash Ledger Report</div>
                <div class="subtitle">
                    Transaction analysis and cash movement
                </div>
            </div>

            <div class="no-print mt-2">
                <button type="button"
                        onclick="window.print()"
                        class="btn btn-dark">
                    Print Report
                </button>

                <a href="{{ route('account-transactions.index') }}"
                   class="btn btn-outline-secondary">
                    Transactions
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mt-3">
                Please correct the report filters.
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET"
              action="{{ route('account-transactions.report') }}"
              class="filter-panel no-print">

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label>Start Date</label>
                    <input type="date"
                           name="startdate"
                           class="form-control"
                           value="{{ $filters['startdate'] }}">
                </div>

                <div class="form-group col-md-2">
                    <label>End Date</label>
                    <input type="date"
                           name="enddate"
                           class="form-control"
                           value="{{ $filters['enddate'] }}">
                </div>

                <div class="form-group col-md-3">
                    <label>Transaction Type</label>
                    <select name="transaction_type" class="form-control">
                        <option value="">All Types</option>

                        @foreach([
                            'opening_balance' => 'Opening Balance',
                            'cash_collection' => 'Cash Collection',
                            'expense' => 'Expense',
                            'cash_payment' => 'Cash Payment',
                            'adjustment' => 'Adjustment',
                        ] as $value => $label)

                            <option value="{{ $value }}"
                                {{ $filters['transaction_type'] == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label>Expense Category</label>
                    <select name="expense_category_id" class="form-control">
                        <option value="">All Categories</option>

                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $filters['expense_category_id'] == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label>Search</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Particulars / voucher"
                           value="{{ $filters['search'] }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Apply Filters
            </button>

            <a href="{{ route('account-transactions.report') }}"
               class="btn btn-outline-secondary">
                Reset
            </a>
        </form>

        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="summary-card">
                    <div class="summary-label">Opening Balance</div>
                    <div class="summary-value">
                        {{ number_format($openingBalance, 2) }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="summary-card">
                    <div class="summary-label">Total Debit</div>
                    <div class="summary-value text-success">
                        {{ number_format($totalDebit, 2) }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="summary-card">
                    <div class="summary-label">Total Credit</div>
                    <div class="summary-value text-danger">
                        {{ number_format($totalCredit, 2) }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="summary-card">
                    <div class="summary-label">Closing Balance</div>
                    <div class="summary-value">
                        {{ number_format($closingBalance, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="ledger-title">PETTY CASH LEDGER</div>

        <div class="ledger-period">
            Period:
            {{ \Carbon\Carbon::parse($filters['startdate'])->format('d/m/Y') }}
            to
            {{ \Carbon\Carbon::parse($filters['enddate'])->format('d/m/Y') }}
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Voucher Type</th>
                        <th>Voucher No.</th>
                        <th class="amount">Debit (TZS)</th>
                        <th class="amount">Credit (TZS)</th>
                        <th class="amount">Balance (TZS)</th>
                    </tr>
                </thead>

                <tbody>
                    <tr class="opening-row">
                        <td class="nowrap">
                            {{ \Carbon\Carbon::parse($filters['startdate'])->format('d/m/Y') }}
                        </td>
                        <td>To Opening Balance</td>
                        <td></td>
                        <td></td>
                        <td class="amount">
                            {{ number_format(max(0, $openingBalance), 2) }}
                        </td>
                        <td class="amount">
                            {{ number_format(max(0, -$openingBalance), 2) }}
                        </td>
                        <td class="amount">
                            {{ number_format($openingBalance, 2) }}
                        </td>
                    </tr>

                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="nowrap">
                                {{ $transaction->transaction_date->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $transaction->credit > 0 ? 'By' : 'To' }}
                                {{ $transaction->particulars }}
                            </td>

                            <td>
                                {{ $transaction->voucher_type }}
                            </td>

                            <td>
                                {{ $transaction->voucher_no }}
                            </td>

                            <td class="amount">
                                {{ $transaction->debit > 0
                                    ? number_format($transaction->debit, 2)
                                    : '-' }}
                            </td>

                            <td class="amount">
                                {{ $transaction->credit > 0
                                    ? number_format($transaction->credit, 2)
                                    : '-' }}
                            </td>

                            <td class="amount">
                                {{ number_format($transaction->running_balance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No transactions match your selected filters.
                            </td>
                        </tr>
                    @endforelse

                    <tr class="totals-row">
                        <td colspan="4" class="text-right">
                            Period Totals
                        </td>

                        <td class="amount">
                            {{ number_format($totalDebit, 2) }}
                        </td>

                        <td class="amount">
                            {{ number_format($totalCredit, 2) }}
                        </td>

                        <td></td>
                    </tr>

                    <tr class="closing-row">
                        <td colspan="4">Closing Balance</td>
                        <td colspan="3" class="amount">
                            {{ number_format($closingBalance, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-muted mt-3 small">
            Amounts are displayed in Tanzanian Shillings (TZS).
            Generated on {{ now()->format('d/m/Y H:i') }}.
        </div>

    </div>
</div>
</body>
</html>
