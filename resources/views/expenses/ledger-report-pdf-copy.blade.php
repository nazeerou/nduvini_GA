<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ledger Report</title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header-table {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .header-table td {
            vertical-align: bottom;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
        }
        .date-range {
            font-size: 12px;
            text-align: right;
            font-weight: bold;
        }
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
                        border: 1px solid #ddd;

        }
        .ledger-table th {
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            padding: 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .ledger-table th.text-right {
            text-align: right;
        }
        .ledger-table td {
            padding: 5px 6px;
            vertical-align: top;
            border: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .detail-row td {
            font-size: 10px;
            color: #444;
            /* border-bottom: 1px solid #ddd; */
            
        }
        .total-row td {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            font-weight: bold;
            padding: 8px 6px;
        }
         .total-row_ td {
            border-top: 2px solid #e0d6d6;
            border-bottom: 2px solid #dfcece;
            font-weight: bold;
            padding: 8px 6px;
        }
    </style>
</head>
<body>
 @php
    $closingBalance = $closingBalance ?? (
        $openingBalance + $totalDebit - $totalCredit
    );        
        // Make the final totals balance (add the closing balance to the opposite side)
        $finalDebit = $totalDebit + ($closingBalance < 0 ? abs($closingBalance) : 0);
        $finalCredit = $totalCredit + ($closingBalance > 0 ? $closingBalance : 0);
    @endphp
    <table class="header-table">
        <tr>
            <td>
                <div class="report-title">
                    Ledger: {{ $accountId ? \App\Models\Account::find($accountId)->name : 'Petty Cash' }}
                </div>
                <div style="font-size: 10px; color: #666; margin-top: 3px;">
                    {{ strtoupper($settings['business_name'] ?? '') }}
                </div>
            </td>
            <td class="date-range">
                {{ request('startdate') ? \Carbon\Carbon::parse(request('startdate'))->format('d-M-Y') : 'Start' }} 
                to 
                {{ request('enddate') ? \Carbon\Carbon::parse(request('enddate'))->format('d-M-Y') : 'End' }}
            </td>
        </tr>
    </table>

    <table class="ledger-table">
        <thead>
            <tr>
                <th style="width: 12%;">Date</th>
                <th style="width: 25%;">Particulars</th>
                <th style="width: 23%;"></th>
                <th style="width: 20%;">Vch Type</th>
                <th class="text-right" style="width: 15%;">Debit</th>
                <th class="text-right" style="width: 15%;">Credit</th>
            </tr>
        </thead>
        <tbody>
        
            @forelse($ledger as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ $row['particulars'] }}</td>
                    <td>{{ $row['vch_type'] }}</td>
                    <td class="text-right">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                    <td class="text-right">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                </tr>

                {{-- Details Sub-rows --}}
                @if(count($row['details']) > 0)
                    @foreach($row['details'] as $detail)
                        <tr class="detail-row">
                            <td></td>
                            <td style="padding-left: 15px;">{{ $detail['category'] }}</td>
                             <td></td>
                            <td class="text-right">{{ number_format($detail['amount'], 2) }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #888;">No records found.</td>
                </tr>
            @endforelse

             @if(count($ledger) > 0)
            <tfoot>
                <tr class="total-row_">
                    <td colspan="4" class="text-right"></td>
                    <td class="text-right">{{ number_format($finalDebit, 2) }}</td>
                    <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                </tr>
            </tfoot>
        @endif
        
             {{-- Closing Balance Row --}}
     @if(count($ledger) > 0 && $closingBalance != 0)
     <tr class="total-row">
    <td>{{ \Carbon\Carbon::parse(request('enddate'))->format('d/m/Y') }}</td>
    <td>By</td>
    <td>Closing Balance</td>
    <td></td>

    <td class="text-right">
        {{ $closingBalance < 0
            ? number_format(abs($closingBalance), 2)
            : '' }}
    </td>

    <td class="text-right">
        {{ $closingBalance > 0
            ? number_format($closingBalance, 2)
            : '' }}
    </td>
</tr>
            @endif
        </tbody>
        @if(count($ledger) > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                    <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

</body>
</html>

