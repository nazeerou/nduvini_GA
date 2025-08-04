<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NHIF Payment Voucher</title>
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
    </style>
</head>
<body>

    <div class="header">
        <h2><strong>{{ strtoupper($company['name']) }}</strong></h2>
        <div class="logo">
            <img src="{{ public_path('assets/images/logo_nduvini.jpeg') }}" alt="Company Logo">
        </div>
        <p>{{ $company['address'] }} | {{ $company['phone'] }} | TIN: {{ $company['tin'] }}</p>
        <h2>NHIF PAYMENT VOUCHER</h2>
        <p><strong>Month:</strong> {{ $month }} &nbsp;&nbsp; <strong>NHIF Number:</strong> {{ $company['nhif_number'] }}</p>
    </div>

    <table class="voucher-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>NHIF No.</th>
                <th>Gross Salary</th>
                <th>NHIF Contribution (3%)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_contribution = 0;
            @endphp

            @foreach ($employees as $index => $emp)
                @php
                    $nhif = $emp['gross_salary'] * 0.03;
                    $total_contribution += $nhif;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $emp['name'] }}</td>
                    <td>{{ $emp['nhif_no'] }}</td>
                    <td>{{ number_format($emp['gross_salary'], 2) }}</td>
                    <td>{{ number_format($nhif, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">TOTAL</th>
                <th>{{ number_format($total_contribution, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <p><strong>Total Employees:</strong> {{ count($employees) }}</p>
        <p><strong>Total NHIF Payment:</strong> TZS {{ number_format($total_contribution, 2) }}</p>
        <p><strong>Payment Method:</strong> ____________________</p>
        <p><strong>Payment Ref No:</strong> ____________________</p>
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
            <p>Received By (NHIF)</p><br><br>
            _________________________<br>
            Name & Stamp
        </div>
    </div>

</body>
</html>
