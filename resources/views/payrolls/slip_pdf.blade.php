<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $slip['employee_name'] }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            background-color: #f9f9f9;
        }

        .watermark {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            z-index: 0;
            width: 400px;
            height: auto;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 30px 40px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #222;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 4px 0;
        }

        .info strong {
            display: inline-block;
            width: 140px;
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
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }

        .footer {
            text-align: right;
            padding: 15px 0;
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
            <p><strong>Reference:</strong> {{ $slip['reference'] }}</p>
        </div>

        <div class="section-title">Salary Breakdown</div>
        <table>
            <tr>
                <th>Basic Salary</th>
                <td>{{ $slip['basic_salary'] }}</td>
            </tr>
            <tr>
                <th>Allowance</th>
                <td>{{ $slip['allowance'] }}</td>
            </tr>
            <tr>
                <th>NSSF</th>
                <td>{{ $slip['nssf'] }}</td>
            </tr>
            <tr>
                <th>PAYE</th>
                <td>{{ $slip['paye'] }}</td>
            </tr>
            <tr>
                <th>NHIF</th>
                <td>{{ $slip['nhif'] }}</td>
            </tr>
            <tr>
                <th><strong>Net Salary</strong></th>
                <td><strong>{{ $slip['net_salary'] }}</strong></td>
            </tr>
        </table>

        <div class="footer">
            Generated on: {{ \Carbon\Carbon::now()->format('d M Y') }}
        </div>
    </div>

</body>
</html>
