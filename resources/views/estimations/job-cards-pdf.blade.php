
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Job Sheet') }}</title>

    <style>
        @page {
            margin: 30px 35px 45px 35px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .document-header {
            border-bottom: 2px solid #4c83b5;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: normal;
            letter-spacing: 2px;
            color: #315f86;
            margin: 0 0 8px 0;
        }

        .document-subtitle {
            text-align: center;
            color: #777;
            font-size: 9px;
            margin-bottom: 8px;
        }

        .section-title {
            background: #eaf1f7;
            color: #315f86;
            padding: 7px 9px;
            font-size: 10px;
            font-weight: bold;
            border-left: 3px solid #4c83b5;
            margin: 14px 0 8px 0;
        }

        .top-table td {
            vertical-align: top;
            padding: 5px 8px;
        }

        .customer-name {
            font-size: 13px;
            color: #315f86;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .customer-info {
            color: #666;
            font-size: 10px;
            line-height: 1.9;
        }

        .reference-table td {
            padding: 3px 2px;
            vertical-align: top;
        }

        .reference-label {
            color: #777;
            width: 95px;
        }

        .reference-value {
            color: #333;
            font-weight: bold;
        }

        .job-reference {
            color: #315f86;
            font-size: 17px;
            font-weight: bold;
            text-align: right;
        }

        .status-area {
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 7px;
            font-size: 9px;
        }

        .status-area td {
            padding: 4px 0;
        }

        input[type="checkbox"] {
            width: 11px;
            height: 11px;
            vertical-align: middle;
        }

        .info-table {
            margin-top: 5px;
        }

        .info-table th {
            background: #eaf1f7;
            color: #315f86;
            font-weight: normal;
            text-align: left;
            padding: 8px 6px;
            border: 1px solid #d8e0e7;
        }

        .info-table td {
            padding: 9px 6px;
            border: 1px solid #d8e0e7;
            text-align: left;
        }

        .notes-container {
            margin-top: 10px;
            width: 100%;
        }

        .notes-image {
            width: 37%;
            vertical-align: top;
            padding-right: 12px;
        }

        .notes-image img {
            width: 100%;
            height: 115px;
        }

        .notes-content {
            width: 63%;
            vertical-align: top;
        }

        .notes-box {
            border: 1px solid #d8e0e7;
            height: 115px;
            padding: 8px;
            color: #777;
            font-size: 10px;
        }

        .notes-heading {
            color: #315f86;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .work-table {
            margin-top: 5px;
            page-break-inside: auto;
        }

        .work-table thead {
            display: table-header-group;
        }

        .work-table th {
            background: #eaf1f7;
            color: #315f86;
            font-weight: bold;
            text-align: left;
            padding: 8px 7px;
            border: 1px solid #d8e0e7;
        }

        .work-table td {
            padding: 9px 7px;
            border: 1px solid #e0e4e8;
            vertical-align: middle;
        }

        .work-table tr {
            page-break-inside: avoid;
        }

        .center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .check-cell {
            text-align: center;
        }

        .check-line {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1px solid #777;
        }

        .signature-section {
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .signature-section td {
            width: 50%;
            padding: 10px 20px;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #777;
            margin-top: 35px;
            padding-top: 6px;
            text-align: center;
            color: #666;
        }

        .footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            border-top: 1px solid #d8e0e7;
            padding-top: 5px;
            font-size: 8px;
            color: #888;
        }

        .footer-right {
            text-align: right;
        }

        .muted {
            color: #888;
        }

        .spacer {
            height: 5px;
        }
        /* Compact table styling */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9px;
}

.info-table th,
.work-table th {
    padding: 5px 6px;
    font-size: 9px;
    font-weight: normal;
    line-height: 1.2;
}

.info-table td,
.work-table td {
    padding: 4px 6px;
    font-size: 9px;
    line-height: 1.2;
    vertical-align: middle;
}

.top-table td {
    padding: 3px 5px;
    font-size: 9px;
}

.reference-table td {
    padding: 2px 3px;
    font-size: 9px;
}

.status-area td {
    padding: 2px 0;
    font-size: 9px;
}

/* Smaller checkboxes */
input[type="checkbox"] {
    width: 9px;
    height: 9px;
    margin: 0 2px;
}

.check-line {
    width: 10px;
    height: 10px;
}

/* Compact section headings */
.section-title {
    padding: 5px 7px;
    margin: 9px 0 5px 0;
    font-size: 9px;
}

/* Compact customer information */
.customer-name {
    font-size: 11px;
    margin-bottom: 3px;
}

.customer-info {
    font-size: 9px;
    line-height: 1.5;
}

/* Reduce document spacing */
.document-header {
    padding-bottom: 6px;
    margin-bottom: 8px;
}

.document-title {
    font-size: 17px;
    margin-bottom: 4px;
}

.document-subtitle {
    font-size: 8px;
    margin-bottom: 5px;
}

.notes-box {
    height: 95px;
    padding: 6px;
    font-size: 9px;
}

.notes-image img {
    height: 95px;
}

.signature-section {
    margin-top: 20px;
}
    </style>
</head>

<body>

