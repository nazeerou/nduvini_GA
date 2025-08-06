<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Report - {{ $month }}</title>
    <style>
         @page {
            margin: 14px;
        }
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
        }

        .header {
            text-align: center;
            /* margin-bottom: 10px; */
        }

        .company-info {
            font-size: 17px;
            line-height: 1.5;
        }

        .title {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 5px;
            /* text-align: center; */
        }

        th {
            /* background-color:rgb(28, 145, 240); */
            /* color: white; */
        }

        tbody tr:nth-child(even) {
            /* background-color: #f9f9f9; */
        }

        tbody tr:hover {
            /* background-color: #f1f7ff; */
        }

        tfoot th {
            /* background-color:rgb(122, 166, 232); */
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
            /* margin-top: 40px; */
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
        .branch {
            text-align: center;
            font-size: 15px;
            padding-top: 10px;
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
<div class="branch"> 
@if(Auth::user()->branch_id == 1)
    HEAD OFFICE - Dar es Salaam 
@elseif (Auth::user()->branch_id == 2)
    TABORA  BRANCH
@elseif (Auth::user()->branch_id ==3)
    DODOMA BRANCH
@else
@endif
</div>

@php
    $total_basic_salary = 0;
    $total_nssf = 0;
    $total_wcf = 0;
    $total_nhif = 0;
    $total_tuico = 0;
    $total_loan = 0;
    $total_advance = 0;
    $total_allowance = 0;
    $total_net = 0;
@endphp

<!-- Salary Table -->
<table>
    <thead>
        <tr>
            <th>S/N</th>
            <th>Full Name</th>
            <th>Basic Salary</th>
            <th>Allowance</th>
            <th>Gross Salary </th>
            <th>Advance Pay</th>
            <th>NSSF</th>
            <th>NHIF</th>
            <th>WCF</th>
            <th>TUICO</th>
            <th>LOAN </th>
            <th>Total Deduction</th>
            <th>Net Salary</th>
        </tr>
    </thead>
    <tbody>
        @foreach($slips as $index => $slip)
            @php
                $nssf = floatval($slip['nssf'] ?? 0);
                $wcf = floatval($slip['wcf'] ?? 0);
                $tuico = floatval($slip['tuico'] ?? 0);
                $advance_pay = floatval($slip['salary_advance'] ?? 0);
                $loan_repayment = floatval($slip['loan'] ?? 0);
                $nhif = floatval($slip['nhif'] ?? 0);
                $net = floatval($slip['net_salary'] ?? 0);
                $basic_salary = floatval($slip['basic_salary'] ?? 0);
                $allowance = $slip['allowance'] ?? 0;
                $total_basic_salary += $basic_salary;
                $total_nssf += $nssf;
                $total_wcf += $wcf;
                $total_nhif += $nhif;
                $total_allowance += $allowance;
                $total_advance += $advance_pay;
                $total_loan += $loan_repayment;
                $total_tuico += $tuico;
                $total_net += $net;
            @endphp
            <tr>
            <td>{{ $index + 1 }}</td>
            <td style="text-align: left;">{{ strtoupper($slip['employee_name']) }}</td>
            <td>{{ number_format($slip['basic_salary'] ?? 0) }}</td>
            <td>{{ number_format($slip['allowance'] ?? 0) }}</td>
            <td>{{ number_format(($slip['basic_salary'] ?? 0) + ($slip['allowance'] ?? 0)) }}</td>
            <td>{{ number_format($advance_pay ?? 0) }}</td>
            <td>{{ number_format($nssf ?? 0) }}</td>
            <td>{{ number_format($nhif ?? 0) }}</td>
            <td>{{ number_format($wcf ?? 0) }}</td>
            <td>{{ number_format($tuico ?? 0) }}</td>
            <td>{{ number_format($loan ?? 0) }}</td>
            <td>{{ number_format(($advance_pay ?? 0) + ($nssf ?? 0) + ($wcf ?? 0) + ($tuico ?? 0) + ($loan ?? 0) + ($nhif ?? 0)) }}</td>
            <td>{{ number_format($net ?? 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align: right;">Total</th>
            <th>{{ number_format($total_basic_salary) }}</th>
            <th>{{ number_format($total_allowance) }}</th>
            <th>{{ number_format($total_basic_salary + $total_allowance) }}</th>
            <th>{{ number_format($total_advance) }}</th>
            <th>{{ number_format($total_nssf) }}</th>
            <th>{{ number_format($total_nhif) }}</th>
            <th>{{ number_format($total_wcf) }}</th>
            <th>{{ number_format($total_tuico) }}</th>
            <th>{{ number_format($total_loan) }}</th>
            <th>{{ number_format($total_advance + $total_loan + $total_nhif + $total_nssf + $total_wcf + $total_tuico) }}</th>
            <th>{{ number_format($total_net) }}</th>
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
