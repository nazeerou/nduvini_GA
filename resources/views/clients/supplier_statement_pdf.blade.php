<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Supplier Statement') }}</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #2c3e50;
            line-height: 1.4;
        }

        table { width: 100%; border-collapse: collapse; }

        /* ============ TABLE ============ */
        #sales_report {
            border: 1px solid #d5dbe0;
            margin-top: 8px;
        }
        #sales_report td, #sales_report th {
            padding: 7px 9px;
            border-bottom: 1px solid #e6eaed;
            text-align: left;
        }
        /* Right-align numeric columns */
        #sales_report td.num,
        #sales_report th.num { text-align: right; }

        #sales_report thead th {
            background-color: #1f3a5f;
            color: #ffffff;
            font-size: 11.5px;
            font-weight: bold;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            border-bottom: 2px solid #14273f;
            padding: 9px 9px;
        }
        #sales_report tbody tr:nth-child(even) td {
            background-color: #f6f8fa;
        }

        /* Total row */
        .total-row td {
            font-weight: bold;
            background-color: #eaf0f6 !important;
            color: #1f3a5f;
            border-top: 2px solid #1f3a5f;
            border-bottom: 2px solid #1f3a5f;
            font-size: 12.5px;
        }

        /* ============ PAGE-BREAK ============ */
        #sales_report thead { display: table-header-group; }
        #sales_report tr    { page-break-inside: avoid; }

        /* ============ HEADER (repeats) ============ */
        header {
            position: fixed;
            top: -270px;
            width: 100%;
            height: auto;
        }
        header img {
            height: 130px;
            width: 100%;
        }
        header .header-line {
            border: 0;
            border-top: 2px solid #1f3a5f;
            border-bottom: 1px solid #1f3a5f;
            height: 3px;
            margin: 6px 0 10px 0;
        }
        header table,
        header table td { border: none; padding: 2px 0; vertical-align: top; }
        header p { margin: 3px 0; }

        .supplier-name {
            font-size: 15px;
            font-weight: bold;
            color: #1f3a5f;
            letter-spacing: 0.3px;
        }
        .supplier-detail { color: #4a5a6a; font-size: 12px; }

        .stmt-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f3a5f;
            letter-spacing: 2px;
            text-align: right;
            margin: 0 0 4px 0;
        }
        .stmt-meta {
            text-align: right;
            font-size: 11.5px;
            color: #4a5a6a;
            margin: 2px 0;
        }
        .stmt-meta strong { color: #1f3a5f; }

        /* ============ FOOTER (repeats) ============ */
        footer {
            position: fixed;
            bottom: -90px;
            height: auto;
            width: 100%;
            font-size: 12px;
        }
        footer .footer-line {
            border: 0;
            border-top: 1px solid #d5dbe0;
            margin: 0 0 6px 0;
        }
        footer .footer-inner td { border: none; vertical-align: middle; }
        footer .page-label {
            text-align: left;
            color: #7a8a9a;
            font-size: 10.5px;
        }
        footer .page-number:before { content: counter(page); }

        .outstanding {
            text-align: right;
            font-size: 15px;
            font-weight: bold;
            color: #ffffff;
            background-color: #1f3a5f;
            padding: 8px 14px;
            letter-spacing: 0.3px;
        }
        .outstanding .label {
            font-weight: normal;
            font-size: 11px;
            color: #c8d4e0;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-right: 8px;
        }

        /* ============ @PAGE ============ */
        @page {
            margin: 280px 25px 100px 25px;
        }

        main { margin-top: 0; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

@php
    $showAllSuppliers  = empty($supplierId);
    $totalLabelColspan = $showAllSuppliers ? 4 : 3;   // Date + Doc + Ref (+ Supplier)
    $totalCols         = 5 + ($showAllSuppliers ? 1 : 0); // Date, Doc, Ref, Invoice, Balance (+Supplier)
    if ($payment == '1' || $payment == '2') {
        $totalCols += 1;
    } elseif ($payment == '') {
        $totalCols += 2;
    } else {
        $totalCols += 1;
    }
@endphp

<header>
    <img src="{{ public_path('assets/images/nduvini_header.png') }}" alt="Header Image">
    <hr class="header-line">
    <table>
        <tr>
            <td width="65%">
                @if ($showAllSuppliers)
                    <p class="supplier-name">ALL SUPPLIERS</p>
                    <p class="supplier-detail">Consolidated Statement</p>
                    <p class="supplier-detail">{{ count($purchases) }} transaction line(s)</p>
                @elseif (count($purchases))
                    <p class="supplier-name">{{ $purchases[0]->supplier_name ?? '' }}</p>
                    <p class="supplier-detail">{{ $purchases[0]->address ?? '' }}</p>
                    <p class="supplier-detail">{{ $purchases[0]->place ?? '' }}</p>
                @else
                    <p class="supplier-name">No Records</p>
                @endif
            </td>
            <td width="35%">
                <p class="stmt-title">STATEMENT</p>
                <p class="stmt-meta">
                    <strong>Period:</strong>
                    @if ($startdate == '' && $enddate == '')
                        ALL
                    @else
                        {{ $startdate }} &nbsp;–&nbsp; {{ $enddate }}
                    @endif
                </p>
                <p class="stmt-meta">
                    <strong>Page:</strong> <span class="page-number"></span>
                </p>
            </td>
        </tr>
    </table>
</header>

<main>
    <table id="sales_report">
        <thead>
            <tr>
                <th>Date</th>
                @if ($showAllSuppliers)
                    <th>Supplier</th>
                @endif
                <th>Doc. No</th>
                <th>Reference</th>
                <th class="num">Invoice Amount</th>
                @if ($payment == '1')
                    <th class="num">Credit</th>
                @elseif ($payment == '')
                    <th class="num">Debit</th>
                    <th class="num">Credit</th>
                @else
                    <th class="num">Debit</th>
                @endif
                <th class="num">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalInvoice = 0;
                $totalDebit   = 0;
                $totalCredit  = 0;
                $totalBalance = 0;
            @endphp

            @forelse ($purchases as $s)
                @php
                    $invoiceAmount = $s->total_amount;
                    $creditAmount  = $s->paid_amount;
                    $debitAmount   = max(0, $s->total_amount - $s->paid_amount);
                    $balance       = $s->total_amount - $s->paid_amount;

                    $totalInvoice += $invoiceAmount;
                    if ($payment == '1') {
                        $totalCredit += $creditAmount;
                    } elseif ($payment == '') {
                        $totalDebit  += $debitAmount;
                        $totalCredit += $creditAmount;
                    } else {
                        $totalDebit += $debitAmount;
                    }
                    $totalBalance += $balance;
                @endphp

                <tr>
                    <td>{{ $s->created_date }}</td>
                    @if ($showAllSuppliers)
                        <td>{{ $s->supplier_name }}</td>
                    @endif
                    <td>PI {{ $s->invoice_number }}</td>
                    <td>{{ $s->lpo_number }}</td>
                    <td class="num">{{ number_format($invoiceAmount, 2) }}</td>

                    @if ($payment == '1')
                        <td class="num">{{ number_format($creditAmount, 2) }}</td>
                        <td class="num">{{ number_format($balance, 2) }}</td>
                    @elseif ($payment == '')
                        <td class="num">{{ number_format($debitAmount, 2) }}</td>
                        <td class="num">{{ number_format($creditAmount, 2) }}</td>
                        <td class="num">{{ number_format($balance, 2) }}</td>
                    @else
                        <td class="num">{{ number_format($debitAmount, 2) }}</td>
                        <td class="num">{{ number_format($balance, 2) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $totalCols }}" style="text-align: center; padding: 24px; color: #7a8a9a; font-style: italic;">
                        No Data Found
                    </td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="{{ $totalLabelColspan }}" style="text-align: right;">TOTAL</td>
                <td class="num">{{ number_format($totalInvoice, 2) }}</td>

                @if ($payment == '1')
                    <td class="num">{{ number_format($totalCredit, 2) }}</td>
                    <td class="num">{{ number_format($totalBalance, 2) }}</td>
                @elseif ($payment == '')
                    <td class="num">{{ number_format($totalDebit, 2) }}</td>
                    <td class="num">{{ number_format($totalCredit, 2) }}</td>
                    <td class="num">{{ number_format($totalBalance, 2) }}</td>
                @else
                    <td class="num">{{ number_format($totalDebit, 2) }}</td>
                    <td class="num">{{ number_format($totalBalance, 2) }}</td>
                @endif
            </tr>
        </tbody>
    </table>
</main>

<footer>
    <hr class="footer-line">
    <table class="footer-inner">
        <tr>
            <td class="page-label" width="50%">
                Generated on {{ date('d M Y, H:i') }}
            </td>
            <td width="50%">
                <div class="outstanding">
                    <span class="label">
                        @if ($showAllSuppliers)
                            Grand Outstanding Balance
                        @else
                            Outstanding Balance
                        @endif
                    </span>
                    {{ number_format($totalBalance, 2) }}
                </div>
            </td>
        </tr>
    </table>
</footer>

</body>
</html>