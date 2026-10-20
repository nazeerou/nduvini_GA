<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ledger Report</title>
    <style>
        @page {
            margin: 4px 24px 48px 24px;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10.5px;
            color: #2a2a2a;
            line-height: 1.4;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #1f3a5f;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-table td { vertical-align: bottom; padding: 0; }
        .report-title {
            font-size: 16px; font-weight: bold; color: #1f3a5f;
            letter-spacing: 0.02em;
        }
        .company-name {
            font-size: 9px; color: #6b7280;
            text-transform: uppercase; letter-spacing: 0.12em;
            margin-top: 3px;
        }
        .date-range { text-align: right; }
        .date-range .label {
            display: block; font-size: 7.5px;
            text-transform: uppercase; letter-spacing: 0.14em;
            color: #9ca3af;
        }
        .date-range .value {
            display: block; font-size: 10.5px;
            font-weight: bold; color: #1f3a5f; margin-top: 2px;
        }

        /* LEDGER TABLE */
        .ledger-table {
            width: 100%; border-collapse: collapse;
            margin-top: 6px; font-size: 10px;
        }
        .ledger-table thead th {
            background-color: #1f3a5f; color: #ffffff;
            font-size: 8.5px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.08em;
            padding: 8px 7px; text-align: left;
            border: 1px solid #1f3a5f;
        }
        .ledger-table thead th.text-right { text-align: right; }
        .ledger-table tbody td {
            padding: 6px 7px; vertical-align: top;
            border-bottom: 1px solid #e5e7eb;
            border-left: 1px solid #f3f4f6;
            border-right: 1px solid #f3f4f6;
        }
        .ledger-table tbody tr { page-break-inside: avoid; }
        .ledger-table tbody tr:nth-child(even) td { background-color: #f9fafb; }

        .text-right { text-align: right; }

        .ledger-table td.num {
            font-family: 'Courier New', monospace;
            font-size: 10px; white-space: nowrap;
            letter-spacing: 0.02em;
        }

        /* DETAIL ROWS */
        .ledger-table tr.detail-row td {
            font-size: 9.5px; color: #6b7280;
            background-color: #fdfdfd !important;
            padding-top: 3px; padding-bottom: 3px;
            border-bottom: 1px dashed #eef0f3;
            border-top: none;
        }
        .ledger-table tr.detail-row td.category {
            padding-left: 22px; font-style: italic;
        }

        /* OPENING / CLOSING BALANCE ROWS */
        .ledger-table tbody tr.balance-row td {
            background-color: #eef3fa !important;
            color: #1f3a5f; font-weight: bold;
            border-top: 1px solid #c7d4e6;
            border-bottom: 1px solid #c7d4e6;
        }
        .ledger-table tbody tr.balance-row td.label { font-style: italic; }

        /* SUBTOTAL ROW */
        .ledger-table tbody tr.subtotal-row td {
            background-color: #f5f8fc !important;
            color: #1f3a5f;
            font-weight: bold;
            font-size: 10px;
            border-top: 1.5px solid #c7d4e6;
            border-bottom: 1px solid #dbe3ef;
            font-style: italic;
        }
        .ledger-table tbody tr.subtotal-row td.num {
            font-family: 'Courier New', monospace;
        }

        /* GRAND TOTAL FOOTER */
        .ledger-table tfoot td {
            background-color: #e3ebf5;
            border-top: 2px solid #1f3a5f;
            border-bottom: 3px double #1f3a5f;
            font-weight: bold; font-size: 10.5px;
            color: #1f3a5f; padding: 10px 7px;
        }
        .ledger-table tfoot td.num {
            font-family: 'Courier New', monospace; font-size: 10px;
        }

        /* EMPTY STATE */
        .ledger-table td.empty-cell {
            text-align: center; padding: 24px;
            color: #9ca3af; font-style: italic; font-size: 10px;
        }

        /* Watermark */
        .logo-watermark {
            position: fixed;
            top: 25%;
            left: 22%;
            width: 56%;
            opacity: 0.05;
            z-index: -2;
        }
        .logo-watermark img { width: 100%; }

        /* Header image */
        .header { text-align: center; }
        .header-image {
            display: block;
            width: 100%;
            height: auto;
            margin-bottom: 8px;
        }

        /* PETTY CASH DETAIL TABLE */
        .petty-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 10px;
        }
        .petty-table thead th {
            background-color: #1f3a5f;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 8px 7px;
            text-align: left;
            border: 1px solid #1f3a5f;
        }
        .petty-table tbody td {
            padding: 6px 7px;
            vertical-align: top;
            border-bottom: 1px solid #e5e7eb;
            border-left: 1px solid #f3f4f6;
            border-right: 1px solid #f3f4f6;
        }
        .petty-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }
        .petty-table tfoot td {
            background-color: #e3ebf5;
            border-top: 2px solid #1f3a5f;
            border-bottom: 3px double #1f3a5f;
            font-weight: bold;
            font-size: 10.5px;
            color: #1f3a5f;
            padding: 10px 7px;
        }
    </style>
