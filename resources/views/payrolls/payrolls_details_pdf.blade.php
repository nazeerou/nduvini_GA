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

    

        .signature-label {
            font-size: 13px;
            margin-bottom: 40px;
        }

        .signature-line {
            width: 100px;
             border-top: 1px dashed #999;
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
        /* =====================================================
   SIGNATURE SECTIONS - LEFT AND RIGHT ALIGNMENT
===================================================== */

.signature-wrapper {
        width: 100%;
        display: table;
        table-layout: fixed;
        margin-top: 45px;
        margin-bottom: 35px;
    }

    .signature-section-1,
    .signature-section-2 {
        display: table-cell;
        width: 50%;
        vertical-align: top;
    }

    .signature-section-1 {
        text-align: left;
        padding-left: 20px;
    }

    .signature-section-2 {
        text-align: right;
        padding-right: 20px;
    }

    .signature-heading {
        font-size: 12px;
        margin-bottom: 8px;
    }

    .signature-image {
        height: 55px;
        width: 120px;
        object-fit: contain;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .signature-line {
        width: 100px;
         border-top: 1px dashed #999;
        margin-top: 5px;
    }

    .signature-section-1 .signature-line {
        margin-left: 0;
    }

    .signature-section-2 .signature-line {
        margin-left: auto;
        margin-right: 0;
    }

    .signature-name {
        font-size: 11px;
        margin-top: 8px;
        margin-bottom: 4px;
    }

    .signature-role {
        font-size: 10px;
        color: #555;
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
    $total_nhif = 0;
    $total_tuico = 0;
    $total_loan = 0;
    $total_advance = 0;
    $total_allowance = 0;
    $total_paye = 0;
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
            <th>LOAN </th>
            <th>NSSF</th>
            <th>NHIF</th>
            <th>TUICO</th>
            <th>PAYE </th>
            <th>Total Deduction</th>
            <th>Net Salary</th>
        </tr>
    </thead>
    <tbody>
        @foreach($slips as $index => $slip)
            @php
                $nssf = floatval($slip['nssf'] ?? 0);
                $tuico = floatval($slip['tuico'] ?? 0);
                $advance_pay = floatval($slip['salary_advance'] ?? 0);
                $loan_repayment = floatval($slip['loan'] ?? 0);
                $nhif = floatval($slip['nhif'] ?? 0);
                $net = floatval($slip['net_salary'] ?? 0);
                $basic_salary = floatval($slip['basic_salary'] ?? 0);
                $allowance = $slip['allowance'] ?? 0;
                $paye = floatval($slip['paye'] ?? 0);
                $total_basic_salary += $basic_salary;
                $total_nssf += $nssf;
                $total_nhif += $nhif;
                $total_allowance += $allowance;
                $total_advance += $advance_pay;
                $total_loan += $loan_repayment;
                $total_tuico += $tuico;
                $total_paye += $paye;
                $total_net += $net;
            @endphp
            <tr>
            <td>{{ $index + 1 }}</td>
            <td style="text-align: left;">{{ strtoupper($slip['employee_name']) }}</td>
            <td>{{ number_format($slip['basic_salary'] ?? 0) }}</td>
            <td>{{ number_format($slip['allowance'] ?? 0) }}</td>
            <td>{{ number_format(($slip['basic_salary'] ?? 0) + ($slip['allowance'] ?? 0)) }}</td>
            <td>{{ number_format($advance_pay ?? 0) }}</td>
            <td>{{ number_format($slip['loan'] ?? 0) }}</td>
            <td>{{ number_format($slip['nssf'] ?? 0) }}</td>
            <td>{{ number_format($slip['nhif'] ?? 0) }}</td>
            <td>{{ number_format($slip['tuico'] ?? 0) }}</td>
            <td>{{ number_format($slip['paye'] ?? 0) }}</td>
            <td>{{ number_format(($advance_pay ?? 0) + ($nssf ?? 0) + ($nhif ?? 0) + ($tuico ?? 0) + ($loan ?? 0) +  ($paye ?? 0)) }}</td>
            <td>{{ number_format($net ?? 0) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="padding: 0 0 0 4px;">Total</th>
            <th>{{ number_format($total_basic_salary) }}</th>
            <th>{{ number_format($total_allowance) }}</th>
            <th>{{ number_format($total_basic_salary + $total_allowance) }}</th>
            <th>{{ number_format($total_advance) }}</th>
            <th>{{ number_format($total_loan) }}</th>
            <th>{{ number_format($total_nssf) }}</th>
            <th>{{ number_format($total_nhif) }}</th>
            <th>{{ number_format($total_tuico) }}</th>
            <th>{{ number_format($total_paye) }}</th>
            <th>{{ number_format($total_advance + $total_loan + $total_nhif + $total_nssf + $total_tuico + $total_paye) }}</th>
            <th>{{ number_format($total_net) }}</th>
        </tr>
    </tfoot>
</table>


<!-- Signature Sections -->
<!-- =====================================================
     PAYROLL SIGNATURES
===================================================== -->

<div class="signature-wrapper">

    <!-- CREATED BY - LEFT -->
    <div class="signature-section-1">

        <div class="signature-heading" style="padding-left: 20px;">
            <strong>Created By</strong>
        </div>

        @if(!empty($creator['signature']))
            <img
                src="{{ $creator['signature'] }}"
                class="signature-image"
                alt="Creator Signature"
            >
        @else
            <div style="height: 65px;"></div>
        @endif

        <div class="signature-line"></div>

        <div class="signature-name">
            <strong>{{ strtoupper($creator['name']) }}</strong>
        </div>

        <div class="signature-role" style="padding-left: 20px;">
            {{ $creator['role'] }}
        </div>

    </div>


    <!-- APPROVED BY - RIGHT -->
    <div class="signature-section-2">

        <div class="signature-heading" style="padding-right: 20px;">
            <strong>Approved By</strong>
        </div>

        @if(!empty($approver['signature']))
            <img
                src="{{ $approver['signature'] }}"
                class="signature-image"
                alt="Approver Signature"
            >
        @else
            <div style="height: 65px;"></div>
        @endif

        <div class="signature-line"></div>

        <div class="signature-name">
            <strong>{{ strtoupper($approver['name']) }}</strong>
        </div>

        <div class="signature-role">
            {{ $approver['role'] }}
        </div>

    </div>

</div>

<!-- Footer -->
<div class="footer">
   Generated on: {{ now()->addHours(3)->format('F d, Y h:i A') }} 
</div>

</body>
</html>
