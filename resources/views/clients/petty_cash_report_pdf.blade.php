
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Petty Cash Ledger</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 25px 30px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            width: 100%;
            border-bottom: 1px solid #aaa;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header td {
            border: none;
            padding: 4px;
        }

        .report-title {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            margin: 12px 0 5px;
        }

        .report-period {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }

        table.ledger {
            width: 100%;
            border-collapse: collapse;
        }

        .ledger th {
            background: #eee;
            border: 1px solid #ccc;
            padding: 7px 5px;
            text-align: left;
        }

        .ledger td {
            border: 1px solid #ddd;
            padding: 6px 5px;
            vertical-align: top;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
        }

        .date {
            width: 95px;
            white-space: nowrap;
        }

        .particulars {
            width: 43%;
        }

        .voucher {
            width: 18%;
        }

        .opening,
        .totals {
            font-weight: bold;
            background: #f4f4f4;
        }

        .closing {
            font-weight: bold;
            background: #e9e9e9;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
            text-align: right;
        }
    </style>
</head>

<body>

<table class="header">
    <tr>
        <td>
            <strong>Ledger:</strong>
            Petty Cash
            @if($petty)
                - {{ $petty->name }}
            @else
                - ALL
            @endif
        </td>
        <td style="text-align:right;">
            <strong>
                {{ \Carbon\Carbon::parse($startdate)->format('j-M-Y') }}
                to
                {{ \Carbon\Carbon::parse($enddate)->format('j-M-Y') }}
            </strong>
        </td>
    </tr>
</table>

<div class="report-title">PETTY CASH LEDGER</div>

<div class="report-period">
    {{ $settings->business_name ?? '' }}
    @if(!empty($settings->address))
        | {{ $settings->address }}
    @endif
</div>

<table class="ledger">
    <thead>
        <tr>
            <th class="date">Date</th>
            <th class="particulars">Particulars</th>
            <th class="voucher">Vch Type / Voucher No.</th>
            <th class="amount">Debit</th>
            <th class="amount">Credit</th>
        </tr>
    </thead>

    <tbody>
        <tr class="opening">
            <td>
                {{ \Carbon\Carbon::parse($startdate)->format('d/m/Y') }}
            </td>
            <td>To Opening Balance</td>
            <td></td>
            <td class="amount">
                {{ number_format(max(0, $openingBalance), 2) }}
            </td>
            <td class="amount">
                {{ number_format(max(0, -$openingBalance), 2) }}
            </td>
        </tr>

        @forelse($petty_cash as $transaction)
            <tr>
                <td>
                    {{ \Carbon\Carbon::parse(
                        $transaction->transaction_date
                    )->format('d/m/Y') }}
                </td>

                <td>
                    @if($transaction->credit > 0)
                        By
                    @else
                        To
                    @endif

                    {{ $transaction->particulars }}
                </td>

                <td>
                    {{ $transaction->voucher_type ?? '' }}

                    @if($transaction->voucher_no)
                        <br>
                        {{ $transaction->voucher_no }}
                    @endif
                </td>

                <td class="amount">
                    {{ $transaction->debit > 0
                        ? number_format($transaction->debit, 2)
                        : '' }}
                </td>

                <td class="amount">
                    {{ $transaction->credit > 0
                        ? number_format($transaction->credit, 2)
                        : '' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;">
                    No transactions found for this period.
                </td>
            </tr>
        @endforelse

        <tr class="totals">
            <td colspan="3" class="amount">
                Period Totals
            </td>
            <td class="amount">
                {{ number_format($totalDebit, 2) }}
            </td>
            <td class="amount">
                {{ number_format($totalCredit, 2) }}
            </td>
        </tr>

        <tr class="closing">
            <td colspan="3">
                Closing Balance
            </td>
            <td colspan="2" class="amount">
                {{ number_format($closingBalance, 2) }}
            </td>
        </tr>
    </tbody>
</table>

<div class="footer">
    Generated on {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
