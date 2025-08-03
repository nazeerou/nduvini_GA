<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PAYE Payment Voucher</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 20px;
        }
        .header, .footer {
            text-align: center;
        }
        .voucher-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .voucher-table th, .voucher-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .voucher-table th {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 20px;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature {
            display: inline-block;
            width: 30%;
            text-align: center;
            margin-right: 5%;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2><strong>{{ $company['name'] }}</strong></h2>
        <p>{{ $company['address'] }} | {{ $company['phone'] }} | TIN: {{ $company['tin'] }}</p>
        <h3>PAYE PAYMENT VOUCHER</h3>
        <p><strong>Month:</strong> {{ $month }} &nbsp;&nbsp; <strong>TRA TIN:</strong> {{ $company['tin'] }}</p>
    </div>

    <table class="voucher-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Gross Salary</th>
                <th>Taxable Income</th>
                <th>PAYE Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_paye = 0;
            @endphp

            @foreach ($employees as $index => $emp)
                @php
                    $taxable_income = $emp['gross_salary']; // adjust if needed
                    $paye = $emp['paye']; // pre-calculated in controller
                    $total_paye += $paye;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $emp['name'] }}</td>
                    <td>{{ number_format($emp['gross_salary'], 2) }}</td>
                    <td>{{ number_format($taxable_income, 2) }}</td>
                    <td>{{ number_format($paye, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">TOTAL</th>
                <th>{{ number_format($total_paye, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <p><strong>Total Employees:</strong> {{ count($employees) }}</p>
        <p><strong>Total PAYE to TRA:</strong> TZS {{ number_format($total_paye, 2) }}</p>
        <p><strong>Payment Method:</strong> ____________________</p>
        <p><strong>Control Number:</strong> ____________________</p>
    </div>

    <div class="signature-section">
        <div class="signature">
            <p>Prepared By</p><br><br>
            _________________________<br>
            Name & Signature
        </div>

        <div class="signature">
            <p>Approved By</p><br><br>
            _________________________<br>
            Name & Signature
        </div>

        <div class="signature">
            <p>Received By (TRA)</p><br><br>
            _________________________<br>
            Name & Stamp
        </div>
    </div>

</body>
</html>