@php
    $sale = $sales[0];
    $client = $client_name[0] ?? null;

    $accountNumber = '';

    if ($sale->account_no < 10) {
        $accountNumber = $sale->account_prefix . '00' . $sale->account_no;
    } elseif ($sale->account_no < 100) {
        $accountNumber = $sale->account_prefix . '0' . $sale->account_no;
    } else {
        $accountNumber = $sale->account_prefix . $sale->account_no;
    }
@endphp

{{-- ================= HEADER ================= --}}

<div class="document-header">

    <h1 class="document-title">JOB SHEET</h1>

    <div class="document-subtitle">
        VEHICLE SERVICE AND REPAIR WORK ORDER
    </div>

</div>

{{-- ================= CUSTOMER DETAILS ================= --}}

<div class="section-title">
    CUSTOMER & JOB INFORMATION
</div>

<table class="top-table">

    <tr>

        <td width="43%">

            <div class="customer-name">
                {{ $client->client_name ?? $sale->customer_name }}
            </div>

            <div class="customer-info">
                {{ $client->address ?? '' }}<br>
                {{ $client->place ?? '' }}
            </div>

        </td>

        <td width="32%">

            <table class="reference-table">

                <tr>
                    <td class="reference-label">Account No.</td>
                    <td class="reference-value">{{ $accountNumber }}</td>
                </tr>

                <tr>
                    <td class="reference-label">Order Ref.</td>
                    <td class="reference-value">{{ $sale->reference }}</td>
                </tr>

                <tr>
                    <td class="reference-label">Receive Date</td>
                    <td class="reference-value">{{ $sale->created_date }}</td>
                </tr>

                <tr>
                    <td class="reference-label">Due Date</td>
                    <td class="reference-value">{{ $sale->created_date }}</td>
                </tr>

            </table>

        </td>

        <td width="25%">

            <div class="job-reference">
                JC{{ $sale->job_card_reference }}
            </div>

            <div class="status-area">

                <table>
                    <tr>
                        <td>
                            <input type="checkbox">
                            In Progress
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox">
                            Completed
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox">
                            Pending
                        </td>
                    </tr>
                </table>

            </div>

        </td>

    </tr>

</table>

{{-- ================= VEHICLE DETAILS ================= --}}

<div class="section-title">
    VEHICLE INFORMATION
</div>

<table class="info-table">

    <thead>
        <tr>
            <th>Vehicle Reg. No.</th>
            <th>Make</th>
            <th>Model</th>
            <th>Chassis No.</th>
            <th>Mileage</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>{{ $sale->vehicle_reg }}</td>
            <td>{{ $sale->emake }}</td>
            <td>{{ $sale->emodel }}</td>
            <td>{{ $sale->chassis }}</td>
            <td>{{ $sale->milleage }}</td>
        </tr>
    </tbody>

</table>

{{-- ================= VEHICLE NOTES ================= --}}

<div class="section-title">
    VEHICLE CONDITION & CUSTOMER NOTES
</div>

<table class="notes-container">

    <tr>

        <td class="notes-image">

            @if(file_exists(public_path('assets/images/vehicle.jpg')))
                <img
                    src="{{ public_path('assets/images/vehicle.jpg') }}"
                    alt="Vehicle inspection"
                >
            @endif

        </td>

        <td class="notes-content">

            <div class="notes-box">

                <div class="notes-heading">
                    Notes / Reported Problems
                </div>

            </div>

        </td>

    </tr>

</table>

{{-- ================= LABOUR ================= --}}

<div class="section-title">
    LABOUR & SERVICE DETAILS
</div>

<table class="work-table">

    <thead>
        <tr>
            <th width="55%">Description of Work</th>
            <th width="20%">Technician</th>
            <th width="12%" class="center">Qty</th>
            <th width="13%" class="center">Done</th>
        </tr>
    </thead>

    <tbody>

        @forelse($labours as $key => $l)

            <tr>

                <td>{{ $l->labour_name }}</td>

                <td></td>

                <td class="center"></td>

                <td class="check-cell">
                    <span class="check-line"></span>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="4" class="center muted">
                    No labour or service recorded.
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

{{-- ================= SPARE PARTS ================= --}}

<div class="section-title">
    SPARE PARTS & MATERIALS
</div>

<table class="work-table">

    <thead>
        <tr>
            <th width="50%">Part Description</th>
            <th width="22%">Part No.</th>
            <th width="13%" class="center">Qty</th>
            <th width="15%" class="center">Done</th>
        </tr>
    </thead>

    <tbody>

        @forelse($sales as $key => $product)

            <tr>

                <td>{{ $product->item_name }}</td>

                <td></td>

                <td class="center">
                    {{ $product->qty }}
                </td>

                <td class="check-cell">
                    <span class="check-line"></span>
                </td>

            </tr>

        @empty

            <tr>
                <td colspan="4" class="center muted">
                    No spare parts recorded.
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

{{-- ================= TECHNICIAN SIGNATURE ================= --}}

<div class="section-title">
    JOB COMPLETION & APPROVAL
</div>

<table class="signature-section">

    <tr>

        <td>
            <div class="signature-line">
                Technician Name & Signature
            </div>

            <br>

            Date: ------------------------------------------
        </td>

       

    </tr>

</table>

{{-- ================= FOOTER ================= --}}

<div class="footer">

    <table>
        <tr>
            <td width="50%">
                JOB SHEET | JC{{ $sale->job_card_reference }}
            </td>

            
        </tr>
    </table>

</div>

</body>
</html>