</head>
<body>

@php
    /* ---------- Normalise values ---------- */
    $openingBalance = (float) ($openingBalance ?? 0);
    $totalDebit     = (float) ($totalDebit ?? 0);
    $totalCredit    = (float) ($totalCredit ?? 0);

    $closingBalance = (float) ($closingBalance
        ?? ($openingBalance + $totalDebit - $totalCredit));

    $openingDebit  = max(0, $openingBalance);
    $openingCredit = max(0, -$openingBalance);

    $subtotalDebit  = $openingDebit  + $totalDebit;
    $subtotalCredit = $openingCredit + $totalCredit;

    $grandDebit  = $subtotalDebit  + ($closingBalance < 0 ? abs($closingBalance) : 0);
    $grandCredit = $subtotalCredit + ($closingBalance > 0 ?  $closingBalance     : 0);

    /* ---------- Dates ---------- */
    $openingDateDisplay = request('startdate')
        ? \Carbon\Carbon::parse(request('startdate'))->format('d/m/Y')
        : 'Opening';

    $lastRowDate = null;
    if (count($ledger)) {
        $rowsArray   = is_array($ledger) ? $ledger : $ledger->toArray();
        $lastRowDate = end($rowsArray)['date'] ?? null;
    }
    $closingDateDisplay = request('enddate')
        ? \Carbon\Carbon::parse(request('enddate'))->format('d/m/Y')
        : ($lastRowDate ? \Carbon\Carbon::parse($lastRowDate)->format('d/m/Y') : 'Closing');

    $startDateRange = request('startdate')
        ? \Carbon\Carbon::parse(request('startdate'))->format('d-M-Y')
        : 'Start';
    $endDateRange = request('enddate')
        ? \Carbon\Carbon::parse(request('enddate'))->format('d-M-Y')
        : 'End';

    $accountName = $accountId
        ? (optional(\App\Models\Account::find($accountId))->name ?? 'Petty Cash')
        : 'Petty Cash';

    /* ---------- Display rows ---------- */
    // When a category is selected, drop any opening row that might slip in
    $displayRows = collect($ledger);
    if ($categoryId) {
        $displayRows = $displayRows
            ->reject(fn ($r) => !empty($r['is_opening']))
            ->values();
    }

    $hasActivity = $displayRows->count() > 0 || (!$categoryId && $openingBalance != 0);

    $footerTotal = $categoryId
        ? (float) ($total_amounts[0]->total_amount ?? 0)
        : null;

    $reportTitle = $categoryId
        ? 'Petty Cash Report: ' . ($petty->name ?? 'Category')
        : 'Ledger: ' . $accountName;
@endphp

{{-- Watermark --}}
@if(file_exists(public_path('assets/images/logo_nduvini.jpeg')))
    <div class="logo-watermark">
        <img src="{{ public_path('assets/images/logo_nduvini.jpeg') }}" alt="NAW Watermark">
    </div>
@endif

{{-- Company header --}}
<div class="header">
    @if(file_exists(public_path('assets/images/header.png')))
        <img src="{{ public_path('assets/images/header.png') }}" class="header-image" alt="NAW Header">
    @endif
</div>

{{-- REPORT HEADER --}}
<table class="header-table">
    <tr>
        <td>
            <div class="report-title">{{ $reportTitle }}</div>
            <div class="company-name">
                {{ strtoupper($settings['business_name'] ?? '') }} -
                @if(Auth::user()->branch_id == 1)
                    HEAD OFFICE - DAR ES SALAAM
                @elseif(Auth::user()->branch_id == 2)
                    TABORA BRANCH
                @elseif(Auth::user()->branch_id == 3)
                    DODOMA BRANCH
                @else
                    {{ strtoupper($settings['address'] ?? '') }}
                @endif
            </div>
        </td>
        <td class="date-range">
            <span class="label">Statement Period</span>
            <span class="value">{{ $startDateRange }} &nbsp;to&nbsp; {{ $endDateRange }}</span>
        </td>
    </tr>
</table>

