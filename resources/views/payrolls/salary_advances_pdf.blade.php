<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary Advances</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 6px; text-align: left; }
        th { background: #eee; }
        .branch { text-align: center; }
        h2 { text-align: center; }
        .total-row { font-weight: bold; background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>NDUVINI AUTO WORKS (NAW)</h2>
    <div class="branch"> 
        @if(Auth::user()->branch_id == 1)
            HEAD OFFICE : Dar es Salaam 
        @elseif (Auth::user()->branch_id == 2)
            BRANCH : TABORA 
        @elseif (Auth::user()->branch_id == 3)
            BRANCH : DODOMA 
        @endif
    </div>
    
    <h3>Employee Salary Advances</h3>
    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Employee</th>
                <th>Month</th>
                <th>Amount (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp
            @foreach($salary_advances as $key => $advance)
                @php
                    $totalAmount += $advance->amount;
                @endphp
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>
                        @if ($advance->employee)
                            {{ $advance->employee->firstname }} {{ $advance->employee->surname }}
                        @else
                            <em>No employee</em>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($advance->month . '-01')->format('M Y') }}</td>
                    <td>{{ number_format($advance->amount, 2) }}</td>
                </tr>
            @endforeach

            {{-- Totals row --}}
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
