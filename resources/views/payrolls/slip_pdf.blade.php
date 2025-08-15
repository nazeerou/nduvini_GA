<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $slip['employee_name'] }}</title>
    <style>
        @page {
            margin-top: 0;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            background-color: #f0f2f5;
            color: #222;
        }

        .watermark {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            z-index: 0;
            width: 350px;
            height: auto;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 750px;
            margin: 10px auto;
            background: #fff;
            padding: 30px 35px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 700;
            color: #1e1e1e;
        }

        .title {
            font-size: 18px;
            font-weight: 600;
            color: #444;
            margin-top: 3px;
        }

        .info {
            /* margin-bottom: 20px; */
            padding: 15px;
            background-color: #f7f7f7;
            border-left: 4px solid rgb(255, 106, 0);
        }

        .info p {
            margin: 4px 0;
        }

        .info strong {
            display: inline-block;
            width: 160px;
            color: #333;
        }

        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-top: 25px;
            margin-bottom: 10px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 4px 14px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            color: #333;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        tr:last-child {
            background-color: #e9f7ef;
            font-weight: bold;
            color: #155724;
        }

        .footer {
            text-align: right;
            margin-top: 30px;
            font-style: italic;
            color: #666;
            font-size: 12px;
        }

        @media print {
            body {
                background: none;
            }
            .container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>

    {{-- Watermark Logo --}}
    @if(file_exists(public_path('attachments/logo_nduvini.jpeg')))
        <img src="{{ public_path('attachments/logo_nduvini.jpeg') }}" class="watermark" alt="Watermark">
    @endif

    <div class="container">
        <div class="header">
            <div class="company-name">{{ $settings->business_name ?? 'Company Name' }}</div>
            <div class="title">Salary Slip</div>
            <div>Month: {{ \Carbon\Carbon::parse($month)->format('F Y') ?? '' }}</div>
        </div>

        <div class="info">
            <p><strong>Employee Name:</strong> {{ $slip['employee_name'] }}</p>
            <p><strong>Employee ID:</strong> NAW{{ str_pad($slip['employee_id'], 3, '0', STR_PAD_LEFT) }}</p>
        </div>

        <table>
            <tr><th>Basic Salary</th><td>{{ number_format($slip['basic_salary'], 2) }}</td></tr>
            <tr><th>Allowance</th><td>{{ number_format($slip['allowance'], 2) }}</td></tr>
            <tr><th>Salary Advance</th><td>{{ number_format($slip['salary_advance'], 2) }}</td></tr>
            <tr><th>Loan</th><td>{{ number_format($slip['loan'], 2) }}</td></tr>
            <tr><th>NSSF</th><td>{{ number_format($slip['nssf'], 2) }}</td></tr>
            <tr><th>NHIF</th><td>{{ number_format($slip['nhif'], 2) }}</td></tr>
            <tr><th>TUICO</th><td>{{ number_format($slip['tuico'], 2) }}</td></tr>
            <tr><th>PAYE</th><td>{{ number_format($slip['paye'], 2) }}</td></tr>
            <tr><th>Total Deduction</th>
            <td>{{ number_format($slip['paye'] + $slip['nssf'] + $slip['tuico'] + $slip['nhif'], 2) }}</td></tr>
            <tr><th><strong>Net Salary</strong></th><td><strong>{{ number_format($slip['net_salary'], 2) }}</strong></td></tr>
        </table>
    </div>

    <div class="footer">
        Generated on: {{ \Carbon\Carbon::now()->format('d M Y') }}
    </div>
</body>
</html>
