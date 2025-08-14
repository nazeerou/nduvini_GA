<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Employee Loans</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: left; }
        th { background: #eee; }
        h2 { text-align: center; }
        .branch { text-align: center; }
        .total-row { font-weight: bold; background-color: #f0f0f0; }
    </style>
</head>
<body>

    <h2>NDUVINI AUTO WORKS (NAW)</h2>
    <div class="branch">
        @if(Auth::user()->branch_id == 1)
            HEAD OFFICE : Dar es Salaam 
        @elseif(Auth::user()->branch_id == 2)
            BRANCH : TABORA 
        @elseif(Auth::user()->branch_id == 3)
            BRANCH : DODOMA 
        @endif
    </div>

    <h3>Employee Loans</h3>
    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Employee</th>
                <th>Loan Type</th>
                <th>Loan Amount (TZS)</th>
                <th>Monthly Deduction (TZS)</th>
                <th>Outstanding Balance (TZS)</th>
                <th>Start Date</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalLoan = 0;
                $totalMonthly = 0;
                $totalOutstanding = 0;
            @endphp

            @foreach($loans as $key => $loan)
                @php
                    $totalLoan += $loan->principal;
                    $totalMonthly += $loan->monthly_deduction;
                    $totalOutstanding += $loan->balance;
                @endphp
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $loan->employee->firstname }} {{ $loan->employee->surname }}</td>
                    <td>{{ $loan->type }}</td>
                    <td>{{ number_format($loan->principal, 2) }}</td>
                    <td>{{ number_format($loan->monthly_deduction, 2) }}</td>
                    <td>{{ number_format($loan->balance, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($loan->start_date)->format('M Y') }}</td>
                </tr>
            @endforeach

            {{-- Totals row --}}
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL</td>
                <td>{{ number_format($totalLoan, 2) }}</td>
                <td>{{ number_format($totalMonthly, 2) }}</td>
                <td>{{ number_format($totalOutstanding, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
