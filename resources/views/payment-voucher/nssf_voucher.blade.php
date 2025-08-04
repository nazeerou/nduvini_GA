<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NSSF Payment Voucher</title>
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
            /* text-decoration: underline; */
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
            background-color: #f2f2f2;
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
        <div class="voucher-title">NSSF PAYMENT VOUCHER</div>
        <p><strong>Month:</strong> {{ $month }} &nbsp;&nbsp; <strong>NSSF Number:</strong> {{ $company['nssf_number'] }}</p>
    </div>

    <table class="voucher-table">
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
            <p>Received By (NSSF)</p>
            <span class="signature-line"></span>
            Name & Stamp
        </div>
    </div>

</body>
</html>
