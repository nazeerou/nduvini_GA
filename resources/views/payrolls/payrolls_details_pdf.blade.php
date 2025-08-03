<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Report - {{ $month }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .company-info {
            font-size: 20px;
            line-height: 1.5;
        }

        .title {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color:rgb(28, 145, 240);
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f7ff;
        }

        tfoot th {
            background-color:rgb(122, 166, 232);
            font-weight: bold;
        }

        .signature-section {
            margin-top: 50px;
            text-align: center;
        }

        .signature-label {
            font-size: 13px;
            margin-bottom: 40px;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin: 0 auto;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 11px;
            color: #555;
        }

        .watermark {
            position: fixed;
            top: 40%;
            left: 20%;
            width: 60%;
            text-align: center;
            opacity: 0.08;
            font-size: 80px;
            transform: rotate(-30deg);
            color: #000;
            z-index: -1000;
            pointer-events: none;
        }

        .logo-watermark {
            position: fixed;
            top: 20%;
            left: 35%;
            width: 30%;
            opacity: 0.05;
            z-index: -2;
        }

        .logo-watermark img {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="watermark">CONFIDENTIAL</div>

<div class="logo-watermark">
    <img src="{{ url('/attachments/logo_nduvini.jpeg') }}" alt="User profile" class="img-circle img-thumbnail img-responsive profile-pic">
</div>

<!-- Header -->
<div class="header">
    <div class="company-info">
        <strong>{{ strtoupper($settings[0]->business_name) }}</strong><br>
    </div>
    <div class="logo">
    <!-- <img src="{{ url('/attachments/logo_nduvini.jpeg') }}" alt="" class="img-circle img-thumbnail img-responsive profile-pic"> -->
</div>
    <div class="title">Salary Report for {{ $month }}</div>
</div>

@php
    $total_nssf = 0;
    $total_paye = 0;
    $total_nhif = 0;
    $total_net = 0;
@endphp

<!-- Salary Table -->
<table>
    <thead>
        <tr>
            <th>S/N</th>
            <th>Employee Name</th>
            <th>Basic Salary</th>
            <th>Allowance</th>
            <th>NSSF</th>
            <th>PAYE</th>
            <th>NHIF</th>
            <th>Net Salary</th>
        </tr>
    </thead>
    <tbody>
        @foreach($slips as $index => $slip)
            @php
                $nssf = floatval($slip['nssf'] ?? 0);
                $paye = floatval($slip['paye'] ?? 0);
                $nhif = floatval($slip['nhif'] ?? 0);
                $net = floatval($slip['net_salary'] ?? 0);

                $total_nssf += $nssf;
                $total_paye += $paye;
                $total_nhif += $nhif;
                $total_net += $net;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ strtoupper($slip['employee_name']) }}</td>
                <td>{{ number_format($slip['basic_salary']) }}</td>
                <td>{{ number_format($slip['allowance']) }}</td>
                <td>{{ number_format($nssf) }}</td>
                <td>{{ number_format($paye) }}</td>
                <td>{{ number_format($nhif) }}</td>
                <td>{{ number_format($net) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" style="text-align: right;">Total</th>
            <th>{{ number_format($total_nssf, 2) }}</th>
            <th>{{ number_format($total_paye, 2) }}</th>
            <th>{{ number_format($total_nhif, 2) }}</th>
            <th>{{ number_format($total_net, 2) }}</th>
        </tr>
    </tfoot>
</table>

<!-- Signature Section -->
<div class="signature-section">
    <div class="signature-label">Authorized By</div>
    <div class="signature-line"></div>
    <div class="signature-label">Signature</div>
</div>

<!-- Footer -->
<div class="footer">
    Generated on: {{ now()->format('F d, Y h:i A') }}
</div>

</body>
</html>
