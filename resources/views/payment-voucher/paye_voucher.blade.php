<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PAYE Payment Voucher</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 5px 0;
        }

        .logo img {
            width: 100px;
            height: auto;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 4px 0;
        }

        .voucher-title {
            margin-top: 10px;
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
        }

        table.voucher-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.voucher-table th,
        table.voucher-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table.voucher-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        tfoot th {
            background-color: #f9f9f9;
            text-align: right;
        }

        .summary {
            margin-top: 20px;
        }

        .summary p {
            margin: 4px 0;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 30%;
            text-align: center;
            font-size: 12px;
        }

        .signature p {
            margin-bottom: 40px;
        }

        .signature-line {
            display: block;
            border-top: 1px solid #000;
            margin-top: 30px;
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2><strong>{{ strtoupper($company['name']) }}</strong></h2>
        <div class="logo">
            <img src="{{ public_path('assets/images/logo_nduvini.jpeg') }}" alt="Company Logo">
        </div>
        <p>{{ $company['address'] }} | {{ $company['phone'] }} | TIN: {{ $company['tin'] }}</p>
        <div class="voucher-title">PAYE PAYMENT VOUCHER</div>
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
            @php $total_paye = 0; @endphp
            @foreach ($employees as $index => $emp)
                @php
                    $taxable_income = $emp['gross_salary'];
                    $paye = $emp['paye'];
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
                <th colspan="4">TOTAL</th>
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
            <p>Prepared By</p>
            <span class="signature-line"></span>
            Name & Signature
        </div>
        <div class="signature">
            <p>Approved By</p>
            <span class="signature-line"></span>
            Name & Signature
        </div>
        <div class="signature">
            <p>Received By (TRA)</p>
            <span class="signature-line"></span>
            Name & Stamp
        </div>
    </div>

</body>
</html>
