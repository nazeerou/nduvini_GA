<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WCF Payment Voucher</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
        }
        .header, .footer {
            text-align: center;
        }
        .content {
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }
        .logo {
            width: 80px;
        }
        .right {
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
    <h2>NDUVINI AUTO WORKS LIMITED (NAW) </h2>
        <!-- <img src="{{ public_path('logo.png') }}" class="logo" alt="Company Logo"> -->
        <img src="{{ public_path('assets/images/logo_nduvini.jpeg') }}" alt="Company Logo" style="width: 150px;">

        <h2>WCF PAYMENT VOUCHER</h2>
        <p>Workers Compensation Fund Contribution</p>
    </div>

    <div class="content">
        <table>
            <tr>
                <td><strong>Voucher No:</strong> WC73647474</td>
                <td class="right"><strong>Date:</strong> 2025-02</td>
            </tr>
        </table>

        <br>

        <table border="1" width="100%" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>NIDA</th>
            <th>Gross Salary (TZS)</th>
            <th>WCF 1% (TZS)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_gross = 0;
            $total_wcf = 0;
            $total_net = 0;
        @endphp

        @foreach ($employees as $index => $emp)
            @php
                $total_gross += $emp['gross_salary'];
                $total_wcf += $emp['wcf'];
                $total_net += $emp['net_after_wcf'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $emp['name'] }}</td>
                <td>{{ $emp['nida'] }}</td>
                <td style="text-align: right;">{{ number_format($emp['gross_salary'], 2) }}</td>
                <td style="text-align: right;">{{ number_format($emp['wcf'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background-color: #f0f0f0;">
            <td colspan="3" style="text-align: right;">TOTAL</td>
            <td style="text-align: right;">{{ number_format($total_gross, 2) }}</td>
            <td style="text-align: right;">{{ number_format($total_wcf, 2) }}</td>
        </tr>
    </tfoot>
</table>


        <br><br>
        <p><strong>Prepared By:</strong> </p>
        <p><strong>Approved By:</strong> __________________________</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ $company->name ?? 'Your Company Name' }}</p>
    </div>
</body>
</html>
