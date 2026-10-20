

@extends('layouts.app_header')
@section('content')
 

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
            color: #343a40;
        }

        .page-wrapper {
            padding: 25px 15px;
        }

        .report-card {
            background: #fff;
            border-radius: 8px;
            padding: 22px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
        }

        .table th {
            background: #f0f2f5;
            white-space: nowrap;
            font-size: 12px;
        }

        .table td {
            font-size: 12px;
            vertical-align: middle;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
        }

        .filter-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .badge {
            padding: 6px 8px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .report-card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
<div class="container-fluid page-wrapper">
    <div class="report-card">

        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <div class="page-title">Petty Cash Transactions</div>
                <div class="text-muted">
                    Manage opening balances, collections and payments.
                </div>
            </div>

            <a href="{{ route('account-transactions.create') }}"
               class="btn btn-primary no-print mt-2">
                + New Transaction
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mt-3">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mt-3">
                Please check the submitted information.
            </div>
        @endif

        <form method="GET"
              action="{{ route('account-transactions.index') }}"
              class="filter-card no-print">

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label>From Date</label>
                    <input type="date"
                           name="startdate"
                           class="form-control"
                           value="{{ request('startdate') }}">
                </div>

                <div class="form-group col-md-2">
                    <label>To Date</label>
                    <input type="date"
                           name="enddate"
                           class="form-control"
                           value="{{ request('enddate') }}">
                </div>

                <div class="form-group col-md-3">
                    <label>Transaction Type</label>
                    <select name="transaction_type" class="form-control">
                        <option value="">All Transactions</option>

                        @foreach([
                            'opening_balance' => 'Opening Balance',
                            'cash_collection' => 'Cash Collection',
                            'expense' => 'Expense',
                            'cash_payment' => 'Cash Payment',
                            'adjustment' => 'Adjustment',
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                {{ request('transaction_type') == $value ? 'selected' : '' }}>
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
                                {{ request('expense_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2 d-flex align-items-end">
                    <button class="btn btn-dark mr-2" type="submit">
                        Filter
                    </button>

                    <a href="{{ route('account-transactions.index') }}"
                       class="btn btn-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive1">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Type</th>
                        <th>Voucher</th>
                        <th class="text-right">Debit (TZS)</th>
                        <th class="text-right">Credit (TZS)</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                {{ $transaction->transaction_date->format('d/m/Y') }}
                            </td>

                            <td>
                                <strong>{{ $transaction->particulars }}</strong>

                                @if($transaction->description)
                                    <br>
                                    <small class="text-muted">
                                        {{ $transaction->description }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                {{ ucwords(str_replace(
                                    '_',
                                    ' ',
                                    $transaction->transaction_type
                                )) }}
                            </td>

                            <td>
                                {{ $transaction->voucher_type }}

                                @if($transaction->voucher_no)
                                    <br>
                                    <small>{{ $transaction->voucher_no }}</small>
                                @endif
                            </td>

                            <td class="amount">
                                {{ number_format((float)$transaction->debit, 2) }}
                            </td>

                            <td class="amount">
                                {{ number_format((float)$transaction->credit, 2) }}
                            </td>

                            <td class="no-print">
                                <a href="{{ route(
                                    'account-transactions.edit',
                                    $transaction->id
                                ) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>

                                @if(empty($transaction->reference_type))
                                    <form method="POST"
                                          action="{{ route(
                                              'account-transactions.destroy',
                                              $transaction->id
                                          ) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('Delete this transaction?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="no-print">
            {{ $transactions->links() }}
        </div>

    </div>
</div>
@endsection