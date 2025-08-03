<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NSSF Payment Voucher</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 20px;
        }
        .header, .footer {
            text-align: center;
        }
        .voucher-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .voucher-box th, .voucher-box td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .voucher-box th {
            background-color: #f2f2f2;
        }
        .summary {
            margin-top: 20px;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature {
            display: inline-block;
            width: 30%;
            text-align: center;
            margin-right: 10%;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2><strong>{{ $company['name'] }}</strong></h2>
        <p>{{ $company['address'] }} | {{ $company['phone'] }} | TIN: {{ $company['tin'] }}</p>
        <h3>NSSF PAYMENT VOUCHER</h3>
        <p><strong>Month:</strong> {{ $month }} &nbsp;&nbsp; <strong>NSSF Number:</strong> {{ $company['nssf_number'] }}</p>
    </div>

    <table class="voucher-box">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>NSSF No.</th>
                <th>Gross Salary</th>
                <th>Employee Contribution (10%)</th>
                <th>Employer Contribution (10%)</th>
                <th>Total Contribution</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_employee = 0;
                $total_employer = 0;
            @endphp

            @foreach ($employees as $index => $emp)
                @php
                    $emp_contrib = $emp['gross_salary'] * 0.10;
                    $empr_contrib = $emp['gross_salary'] * 0.10;
                    $total_employee += $emp_contrib;
                    $total_employer += $empr_contrib;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $emp['name'] }}</td>
                    <td>{{ $emp['nssf_no'] }}</td>
                    <td>{{ number_format($emp['gross_salary'], 2) }}</td>
                    <td>{{ number_format($emp_contrib, 2) }}</td>
                    <td>{{ number_format($empr_contrib, 2) }}</td>
                    <td>{{ number_format($emp_contrib + $empr_contrib, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">TOTAL</th>
                <th>{{ number_format($total_employee, 2) }}</th>
                <th>{{ number_format($total_employer, 2) }}</th>
                <th>{{ number_format($total_employee + $total_employer, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <p><strong>Total Employees:</strong> {{ count($employees) }}</p>
        <p><strong>Total Payment to NSSF:</strong> TZS {{ number_format($total_employee + $total_employer, 2) }}</p>
        <p><strong>Payment Method:</strong> ________________________</p>
        <p><strong>Reference No:</strong> ________________________</p>
    </div>

    <div class="signature-section">
        <div class="signature">
            <p>Prepared By</p><br><br>
            _______________________<br>
            Name & Signature
        </div>

        <div class="signature">
            <p>Approved By</p><br><br>
            _______________________<br>
            Name & Signature
        </div>

        <div class="signature">
            <p>Received By (NSSF)</p><br><br>
            _______________________<br>
            Name & Stamp
        </div>
    </div>

</body>
</html>