{{-- LEDGER TABLE --}}
<table class="ledger-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Particulars</th>
            <th></th>
            <th>Vch Type</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
        </tr>
    </thead>

    <tbody>
        {{-- OPENING BALANCE — full ledger only --}}
        @if(!$categoryId && $openingBalance != 0)
            <tr class="balance-row">
                <td>{{ $openingDateDisplay }}</td>
                <td class="label">
                    {{ $openingBalance > 0 ? 'To' : 'By' }}
                    Opening Balance B/F
                </td>
                <td></td>
                <td></td>
                <td class="num text-right">
                    {{ $openingBalance > 0 ? number_format($openingBalance, 2) : '' }}
                </td>
                <td class="num text-right">
                    {{ $openingBalance < 0 ? number_format(abs($openingBalance), 2) : '' }}
                </td>
            </tr>
        @endif

        {{-- TRANSACTION ROWS --}}
        @forelse($displayRows as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                <td>{{ $row['type'] }}</td>
                <td>{{ $row['particulars'] }}</td>
                <td>{{ $row['vch_type'] }}</td>
                <td class="num text-right">
                    {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}
                </td>
                <td class="num text-right">
                    {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}
                </td>
            </tr>

            {{-- Detail sub-rows --}}
            @if(!empty($row['details']) && count($row['details']) > 0)
                @foreach($row['details'] as $detail)
                    <tr class="detail-row">
                        <td></td>
                        <td class="category">{{ $detail['category'] }}</td>
                        <td class="num text-right">
                            {{ $row['debit'] > 0 ? number_format($detail['amount'], 2) : '' }}
                        </td>
                        <td class="num text-right">
                            {{ $row['credit'] > 0 ? number_format($detail['amount'], 2) : '' }}
                        </td>
                        <td></td><td></td>
                    </tr>
                @endforeach
            @endif
        @empty
            <tr>
                <td colspan="6" class="empty-cell">No records found.</td>
            </tr>
        @endforelse

        {{-- SUBTOTAL — full ledger only --}}
        @if(!$categoryId && $hasActivity)
            <tr class="subtotal-row">
                <td></td>
                <td class="label" colspan="3"></td>
                <td class="num text-right">{{ number_format($subtotalDebit, 2) }}</td>
                <td class="num text-right">{{ number_format($subtotalCredit, 2) }}</td>
            </tr>
        @endif

        {{-- CLOSING BALANCE — full ledger only --}}
        @if(!$categoryId && $closingBalance != 0)
            <tr class="balance-row">
                <td>{{ $closingDateDisplay }}</td>
                <td class="label">
                    {{ $closingBalance > 0 ? 'By' : 'To' }}
                    Closing Balance C/F
                </td>
                <td></td>
                <td></td>
                <td class="num text-right">
                    {{ $closingBalance < 0 ? number_format(abs($closingBalance), 2) : '' }}
                </td>
                <td class="num text-right">
                    {{ $closingBalance > 0 ? number_format($closingBalance, 2) : '' }}
                </td>
            </tr>
        @endif
    </tbody>

    {{-- FOOTER --}}
    @if($categoryId)
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">
                    Total Expenses{{ !empty($petty) ? ' ('.$petty->name.')' : '' }}
                </td>
                <td class="num text-right">—</td>
                <td class="num text-right">{{ number_format($footerTotal, 2) }}</td>
            </tr>
        </tfoot>
    @elseif($hasActivity)
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Grand Total</td>
                <td class="num text-right">{{ number_format($grandDebit, 2) }}</td>
                <td class="num text-right">{{ number_format($grandCredit, 2) }}</td>
            </tr>
        </tfoot>
    @endif
</table>

{{-- PETTY CASH DETAIL — only when a category is selected --}}
@if(!empty($petty) && $petty_cash->count() > 0)

    <div style="page-break-before: always;"></div>

    <table class="header-table">
        <tr>
            <td>
                <div class="report-title">Petty Cash Report: {{ $petty->name }}</div>
                <div class="company-name">
                    {{ strtoupper($settings['business_name'] ?? '') }} -
                    @if(Auth::user()->branch_id == 1)
                        HEAD OFFICE - DAR ES SALAAM
                    @elseif(Auth::user()->branch_id == 2)
                        TABORA BRANCH
                    @elseif(Auth::user()->branch_id == 3)
                        DODOMA BRANCH
                    @else
                        {{ strtoupper($settings['address'] ?? '') }}
                    @endif
                </div>
            </td>
            <td class="date-range">
                <span class="label">Period</span>
                <span class="value">{{ $startDateRange }} &nbsp;to&nbsp; {{ $endDateRange }}</span>
            </td>
        </tr>
    </table>

    <table class="petty-table">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:40%;">Expense Name</th>
                <th style="width:20%;">Paid To</th>
                <th style="width:15%;">Voucher No.</th>
                <th style="width:20%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($petty_cash as $key => $p)
                <tr>
                    <td style="text-align:center;">{{ $key + 1 }}</td>
                    <td>{{ strtoupper($p->note ?? '') }}</td>
                    <td>{{ $p->paid_to ?? '' }}</td>
                    <td>{{ $p->voucher_no ?? '' }}</td>
                    <td class="num text-right">{{ number_format($p->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Total Amount</td>
                <td class="num text-right">
                    {{ number_format($total_amounts[0]->total_amount, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

@endif

</body>
</html>