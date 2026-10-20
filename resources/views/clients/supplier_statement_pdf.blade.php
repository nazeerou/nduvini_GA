<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Supplier Statement') }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        #sales_report td, #sales_report th {
            border: 1px solid #ddd;
            padding: 6px 8px;
        }
        #sales_report th {
            background-color: #eee;
        }

        /* ---- page-break control ---- */
        #sales_report thead { display: table-header-group; }
        #sales_report tr    { page-break-inside: avoid; }

        /* ---- repeating header ---- */
        header {
            position: fixed;
            top: -270px;                 /* 280 - 270 = 10px from page top */
            width: 100%;
            height: auto;                /* auto — do not clip content */
        }
        header img {
            height: 130px;
            width: 100%;
        }
        /* header hr { margin: 4px 0; } */
        header table,
        header table td { border: none; padding: 2px 0; }
        header p { margin: 2px 0; }

        /* ---- repeating footer ---- */
        footer {
            position: fixed;
            bottom: -90px;               /* -margin-bottom − 10 */
            height: auto;
            width: 100%;
            text-align: right;
            font-size: 13px;
        }

        .page-number:before { content: counter(page); }

        /* @page margin-top must cover the FULL header height (~250px) */
        @page {
            margin: 280px 25px 100px 25px;
        }

        main {
            margin-top: 0;               /* the @page margin handles the gap */
        }

        .page-break { page-break-before: always; }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .outstanding {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 8px;
        }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('assets/images/nduvini_header.png') }}" alt="Header Image">
    <hr>
    <table>
        <tr>
            <td width="70%">
                @if (count($purchases))
                    <p>{{ $purchases[0]->supplier_name ?? '' }}</p>
                    <p>{{ $purchases[0]->address ?? '' }}</p>
                    <p>{{ $purchases[0]->place ?? '' }}</p>
                @endif
            </td>
            <td>
                <p><strong>STATEMENT</strong></p>
                <p>Period:
                    @if ($startdate == '' && $enddate == '')
                        ALL
                    @else
                        {{ $startdate }} TO {{ $enddate }}
                    @endif
                </p>
                <p>Page: <span class="page-number"></span></p>
            </td>
        </tr>
    </table>
</header>

<main>
    <table id="sales_report">
        <thead>
            <tr>
                <th>Date</th>
                <th>Doc. No</th>
                <th>Reference</th>
                <th>Invoice Amount</th>
                @if ($payment == '1')
                    <th>Credit</th>
                @elseif ($payment == '')
                    <th>Debit</th>
                    <th>Credit</th>
                @else
                    <th>Debit</th>
                @endif
                <th>Balance</th>
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
                    <td>PI {{ $s->invoice_number }}</td>
                    <td>{{ $s->lpo_number }}</td>
                    <td>{{ number_format($invoiceAmount, 2) }}</td>

                    @if ($payment == '1')
                        <td>{{ number_format($creditAmount, 2) }}</td>
                        <td>{{ number_format($balance, 2) }}</td>
                    @elseif ($payment == '')
                        <td>{{ number_format($debitAmount, 2) }}</td>
                        <td>{{ number_format($creditAmount, 2) }}</td>
                        <td>{{ number_format($balance, 2) }}</td>
                    @else
                        <td>{{ number_format($debitAmount, 2) }}</td>
                        <td>{{ number_format($balance, 2) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding-top: 20px;">No Data Found</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL</td>
                <td>{{ number_format($totalInvoice, 2) }}</td>

                @if ($payment == '1')
                    <td>{{ number_format($totalCredit, 2) }}</td>
                    <td>{{ number_format($totalBalance, 2) }}</td>
                @elseif ($payment == '')
                    <td>{{ number_format($totalDebit, 2) }}</td>
                    <td>{{ number_format($totalCredit, 2) }}</td>
                    <td>{{ number_format($totalBalance, 2) }}</td>
                @else
                    <td>{{ number_format($totalDebit, 2) }}</td>
                    <td>{{ number_format($totalBalance, 2) }}</td>
                @endif
            </tr>
        </tbody>
    </table>
</main>

<footer>
    <p class="outstanding">
        Outstanding Balance: {{ number_format($totalBalance, 2) }}
    </p>
</footer>

</body>
</html>